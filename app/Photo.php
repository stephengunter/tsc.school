<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Photo extends Model
{
    protected $table='photoes';

    protected $fillable = ['title', 'description', 'path',
     'userId' , 'public' ,'type'];

    
    public function user() 
	{
		return $this->hasOne('App\User', 'id' ,'userId');
    }

    public function isAccountPhoto()
    {
        return $this->type=='account';
    }


    public static function defaultCourse()
    {
         $photo= new Photo();
         $photo->path='/images/default-course.png';
         $photo->default=true;
         return  $photo;
    }
    public static function defaultCenter()
    {
         $photo= new Photo();
         $photo->path='/images/default-center.png';
         $photo->default=true;
         return  $photo;
    }
    public static function defaultProfile()
    {
         $photo= new Photo();
         $photo->path='/images/default-profile.png';
         $photo->default=true;
         return  $photo;
    }


    public static function create_file_name($file)
    {
        $timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());            
        return $timestamp. '-' .$file->getClientOriginalName();         
    }

    public static function save_upload_photo($file ,$width , $height ,$folder_name) 
    {
        $file_name = Photo::create_file_name($file);  
        $file_path= $folder_name . $file_name;

        $save_path =  public_path() . $file_path;

		//尺寸不變
        if(!$width && !$height){
            Image::make($file)->save($save_path);
            return $file_path;
        }

         //鎖定寬度
        if($width && !$height){
             Image::make($file)->resize($width, null, function ($constraint) {
                     $constraint->aspectRatio();
             })-> save($save_path);
            return $file_path;
            
        }
        //鎖定高度
        if(!$width && $height){
             Image::make($file)->resize(null, $height, function ($constraint) {
                     $constraint->aspectRatio();
             })-> save($save_path);
           return $file_path;
            
        }

        //鎖定寬度與高度
        if($width && $height){
             Image::make($file)->resize($width, $height, function ($constraint) {
                     $constraint->aspectRatio();
             })-> save($save_path);

             return $file_path;
        }
	}


    
}