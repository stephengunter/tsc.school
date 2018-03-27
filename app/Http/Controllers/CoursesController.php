<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\CourseInfoRequest;

use App\Term;
use App\Course;
use App\User;
use App\Profile;
use App\Center;
use App\Category;
use App\Services\Terms;
use App\Services\Courses;
use App\Services\Centers;
use App\Services\Categories;
use App\Services\Teachers;
use App\Services\TeacherGroups;
use App\Services\CourseInfoes;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class CoursesController extends Controller
{
    
    public function __construct(Courses $courses, Teachers $teachers,TeacherGroups $teacherGroups,
        Terms $terms,Centers $centers,Categories $categories,CourseInfoes $courseInfoes)
    {
        $this->courses=$courses;
        $this->teachers=$teachers;
        $this->teacherGroups=$teacherGroups;
        $this->terms=$terms;
        $this->centers=$centers;
        $this->categories=$categories;
        $this->courseInfoes=$courseInfoes;
    }

    function canEdit($course)
    {
        if($this->currentUserIsDev()) return true;
        if(!count($course->centers)) return true;
      
        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect($course->centers);

        if(count($intersect)) return true;
        return false;

    }

    function canReview(Course $course)
    {
        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect([$course->center]);

        
        if(count($intersect)) return true;
        return false;

    }
    function canReviewCenter(Center $center=null)
    {
        if(!$center) return false;

        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect([$center]);

        if(count($intersect)) return true;
        return false;
    }

    function canDelete($course)
    {
        return $this->canReview($course);
    }


    function canImport()
    {
        return $this->currentUserIsDev();
    }

    function canEditCenters()
    {
        if($this->currentUserIsDev()) return true;
        return $this->currentUser()->admin->isHeadCenterCourse();
    }

 
    function setTeachers($course)
    {
        $teachers=$course->teachers;
        if($course->teacherGroup) $course->teacherNames=$course->teacherGroup->name;
        if(count($teachers)){
            $names=join(',',$teachers->pluck('user.profile.fullname')->toArray());
            if($course->teacherNames) $course->teacherNames .= $names;
            else $course->teacherNames=$names;
        }

    }

    function setCategories($course)
    {
        $categories=$course->categories;
        $course->categoryName=join(',',$categories->pluck('name')->toArray());

        $course->categoryId =$categories->where('top',false)->first()->id;
    }

    function getTeacherIds(Course $course)
    {
        $options =[];
        $teachers=$course->teachers;
        if(count($teachers)){
            $options = $teachers->map(function ($item) {
                return $item->toOption();
            })->all();
        }

        if($course->teacherGroup){
            array_push($options, [ 'text' => $course->teacherGroup->name ,  'value' => $course->teacherGroup->id ]);
        }

        return  $options;
    }

    

    public function index()
    {
       
        $request=request();

        $term=0;
        if($request->term)  $term=(int)$request->term;

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $category=0;
        if($request->category)  $category=(int)$request->category;

        $reviewed=true;
        if($request->reviewed)  $reviewed=Helper::isTrue($request->reviewed);

        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=999;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

        $selectedCenter = null;
        if ($center) $selectedCenter = Center::find($center);
        if ($selectedCenter == null)
        {
            $center = 0;
            if ($pageSize == 999) $pageSize = 10;
        }
        else
        {
            $pageSize = 999;
        }

        $canReview=$this->canReviewCenter($selectedCenter);

        $selectedCategory = null;
        if ($category) $selectedCategory = Category::find($category);

        $termOptions = $this->terms->options();
        if (!$term){
            if(count($termOptions)) $term = (int)$termOptions[0]['value'];
            
        } 
      
        $courses =  $this->courses->fetchCourses($term ,$selectedCenter, $selectedCategory,$reviewed, $keyword);
      
        $pageList = new PagedList($courses,$page,$pageSize);

        foreach($pageList->viewList as $course){
            $course->fullName();
            $course->loadClassTimes();
            $this->setTeachers($course);
            $this->setCategories($course);
        } 

        if($this->isAjaxRequest()){
            return response() ->json([
                'canReview' => $canReview,
                'model' => $pageList
            ]);
        }

        foreach ($termOptions as $item)
        {
            $item['text'] .= '學期';
        }

        $centerOptions=$this->centers->centerOptions();
        $categoryOptions = $this->categories->options();

        $model=[
            'title' => '課程管理',
            'menus' => $this->adminMenus('CoursesAdmin'),

            'centers' => $centerOptions,
            'terms' => $termOptions,
            'categories' => $categoryOptions,
            'weekdays' => $this->courseInfoes->weekdayOptions(),
            'canReview' => $canReview,
            'canImport' => $this->canImport(),
            'list' =>  $pageList
        ];

        return view('courses.index')->with($model);
    }

    public function create()
    {
        $course=Course::init();
        $user=User::init();

    
        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        $centerIds=[];
        if (count($centerOptions))
        {
            array_push($centerIds,$centerOptions[0]['value']);
        }
      
        $form=[
            'course' => $course,
            'user' => $user,
            'centerOptions' => $centerOptions,
            'centerIds' => $centerIds

        ];

        return response() ->json($form);
      
    }

    function validateCourseInputs(array $courseValues, array $teacherIds)
    {
        $errors=[];

        if(!count($teacherIds))  $errors['teacherIds'] = ['請選擇教師'];

        $beginDate= Carbon::parse($courseValues['beginDate']);
        $endDate= Carbon::parse($courseValues['endDate']);

        if ($endDate <= $beginDate)
        {
            $errors['course.endDate'] = ['日期錯誤'];
        }

        return $errors;
    }

    public function store(CourseRequest $request)
    {

        $courseValues=$request->getCourseValues();
        $userValues=$request->getUserValues();
        $profileValues= $userValues['profile'];

        
        $errors=$this->users->validateUserInputs($userValues);
        if($errors) return $this->requestError($errors);

        $errors=$this->validateCourseInputs($courseValues);

        $sid=$userValues['profile']['sid'];
        if(!$sid){
            $errors['user.profile.sid'] = ['必須填寫身分證號'];
        }

        $centerIds=$request->getCenterIds();
        if(!count($centerIds)){
            $errors['centerIds'] = ['請選擇所屬中心'];
        }

      
        if($errors) return $this->requestError($errors);

        $current_user=$this->currentUser();
        $updatedBy=$current_user->id;

        $courseValues['updatedBy']=$updatedBy;
        $userValues['updatedBy']=$updatedBy;
        $profileValues['updatedBy']=$updatedBy;
        
        $userValues=array_except($userValues,['profile']);
        $userId=$request->getUserId();
        $user=null;
        if($userId){
            $user = User::find($userId);

            $user->profile->update($profileValues);
            $this->users->updateUser($user,$userValues);
            
        }else{
          
           $user=$this->users-> createUser(new User($userValues),new Profile($profileValues));
           $userId=$user->id;
         
        }

        $wageValues=[
            'account' => $courseValues['account'],
            'money' => $courseValues['wage'],
            'updatedBy' => $updatedBy,
        ];

        $course=Course::find($userId);
        if($course){
            $course->update($courseValues);
            $course->addRole();

            $wage=$course->getWage();
            if($wage) $wage->update($wageValues);

        }else{
            $course=$this->courses->createCourse($user,new Course($courseValues), $wageValues);
            $course->userId=$userId;
        }

        $course->centers()->sync($centerIds);
       
        return response() ->json($course);
    }

    public function show($id)
    {
        $course = $this->courses->getById($id);
        if(!$course) abort(404);

        $course->fullName();
        $course->loadClassTimes();
        $this->setTeachers($course);
        $this->setCategories($course);

        $course->processes;

        $course->canEdit = $this->canEdit($course);
        $course->canReview =$this->canReview($course);
        $course->canDelete = $this->canDelete($course);

        return response() ->json($course);
        
    }

    public function edit($id)
    {
       
        $course = $this->courses->getById($id); 
        if(!$course) abort(404);   
        if(!$this->canEdit($course)) $this->unauthorized();

        $course->number=$course->serial();
        $this->setCategories($course);
       
        $teacherIds=$this->getTeacherIds($course);

        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        $withEmpty=false;
        $categoryOptions = $this->categories->options($withEmpty);
        
        $center=$course->center;
       
        $teacherOptions = array_merge($this->teachers->options($center), $this->teacherGroups->options($center));
       
        $form=[
            'course' => $course,
            'teacherIds' =>$teacherIds,
            'teacherOptions' => $teacherOptions,
            'termOptions' => $this->terms->options(),
            'centerOptions' => $centerOptions,
            'categoryOptions' => $categoryOptions
        
        ];

        return response() ->json($form);
       
        
    }


    public function update(CourseRequest $request, $id)
    {
        $course = $this->courses->getById($id); 
        if(!$course) abort(404);   
        if(!$this->canEdit($course)) $this->unauthorized();

        $courseValues=$request->getCourseValues();
        $teacherIdValues=$request->getTeacherIds();
       
        $errors=$this->validateCourseInputs($courseValues,$teacherIdValues);
        
        $category =Category::find($courseValues['categoryId']);
        $center = Center::find($courseValues['centerId']);
        $term = Term::find($courseValues['termId']);

        $courseValues['number'] =Course::initCourseNumber($courseValues['number'] ,$category,$center ,$term );
        $duplicate = $this->courses->getCourseByNumber($courseValues['number']);
        if ($duplicate && $duplicate->id != $id)
        {
            $errors['course.number'] = ['編號重複了'];
        }
       

        if($errors) return $this->requestError($errors);


        $teacherIds = [];
        foreach($teacherIdValues as $item){
            if (Helper::isTrue($item['group']))
            {
                $courseValues['teacherGroupId'] = $item['value'];
            }
            else
            {
                array_push($teacherIds,$item['value']);
            }
        }

        $courseValues['updatedBy']=$this->currentUserId();
        $course->fill($courseValues);

        $this->courses->updateCourse($course,[$category->id], $teacherIds);

        return response() ->json();
    }

    public function review(Request $form)
    {
        $reviewedBy=$this->currentUserId();
        
        $courses=  $form['courses'];

        if(count($courses) > 1){
            $courseIds=array_pluck($courses, 'id');
            $this->courses->reviewOK($courseIds, $reviewedBy);
        }else{
            
            $id=$courses[0]['id'];
         
            $reviewed=Helper::isTrue($courses[0]['reviewed']);

            $this->courses->updateReview($id,$reviewed ,$reviewedBy);
        }
        

        return response() ->json();


    }

    public function destroy($id) 
    {
        $course = Course::findOrFail($id);
        if(!$this->canDelete($course)) $this->unauthorized();

        $this->courses->deleteCourse($course, $this->currentUserId());
       
       
        return response() ->json();
    }

    public function editInfo($id)
    {
        $course = Course::findOrFail($id);
        if(!$this->canEdit($course)) $this->unauthorized();

        return response() ->json(['course' => $course]);
    }

    public function updateInfo($id, CourseInfoRequest $request)
    {
        $course = Course::findOrFail($id);
        if(!$course) abort(404);   
        if(!$this->canEdit($course)) $this->unauthorized();

        $values=$request->getValues();

        $errors=[];
        $tuition=floatval($values['tuition']);
        if(!$tuition){
            $errors['course.tuition'] = ['學費錯誤'];
        }
        $limit=(int)$values['limit'];
        if(!$limit){
            $errors['course.limit'] = ['人數上限錯誤'];
        }

        if($errors) return $this->requestError($errors);
        
        $values['updatedBy']=$this->currentUserId();
        $course->update($values);

    }

    public function import(Request $form)
    {
        
        if(!$this->canImport()){
            return $this->unauthorized();
        }

        
        $errors=[];
      
        if(!$form->hasFile('file')){
            $errors['msg'] = ['無法取得上傳檔案'];
        } 

        if($errors) return $this->requestError($errors);


        $file=Input::file('file');   

        $type=$form['type'];
        if($type=='create'){
            $err_msg=$this->courses->importCourses($file,$this->currentUserId());
        }else if($type=='details'){
            $err_msg=$this->courses->importCourseDetails($file,$this->currentUserId());
        }else{
            abort(404);
        } 
     
        
        if($err_msg)
        {
            $errors['msg'] = [$err_msg];
            return $this->requestError($errors);
        }

        return response() ->json();

       
    }
}
