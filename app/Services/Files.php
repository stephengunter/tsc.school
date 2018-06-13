<?php

namespace App\Services;
use App\Doc;
use App\Term;
use App\Center;
use Storage;
use File;
use Carbon\Carbon;
use App\Core\Helper;

class Files 
{
    function getTemplateTitle($name)
    {
        $title = '';

        switch ($name)
        {
            case 'create_course':
                $title= '課程名稱';
                break;
            case 'course_details':
                $title = '課程詳細資訊';
                break;
            case 'class_times':
                $title = '課程上課時間';
                break;
            case 'processes':
                $title = '課程教學大綱';
                break;
            case 'teachers':
                $title = '教師資料';
                break;
            case 'group_teachers':
                $title = '群組教師資料';
                break;
            case 'centers':
                $title = '開課中心資料';
                break;
            case 'categories':
                $title = '課程分類資料';
                break;
            case 'admins':
                $title = '管理員資料';
                break;
            case 'volunteers':
                $title = '志工資料';
                break;    

        }

        return $title;
    }

    function getTemplateFileName($key)
    {
        $name = '';

        switch ($key)
        {
            case 'create_course':
                $name= 'courses.xlsx';
                break;
            case 'course_details':
                $name = 'course-infoes.xlsx';
                break;
            case 'class_times':
                $name = 'classtimes.xlsx';
                break;
            case 'processes':
                $name = 'processes.xlsx';
                break;
            case 'teachers':
                $name = 'teachers.xlsx';
                break;
            case 'group_teachers':
                $name = 'teacher-groups.xlsx';
                break;
            case 'centers':
                $name = 'centers.xlsx';
                break;
            case 'categories':
                $name = 'categories.xlsx';
                break;
            case 'admins':
                $name = 'admins.xlsx';
                break;
           case 'volunteers':
                $name = 'volunteers.xlsx';
                break;
        }

        return $name;
    }

    function getDisk(bool $public)
    {
        if($public) return  Storage::disk('public');
        return Storage::disk('local');
    }

    function saveFile($disk , $file, $save_path)
    {
        $disk->put($save_path ,  File::get($file));
        
    }

    function createRandomFileName($file)
    {
        $timestamp = str_replace([' ', ':','-'], '', Carbon::now()->toDateTimeString());
        $random_file_name= $timestamp .'.' .$file->getClientOriginalExtension(); 
        return $random_file_name;
    }

    public function downloadTemplate($name)
    {
        $fileName=$this->getTemplateFileName($name);
       
       
        if(!$fileName)  abort(404);
        
        $ext=Helper::get_file_extension($fileName);
      
        $headers = ['Content-Type: ' . Helper::getMimeTypes($ext)];

        $disk=$this->getDisk(false);
        $path =$disk->getDriver()->getAdapter()->getPathPrefix(); 
        $path .= 'templates/' . $fileName;

        $title=$this->getTemplateTitle($name) . '_匯入範本';
        $title .= '.' . $ext;
        
        return response()->download($path,  $title , $headers);
       
       
    }

    public function downloadPublicDoc($doc)
    {
        $headers = ['Content-Type: ' . Helper::getMimeTypes($doc->type)];

        $public=true;
        $disk=$this->getDisk($public);
        $path =$disk->getDriver()->getAdapter()->getPathPrefix(); 

        $docPath=$doc->path;
        if(Helper::str_starts_with($docPath, '/')){
            $docPath=substr($docPath, 1);
        }
      
        $path .= $docPath;
        
        $title = $doc->title;
        $title .= '.' . $doc->type;
        
        return response()->download($path,  $title , $headers);
    }

    public function savePublicDoc($file)
    {
        $random_file_name= $this->createRandomFileName($file); 
        $public=true;
        $disk=$this->getDisk($public);
        $save_path= '/docs/' . $random_file_name;

        $this->saveFile($disk , $file, $save_path);
        
        return $save_path;

    }

    public function saveUploadsData($file, $type ,Center $center=null,Term $term=null)
    {
      
        $folderName = '';
        if($center) $folderName =$center->name . '/';

        $title=$this->getTemplateTitle($type);
        $fileName='';
        if($type=='centers' || $type=='categories'){
            $fileName = $title;
        }else{
            if($term) $fileName = sprintf('%s%d學期%s', $center->name, $term->number,$title);
            else $fileName = sprintf('%s%s', $center->name, $title);
            
        }

        $disk=$this->getDisk(false);
        $save_path= '/uploads/';
        if($folderName) $save_path .= $folderName;
        $save_path .=  $fileName . '.' .strtolower($file->getClientOriginalExtension());
        $this->saveFile($disk , $file, $save_path);
       

    }
   
    
    
    
    
}