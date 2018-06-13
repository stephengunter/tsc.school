<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Doc;
use App\Services\Docs;
use App\Services\Files;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class DocsController extends Controller
{
    public function __construct(Docs $docs,Files $files)
    {
        $this->docs=$docs;
        $this->files=$files;
    }


    function canEdit()
    {
        if($this->currentUserIsDev()) return true;
        return $this->currentUser->admin->isHeadDocAdmin();
    }
   
    
    public function index()
    {
       
        $docs=$this->docs->getAll();
      
        $pageList = new PagedList($docs);

        foreach($pageList->viewList as $doc){
            $doc->url='/manage/docs/' . $doc->id;
        } 

        if($this->isAjaxRequest()){
            return response()->json($pageList);
        }
       

        $menus=$this->adminMenus('HomePageAdmin');

        $canEdit=$this->canEdit();
       
        return view('docs.index')->with([
            'title' => '下載專區',
            'menus' => $menus,
            'list' =>  $pageList,
            'canEdit' => $canEdit
        ]);
    }

    
    public function store(Request $form)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) return $this->unauthorized();

        $id=(int)$form['id'];

        if($id) return $this->update($form,$id);

        $errors=[];
      
        if(!$form->hasFile('file')){
            $errors['msg'] = ['無法取得上傳檔案'];
        } 

        if($errors) return $this->requestError($errors);


        $file=Input::file('file');  
   
        $save_path = $this->files->savePublicDoc($file);

       

        $title=$form['title'];
        $name=$form['name'];

        $doc=Doc::create([
            'path' => $save_path,
            'type' => $file->getClientOriginalExtension(),
            'name' => $name,
            'title' => $title,
            'updatedBy' => $current_user->id
        ]);
       
        
        return response()->json();
    }

    public function show($id)
    {
        $doc = Doc::findOrFail($id);
      
        return $this->files->downloadPublicDoc($doc);
        
    }

    public function edit($id)
    {
        $doc = Doc::findOrFail($id);

        $current_user=$this->currentUser();
        if(!$this->canEdit()) return $this->unauthorized();
        
      
        $form=[
            'doc' => $doc
        ];

        return response()->json($form);
       
        
    }


    public function update($form, $id)
    {
        $doc = Doc::findOrFail($id);

        $current_user=$this->currentUser();
        if(!$this->canEdit()) return $this->unauthorized();

        $errors=[];

        $title=$form['title'];
        $name=$form['name'];
      
        if($form->hasFile('file')){
            $file=Input::file('file');     
            $save_path = $this->files->savePublicDoc($file); 

            $doc->update([
                'path' => $save_path,
                'type' => $file->getClientOriginalExtension(),
                'name' => $name,
                'title' => $title,
                'updatedBy' => $current_user->id
            ]);

        }else{
            $doc->update([
                'title' => $title
            ]);
        } 
        
        return response()->json();
    }

    public function destroy($id) 
    {
        $doc = Doc::findOrFail($id);
        if(!$this->canEdit()) return $this->unauthorized();

        $doc->delete();
         
        return response() ->json();
    }

   
}
