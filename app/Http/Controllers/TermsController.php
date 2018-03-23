<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TermRequest;
use App\Term;
use App\Services\Terms;
use App\Services\Users;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;

class TermsController extends Controller
{
    
    public function __construct(Terms $terms , Users $users)
    {
        $this->terms=$terms;
        $this->users=$users;
    }

    function canEdit()
    {
        return $this->currentUserIsDev();
    }

    function yearOption()
    {
        $thisYear=Helper::toTaipeiYear(Carbon::today()->year);
        $min=$thisYear - 5;
        $max=$thisYear + 1;
        $options=[];
        for ($i = $max; $i >= $min; $i--)
        {
            $item=[ 'text' => $i ,  'value' => $i ];
            array_push($options,  $item);
        }

        return $options;
    }
    function orderOption()
    {
        $options=[];
        for ($i = 1; $i <= 4; $i++)
        {
            $item=[ 'text' => $i ,  'value' => $i ];
            array_push($options,  $item);
        }

        return $options;
    }
    
    public function index()
    {
        $request=request();
        $active=true;
        if($request->active)  $active=Helper::isTrue($request->active);
      
     
        $terms=$this->terms->fetchTerms($active);
       
        $terms=$this->terms->getOrdered($terms);
       
      
        $pageList = new PagedList($terms);

        if($this->isAjaxRequest()){
            return response() ->json($pageList);
        }
       

        $menus=$this->adminMenus('MainSettings');
       
        return view('terms.index')->with([
            'title' => '學期設定管理',
            'menus' => $menus,
            'canEdit' => $this->canEdit(),
            'list' =>  $pageList
        ]);
    }

    public function create()
    {
        $thisYear=Helper::toTaipeiYear(Carbon::today()->year);

        $form=[
            'term' => Term::init($thisYear),
            'yearOptions' => $this->yearOption(),
            'orderOptions' => $this->orderOption(),
        ];

        return response() ->json($form);
      
    }

    public function store(TermRequest $request)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

       
        $values=$request->getValues();
       
        $errors=$this->validateTerm($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy'] = $current_user->id;
        $term = Term::create($values);
        return response() ->json($term);
    }

    public function edit($id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $term = Term::findOrFail($id);
       
        $form=[
            'term' => $term,
            'yearOptions' => $this->yearOption(),
            'orderOptions' => $this->orderOption(),
        ];

        return response() ->json($form);
       
        
    }

    function validateTerm($values)
    {
        $errors=[];

        $id=0;        
        if(array_key_exists ('id' ,$values)) $id=(int)$values['id'];        

        $number=$values['number'];
        $exist=$this->terms->getTermByNumber($number);
        if($exist && $exist->id!=$id){
            $errors['term.order'] = ['年度順序重複了'];
        }

        $closeDate=new Carbon($values['closeDate']);
        $openDate=new Carbon($values['openDate']);
        $birdDate=new Carbon($values['birdDate']);
        if($closeDate<=$openDate){
            $errors['term.openDate'] = ['日期順序錯誤'];
        }

        if($birdDate<=$openDate){
            $errors['term.openDate'] = ['日期順序錯誤'];
        }

        if($closeDate < $birdDate){
            $errors['term.closeDate'] = ['日期順序錯誤'];
        }

        return $errors;
    }

    public function update(TermRequest $request, $id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $term = Term::findOrFail($id);
       
        $values=$request->getValues();

        $errors=$this->validateTerm($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy'] = $current_user->id;
        
        $term->update($values);
        return response() ->json();
    }

    public function importCategories($file,$current_user)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();

        $categoryList=$excel->toArray()[0];
        for($i = 1; $i < count($categoryList); ++$i) {
            $row=$categoryList[$i];
            
            $name=trim($row['name']);
            if(!$name){
               continue;
            }

            $exist_category=null;

            $code=trim($row['code']);
            $top=(int)trim($row['top']);  
            $public=false;
            if($top>0){
                $public=true;
                $exist_category=$this->topCategories()
                ->where('name',$name)->first();
            }else{
                $public=false;
                if(!$code){
                    $err_msg .= '必須填寫分類代碼';
                    continue;
                }
                $exist_category=$this->normalCategories()
                              ->where('code',$code)->first();
            }
             
                        
            $order=(int)trim($row['order']);
            $active=true;
            if($order>=0){
                $active=true;
            }else{
                $active=false;
            }
           
            $icon=trim($row['icon']);
            $updated_by=$current_user->id;

            $values=[
                'name' => $name,
                'code' => $code,
                'order' => $order,
                'icon' => $icon,
                'public' => $public,
                'active' => $active,
                'updated_by' => $updated_by
            ];

            if($exist_category){                                    
               
                $exist_category->update($values);
            
            }else{                
                $category=Category::create($values);
            
            }
        }  //end for  

        return $err_msg;
    }
}
