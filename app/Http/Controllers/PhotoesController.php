<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PhotoRequest;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use App\Services\Photoes;

use Image;
use App\Photo;
use Storage;
use File;

class PhotoesController extends Controller
{
    private  $default_folder = '/images/uploads/';
   
    public function __construct(Photoes $photoes)                                
    {
        $this->photoes=$photoes;
    }

    function canDelete($photo)
    {
        if($this->currentUserIsDev()) return true;
        $user=$photo->user;
        if($user->teacher){
            if(!count($user->teacher->centers)) return true;

            return $this->canAdminCenters($user->teacher->centers);
        }
        return true;
    }

    function getStorageDisk($type)
    {
        if($type=='account'){
            return Storage::disk('local');    // storage\app\
        }

        return null;
    }
    
    function getFolder($type)
    {
        if($type=='account'){
           return 'uploads/accounts/'; 
        }
        return '';
    }
    

    


    public function show($id)
    {
        $photo=Photo::findOrFail($id);

        if($photo->public){

        }else{

            $disk=$this->getStorageDisk($photo->type);
            $savePath= $disk->getDriver()->getAdapter()->getPathPrefix() . $photo->path;

            return Image::make($savePath)->response();
        }
            
    }
    public function store(Request $form)
    {
        $errors=[];
        if(!$form->hasFile('image_file')){
            $errors['msg'] = ['無法取得上傳檔案'];  
        }
        if($errors) return $this->requestError($errors);


        $file=Input::file('image_file');
        $type= $form['type'];
        $userId= $form['user_id'];
        $width= $form['width'];
        $height= $form['height'];

        $fileName=$this->photoes->createFileName($file);
        $folder= $this->getFolder($type);
        $path= $folder . $fileName;

        $disk=$this->getStorageDisk($type);
        if($disk){
            $savePath= $disk->getDriver()->getAdapter()->getPathPrefix() . $path;
            $this->photoes->save($file ,$width , $height ,$savePath);
        }else{
            
        }


        if($type=='account'){
            $this->photoes->setAccountPhoto($userId,$path);
            return response()->json();
        }
        
        
        

    }

    public function destroy($id)
    {
        $photo=Photo::findOrFail($id);
        
        if(!$this->canDelete($photo)) return $this->unauthorized();

        if($photo->isAccountPhoto()){
          
            $account = $photo->user->getAccount();
            if($account->photoId == $id){
                $account->update([
                    'photoId' => ''
                ]);
            }
        }

        $photo->delete();

        return response()->json();
        
    }

    
}
