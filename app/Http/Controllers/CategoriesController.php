<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Category;
use App\Services\Categories;
use App\Services\Files;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class CategoriesController extends Controller
{
    
    public function __construct(Categories $categories,Files $files)
    {
        $this->categories=$categories;
        $this->files=$files;
     
    }

    function canEdit()
    {
        return $this->currentUserIsDev();
    }
    
    function canImport()
    {
        return $this->currentUserIsDev();
    }

    public function index()
    {
        $request=request();
        $active=true;
        if($request->active)  $active=Helper::isTrue($request->active);
      
     
        $categories=$this->categories->fetchCategories($active);
        $categories=$categories->where('top',false);
       
        $categories=$this->categories->getOrdered($categories);
       
      
        $pageList = new PagedList($categories);

        if($this->isAjaxRequest()){
            return response() ->json($pageList);
        }
       

        $menus=$this->adminMenus('MainSettings');
       
        return view('categories.index')->with([
            'title' => '課程分類管理',
            'menus' => $menus,
            'canEdit' => $this->canEdit(),
            'canImport' => $this->canImport(),
            'list' =>  $pageList
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

     
        $values=$request->getValues();
        $values['updatedBy'] = $current_user->id;
        
        $category =  $this->categories->createCategory(new Category($values));
        return response() ->json($category);
    }
    

    public function update(CategoryRequest $request, $id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $category = Category::findOrFail($id);
       
        $values=$request->getValues();

        $values['updatedBy'] = $current_user->id;
        
        $category->update($values);
        return response() ->json();
    }

    public function destroy($id) 
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $category = Category::findOrFail($id);
        $this->categories->removeCategory($category, $current_user->id);

        return response() ->json();
    }
    
    public function importances(Request $request)
    {
        $values=$request->get('importances');
        foreach ($values as $value)
        {
            $value['id'];
            $value['importance'];
            $this->categories->updateImportance($value['id'],  $value['importance']);
        }

        return response() ->json();
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

        $err_msg=$this->categories->importCategories($file,$this->currentUserId());
        
        if($err_msg)
        {
            $errors['msg'] = [$err_msg];
            return $this->requestError($errors);
        }

        return response() ->json();

       
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

       

        $this->files->saveUploadsData($file,$type);

        return response() ->json();
        
       
    }


}
