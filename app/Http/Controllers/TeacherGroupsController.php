<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TeacherGroupsRequest;

use App\TeacherGroup;
use App\User;
use App\Profile;
use App\Center;
use App\Role;
use App\Services\TeacherGroups;
use App\Services\Teachers;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class TeacherGroupsController extends Controller
{
    
    public function __construct(TeacherGroups $teacherGroups,Teachers $teachers)
    {
        $this->teacherGroups=$teacherGroups;
        $this->teachers=$teachers;
    }

    function canEdit($teacherGroup)
    {
        if($this->currentUserIsDev()) return true;
        if(!$teacherGroup->center) return true;
      
        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect([$teacherGroup->center]);

        if(count($intersect)) return true;
        return false;

    }

    
    function canDelete($teacherGroup)
    {
        return $this->canEdit($teacherGroup);
    }
   
   

    public function create()
    {
        $teacherGroup=TeacherGroup::init();
        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        $teacherGroup['centerId']=$centerOptions[0]['value'];
      
        $form=[
            'teacher' => $teacherGroup,
            'centerOptions' => $centerOptions
        ];

        return response() ->json($form);
      
    }

    public function store(Request $request)
    {
       
        $values=$request->get('teacher');
      
        $errors=$this->teacherGroups->validateInputs($values);

        if($errors) return $this->requestError($errors);

    
        $values['updatedBy'] = $this->currentUserId();

        $teacherGroup=TeacherGroup::create($values);

        return response() ->json($teacherGroup);
    }

    public function show($id)
    {
        $teacherGroup = $this->teacherGroups->getById($id);
        if(!$teacherGroup) abort(404);

      
     
        $teacherGroup->canEdit=$this->canEdit($teacherGroup);
        $teacherGroup->canDelete=$this->canDelete($teacherGroup);
       

        return response() ->json($teacherGroup);
        
    }

    public function teachers($id)
    {
        $teacherGroup = $this->teacherGroups->getById($id);
        if(!$teacherGroup) abort(404);

        $teachersIds=$teacherGroup->teachers->pluck('userId')->toArray();

        $teachers=$this->teachers->getAll()->whereIn('userId', $teachersIds)->get();
      

        return response() ->json($teachers);
    }

    public function edit($id)
    {
        $teacherGroup = TeacherGroup::findOrFail($id);        
        if(!$this->canEdit($teacherGroup)) $this->unauthorized();

        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

      
        $form=[
            'teacher' => $teacherGroup,
            'centerOptions' => $centerOptions
        ];

        return response() ->json($form);
        
    }

    public function editTeacher($id)
    {
        
        $teacherGroup = $this->teacherGroups->getById($id);  
        if(!$teacherGroup) abort(404);      

        if(!$this->canEdit($teacherGroup)) $this->unauthorized();

        $teachers=$this->teachers->fetchTeachers($teacherGroup->center);

        $alreadyInGroupIds=$teacherGroup->teachers->pluck('userId')->toArray();
       
        $teachers=$teachers->whereNotIn('userId',$alreadyInGroupIds)->get();
        
        return response() ->json($teachers);
        
    }


    public function addTeachers(Request $request, $id)
    {
        $teacherGroup = TeacherGroup::findOrFail($id);
        if(!$this->canEdit($teacherGroup)) $this->unauthorized();
        
        
        
        $teacherIds=$request->get('teacherIds');

        $teacherGroup->addTeachers($teacherIds);

        return response() ->json();
    }   
      
    public function removeTeacher(Request $request, $id)
    {
        $teacherGroup = TeacherGroup::findOrFail($id);
        if(!$this->canEdit($teacherGroup)) $this->unauthorized();

        $teacherIds=$request->get('teacherIds');
        $teacherGroup->removeTeachers($teacherIds);

        return response()->json();
        
    }

    public function update(Request $request, $id)
    {
        $teacherGroup = TeacherGroup::findOrFail($id);
        if(!$this->canEdit($teacherGroup)) $this->unauthorized();
       
        $values=$request->get('teacher');
      
        $errors=$this->teacherGroups->validateInputs($values);

        if($errors) return $this->requestError($errors);

    
        $values['updatedBy'] = $this->currentUserId();

        $teacherGroup->update($values);

        return response() ->json();
    }

   

    public function destroy($id) 
    {
        $teacherGroup = TeacherGroup::findOrFail($id);
        if(!$this->canDelete($teacherGroup)) $this->unauthorized();

        $teacherGroup->removed=true;
        $teacherGroup->updatedBy=$this->currentUserId();
        
        $teacherGroup->save();
       
        return response() ->json();
    }

    
}
