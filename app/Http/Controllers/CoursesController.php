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
use App\Services\Volunteers;
use App\Services\CourseInfoes;
use App\Services\Files;
use App\Services\Quits;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class CoursesController extends Controller
{
    
    public function __construct(Courses $courses, Teachers $teachers,TeacherGroups $teacherGroups, Volunteers $volunteers,
        Terms $terms,Centers $centers,Categories $categories,CourseInfoes $courseInfoes, Files $files, Quits $quits)
    {
        $this->courses=$courses;
        $this->teachers=$teachers;
        $this->teacherGroups=$teacherGroups;
        $this->volunteers=$volunteers;
        $this->terms=$terms;
        $this->centers=$centers;
        $this->categories=$categories;
        $this->courseInfoes=$courseInfoes;
        $this->files=$files;
        $this->quits=$quits;
    }

    function canEdit(Course $course)
    {
        return $this->canEditCenter($course->center);

    }

    function canEditCenter(Center $center)
    {
        return $this->canAdminCenter($center);
    }

    function canReview(Course $course)
    {
        return $this->canReviewCenter($course->center);

    }
    function canReviewCenter(Center $center=null)
    {
        if(!$center) return false;

        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        return $this->canAdminCenter($center);
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

    function setVolunteers($course)
    {
        $volunteers=$course->volunteers;
       
        if(count($volunteers)){
            $names=join(',',$volunteers->pluck('user.profile.fullname')->toArray());
            $course->volunteerNames=$names;
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
            array_push($options, $course->teacherGroup->toOption() );
        }

        return  $options;
    }  

    function getVolunteerIds(Course $course)
    {
        $options =[];
        $volunteers=$course->volunteers;
        if(count($volunteers)){
            $options = $volunteers->map(function ($item) {
                return $item->toOption();
            })->all();
        }

        return  $options;
    }  

    function canSignup(Course $course,User $user)
    {
        //User報名過的課程記錄
        $coursesSignupedIds = [];
        $userSignupDetailRecords = $this->signups->getSignupDetailsByUser($user);
        
        $coursesSignupedIds = $userSignupDetailRecords->pluck('courseId')->toArray();
        
        if (in_array($selectedCourse->id, $coursesSignupedIds)){
            return false;
        }

        return true;
    }

    function readIndexRequest()
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

        if(!$this->isAjaxRequest() && !$selectedCenter){
            $selectedCenter=$this->defaultAdminCenter();
        }

        if (!$selectedCenter && $pageSize == 999){
            $pageSize = 10;
        }else{
            $pageSize = 999;
        }

        $selectedTerm = null;
        if ($term) $selectedTerm = $this->terms->getById($term);

        $selectedCategory = null;
        if ($category) $selectedCategory = Category::find($category);


        $params=[
            'term' => $selectedTerm,
            'center' => $selectedCenter,
            'category' => $selectedCategory,
            'keyword' => $keyword,
            'reviewed' => $reviewed,
            'page' => $page,
            'pageSize' => $pageSize

        ];

        return $params;
    }

    

    public function index()
    {
        $params=$this->readIndexRequest();

        $selectedTerm=$params['term'];
       
        if(!$selectedTerm){
            $selectedTerm=$this->terms->getActiveTerm();
            $params['term']=$selectedTerm;
        }

        $selectedCenter=$params['center'];
        
        $canReview=$this->canReviewCenter($selectedCenter);
        

        $selectedCategory = $params['category'];

        $reviewed= $params['reviewed'];
        $keyword= $params['keyword'];

        
      
        $courses = $this->courses->fetchCourses($selectedTerm->id ,$selectedCenter, $selectedCategory,$reviewed, $keyword);
           
        $page= $params['page'];
        $pageSize= $params['pageSize'];
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

        // 初次載入頁面 Not Ajax
        $termOptions = $this->terms->options();
        foreach ($termOptions as $item)
        {
            $item['text'] .= '學期';
        }

        $centerOptions=$this->centers->centerOptions();

        $categoryOptions = $this->categories->forEditCourseOptions();
        array_unshift($categoryOptions, ['text' => '所有分類' , 'value' =>'0']);
        

        $percentsOptions=$this->quits->percentsOptions(false);
        array_unshift($percentsOptions, ['text' => '全額退費' , 'value' => 100 ]);
        

        $model=[
            'title' => '課程管理',
            'menus' => $this->adminMenus('CoursesAdmin'),

            'params' => $params,
            'centers' => $centerOptions,
            'terms' => $termOptions,
            'categories' => $categoryOptions,
            'weekdays' => $this->courseInfoes->weekdayOptions(),
            'canReview' => $canReview,
            'canImport' => $this->canImport(),

            'percentsOptions'=> $percentsOptions,
            'list' =>  $pageList
        ];

        return view('courses.index')->with($model);
    }

    public function create()
    {
        $request=request();

        $term=0;
        if($request->term)  $term=(int)$request->term;

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $selectedCenter = null;
        if ($center) $selectedCenter = Center::find($center);

        if(!$selectedCenter){
            $selectedCenter=$this->defaultAdminCenter();
        }

        $selectedTerm = null;
        if ($term) $selectedTerm = $this->terms->getById($term);

        if(!$selectedTerm){
            $selectedTerm=$this->terms->getActiveTerm();
        }
      

        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();
       
        $course=Course::init($selectedTerm->id,$selectedCenter->id);

        $center=$this->centers->getById($selectedCenter->id);
       
       
        $teacherOptions = array_merge($this->teachers->options($selectedCenter), $this->teacherGroups->options($selectedCenter));
       
        $volunteerOptions = $this->volunteers->options($selectedCenter);
        
      
        $form=[
            'course' => $course,
            'teacherIds' =>[],
            'volunteerIds' =>[],
            'centerOptions' => $centerOptions,
            'teacherOptions' => $teacherOptions,
            'volunteerOptions' => $volunteerOptions,
           
        
        ];

        return response()->json($form);
      
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

    function testActive($course)
    {
        $percents=100;
        $reviewedBy=1;

        $this->courses->shutDownCourse($course, $percents,$reviewedBy);
    }

    public function store(CourseRequest $request)
    {
        $courseValues=$request->getValues();
        $teacherIdValues=$request->getTeacherIds();
        $volunteerIdValues=$request->getVolunteerIds();
       
        $centerId=$courseValues['centerId'];
        $center=$this->centers->getById($centerId);
        if(!$this->canEditCenter($center)) return $this->unauthorized();
       
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

        $volunteerIds = [];
        foreach($volunteerIdValues as $item){
            array_push($volunteerIds,$item['value']);
        }

       

        $courseValues['updatedBy']=$this->currentUserId();
        $course=new Course($courseValues);

        $course=$this->courses->createCourse($course,[$category->id], $teacherIds,$volunteerIds);

       

        return response() ->json($course);
    }

    public function show($id)
    {
        $course = $this->courses->getById($id);
        if(!$course) abort(404);

        $course->fullName();
        $course->loadClassTimes();
        $this->setTeachers($course);
        $this->setVolunteers($course);
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
        $volunteerIds=$this->getVolunteerIds($course);

        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        $categoryOptions = $this->categories->forEditCourseOptions();
        
        $center=$course->center;
       
        $teacherOptions = array_merge($this->teachers->options($center), $this->teacherGroups->options($center));
        $volunteerOptions = $this->volunteers->options();
       
      
        $form=[
            'course' => $course,
            'teacherIds' =>$teacherIds,
            'volunteerIds' =>$volunteerIds,
            'teacherOptions' => $teacherOptions,
            'volunteerOptions' => $volunteerOptions,
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
        if(!$this->canEdit($course)) return $this->unauthorized();

        $courseValues=$request->getValues();
        $teacherIdValues=$request->getTeacherIds();
        $volunteerIdValues=$request->getVolunteerIds();
       
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
        $groupId=0;
        foreach($teacherIdValues as $item){
            if (Helper::isTrue($item['group']))
            {
               
                $groupId=$item['value'];
            }
            else
            {
                array_push($teacherIds,$item['value']);
            }
        }

        if($groupId){
            $courseValues['teacherGroupId'] = $groupId;
        }else{
            $courseValues['teacherGroupId'] = null;
        }

        $volunteerIds = [];
        foreach($volunteerIdValues as $item){
            array_push($volunteerIds,$item['value']);
        }



        $courseValues['updatedBy']=$this->currentUserId();
        $course->fill($courseValues);

        $this->courses->updateCourse($course,[$category->id], $teacherIds,$volunteerIds);

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
    
    public function active(Request $form)
    {
       
        //批次停開/恢復開課
        $reviewedBy=$this->currentUserId();

        $courses = $form['courses'];
        $active=$courses[0]['active'];
        $percents=100;  //全額退

        $courseIds=array_column($courses, 'id');

        $course = $this->courses->getById($courseIds[0]); 
        if(!$this->canReview($course)) return $this->unauthorized();

        $course->update([
            'active' => true,
            'updatedBy' => 999
            
        ]);

        if(count($courseIds) > 1){
            $this->courses->setActives($courseIds,$active,$reviewedBy,$percents);
        }else{
           
            $this->courses->setActive($course , $active , $reviewedBy,$percents);
            
        }

       

        return response()->json();
    }

    public function shutdown(Request $form,$id)
    {
        $course = $this->courses->getById($id); 
        if(!$this->canReview($course)) return $this->unauthorized();

        

        $reviewedBy=$this->currentUserId();
        $percents=(int)$form['percents'];
        $active=false;
        
        $this->courses->shutDownCourse($course , $percents ,$reviewedBy);

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
      //  $err_msg=$this->courses->importCoursesDiscount($file,$this->currentUserId());
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

        return response()->json();

       
    }

    public function upload(Request $form)
    {
        if(!$this->canImport()){
            return $this->unauthorized();
        }
        
        $errors=[];
      
        if(!$form->hasFile('file')){
            $errors['msg'] = ['無法取得上傳檔案'];
        } 

        if($errors) return $this->requestError($errors);

        $type=$form['type'];
        if(!$type) abort(500);

        $file=Input::file('file');  

        $center = Center::findOrFail($form['center']);  

        $canEdit = $this->canEditCenter($center);
        if(!$canEdit) return $this->unauthorized();

        $term = Term::findOrFail($form['term']);  

        $this->files->saveUploadsData($file,$type,$center,$term);

        return response() ->json();
        
       
    }
}
