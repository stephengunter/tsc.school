<?php

namespace App\Services;
use App\User;
use App\Role;
use App\Profile;
use App\Center;
use App\Teacher;
use App\Wage;
use App\Account;
use App\ContactInfo;
use App\Address;
use App\District;
use App\Services\Users;
use App\Services\Wages;
use App\Services\Import;
use DB;
use Excel;

class Teachers 
{
    use Import;

    public function __construct(Users $users, Wages $wages)
    {
        $this->users=$users;
        $this->wages=$wages;
        $this->with=['wage', 'user.accounts' ,'user.roles','user.profile','user.contactInfoes.address.district.city'];
    }
    public function getAll()
    {
        return Teacher::with($this->with)->where('removed',false); 
    }
    public function getById($id)
    {   
        return Teacher::with($this->with)->find($id);
    }
    public function getByIds(array $ids)
    {   
        return Teacher::with($this->with)->whereIn('userId',$ids);
    }
    public function findBySID($sid)
	{
        $user=$this->users->findBySID($sid);
        return $user->teacher; 
	
	}

	public function getBySIDs(array $sids)
	{
		$users=$this->users->getBySIDs($sids);
		
		$userIds=$users->pluck('id')->toArray();
		
		return $this->getByIds($userIds);
       
	}

    public function createTeacher(User $user,Teacher $teacher,array $centerIds=[])
    {
      
        $user->teacher()->save($teacher);

        $teacher->userId=$user->id;
        if($centerIds) $teacher->centers()->sync($centerIds);
       
        return $teacher;
        
    }

    public function updateTeacher(Teacher $teacher,array $values)
    {
        $wage=Wage::find($values['wageId']);
        if(!$wage->isSpecial())   $values['pay'] = 0;
      
        $teacher->update($values);
        
    }

    public function reviewOK(array $ids, $reviewedBy)
    {
        $teachers=Teacher::where('removed',false)->whereIn('userId',$ids)->get();
        foreach($teachers as $teacher){
            $teacher->reviewed=true;
            $teacher->reviewedBy=$reviewedBy;
            $teacher->updatedBy=$reviewedBy;
            $teacher->save();

            $teacher->user->addRole(Role::teacherRoleName());
        } 
    }

    public function  updateReview($id,bool $reviewed,int $reviewedBy)
    {
        $teacher=$this->getById($id);
        $teacher->reviewed=$reviewed;
        $teacher->updatedBy=$reviewedBy;

        if($reviewed){
            $teacher->reviewedBy=$reviewedBy;
            $teacher->save();

            $teacher->user->addRole(Role::teacherRoleName());
        }else{
            $teacher->reviewedBy='';
            $teacher->save();

            $teacher->user->removeRole(Role::teacherRoleName());
        }
    }


    public function deleteTeacher(Teacher $teacher,$updatedBy)
    {
        $teacher->removeRole();

        $teacher->removed=true;
        $teacher->updatedBy=$updatedBy;
        $teacher->save();
        
    }
    
    public function fetchTeachers(Center $center = null, bool $reviewed=true,  $keyword = '')
    {
        $teachers=null;
        if($keyword) $teachers=$this->getByKeyword($keyword);
        else $teachers=$this->getAll();

        if($center){
            $teachers=$teachers->whereHas('centers', function($q) use ($center)
            {
                $q->where('id',$center->id );
            });
          
        }

        return $teachers->where('reviewed',$reviewed);
    }

    public function  getByKeyword($keyword)
    {
        $byPhones=User::where('phone', 'LIKE', '%' .$keyword .'%')->pluck('id')->toArray();
        $byFullnames=Profile::where('fullname', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
        $bySIDs=Profile::where('sid', 'LIKE', '%' .$keyword .'%')->pluck('userId')->toArray();
       
        $userIds=array_unique(array_merge($byPhones,$byFullnames,$bySIDs));

        return $this->getAll()->whereIn('userId' , $userIds );
       
    }
    public function  getByCenter(Center $center)
    {   
        return $this->fetchTeachers($center);
    } 
    public function  options(Center $center)
    {
       
        $teachers=$this->getByCenter($center)->get();
        return $teachers->map(function ($teacher) {
            return $teacher->toOption();
        })->all();
    }

    public function getTeacherBySID($sid)
    {
        $profile=Profile::where('sid',$sid)->first();
        if(!$profile) return null;

        return Teacher::find($profile->userId);
    }

    
    
    public function importTeachers($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(20);
            $reader->limitRows(100);
        })->get();

