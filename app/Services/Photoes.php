<?php

namespace App\Services;

use App\Photo;
use App\User;
use Image;
use Carbon\Carbon;

class Photoes 
{
    public function createFileName($file)
    {
        $timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());            
        return $timestamp. '-' .$file->getClientOriginalName();         
    }

    public function save($file ,$width , $height ,$path)
    {
        //尺寸不變
        if(!$width && !$height){
            Image::make($file->getRealPath())->save($path);
        }

         //鎖定寬度
        if($width && !$height){
            Image::make($file->getRealPath())->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
            })->save($path);
            
        }
        //鎖定高度
        if(!$width && $height){
            Image::make($file->getRealPath())->resize(null, $height, function ($constraint) {
                    $constraint->aspectRatio();
            })-> save($path);
            
        }

        //鎖定寬度與高度
        if($width && $height){
            Image::make($file->getRealPath())->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
            })-> save($path);
        }
    } 

    public function getAccountPhoto($userId)
    {
        return $this->getByType('account')->where('userId',$userId)->first();
    }

    public function setAccountPhoto($userId, $path)
    {
        $user=User::find($userId);
        $photo=$this->getAccountPhoto($userId);
        if($photo){
            $photo->update([
                'path' => $path
            ]);
        }else{
            $photo=Photo::create([
                'path' => $path,
                'userId' => $userId,
                'type' => 'account',
                'public' => false
            ]);
        }

        $account = $user->getAccount();
        $account->update([
            'photoId' => $photo->id
        ]);
    }

    public function getByType($type)
    {
        return Photo::where('type',$type);
    }
   
   

  
  
   
   
    
}