        $teacherList=$excel->toArray()[0];
       
        for($i = 1; $i < count($teacherList); ++$i) {
            $row=$teacherList[$i];

            $fullname=trim($row['fullname']);
            if(!$fullname) continue;

            $wage=null;
            $wageName=trim($row['wage']);
            $pay=trim($row['pay']);

            if(!$wageName){
                $err_msg .= '薪酬標準不可空白' . ',';
                continue;
            }else{
                $wage=$this->wages->getByName($wageName);
                if(!$wage){
                    $err_msg .= '薪酬標準錯誤' . ',';
                    continue;
                }
            }

            // $accountNumber=trim($row['account']);
            // if(!$accountNumber){
            //     $err_msg .= '銀行帳號不可空白' . ',';
            //     continue;
            // }

            
            $center_codes=trim($row['centers']);
            if(!$center_codes){
                $err_msg .= '中心代碼不可空白' . ',';
                continue;
            }
            
           
            $education=trim($row['education']);
            $specialty=trim($row['specialty']);
            $jobtitle=trim($row['jobtitle']);
            $ps=trim($row['ps']);

            $experiences='';               
            $array_experiences = explode(',', trim($row['experiences']));
            for($j = 0; $j < count($array_experiences); ++$j){
                $experiences .= $array_experiences[$j] . '<br>';
            }

            $teacherValues=[
                'wageId' => $wage->id,
                'education' => $education,
                'specialty' => $specialty,
                'experiences' => $experiences,
                'jobtitle' => $jobtitle,
                'ps' => $ps,
                'updatedBy' => $updatedBy,
                'removed' => false,
            ];

            if($wage->isSpecial()){
                $pay=floatval($pay);
               
                if(!$pay){
                    $err_msg .= '特殊講師鐘點費' . ',';
                    continue;
                }else{
                    $teacherValues['pay'] = $pay;
                } 
                
            }


            $getCenters=$this->getCenters($row);
            if(array_key_exists('err',$getCenters)){
                $err_msg .= $getCenters['err'] . ',';
                continue;
            }
            $centers=$getCenters['centers'];
            $centerIds=array_map(function($item){
                return $item->id;
            }, $centers);
          

            $userDatas=$this->getImportUserDatas($row,$updatedBy);
            if(array_key_exists('err',$userDatas)){
                $err_msg .= $userDatas['err'] . ',';
                continue;
            }

            $userValues=$userDatas['userValues'];
            $profileValues=$userDatas['profileValues'];
            $contactInfoValues=$userDatas['contactInfoValues'];
            $addressValues=$userDatas['addressValues'];
            $identities=$userDatas['identities'];

            $sid=$profileValues['sid'];
            $existTeacher = $this->getTeacherBySID($sid);
            if($existTeacher)
            {
                foreach($centers as $center){
                    $existTeacher->addToCenter($center);
                }
               
                continue;
            }
            
            $user= $this->users->findBySID($sid);

            if(!$user)
            {
                $user=$this->users->createUser(
                    new User($userValues),
                    new Profile($profileValues)
                );
               
            }

            foreach($identities as $identity){
                $user->addIdentity($identity->id);
            }

            $contactInfo=new ContactInfo($contactInfoValues);
            $address=new Address($addressValues);
            

            $this->users->setContactInfo($user,$contactInfo,$address);
    
            $teacher = new Teacher($teacherValues);
            

            $teacher=$this->createTeacher($user,$teacher,$centerIds);
            
           
        }  //end for  

        
        

        

        return $err_msg;

       



   }
    
}