<?php

namespace App\Services;


class Menus 
{
    

    public static function adminMenus($current,$key)
    {
        
        if($key=='MainSettings'){
            return array(
                 [
                    'text' => '開課中心',
                    'path' => '/manage/centers',
                    'active' => $current =='manage/centers'
                 ],
                 [
                    'text' => '課程分類',
                    'path' => '/manage/categories',
                    'active' => $current=='manage/categories'
                 ],
                 [
                    'text' => '學期設定',
                    'path' => '/manage/terms',
                    'active' => $current=='manage/terms'
                 ],
                 
             );
        } 
        
        if($key=='UsersAdmin'){
            return array(
                    [
                        'text' => '使用者管理',
                        'path' => '/manage/users',
                        'active' => $current =='manage/users'
                    ],
                    [
                        'text' => '教師管理',
                        'path' => '/manage/teachers',
                        'active' => $current=='manage/teachers'
                    ],
                    [
                        'text' => '權限管理',
                        'path' => '/manage/admins',
                        'active' => $current=='manage/admins'
                    ],
                    [
                        'text' => '志工管理',
                        'path' => '/manage/volunteers',
                        'active' => $current=='manage/volunteers'
                    ],
                    
                );
        } 
        if($key=='CoursesAdmin'){
            return array(
                    [
                        'text' => '課程管理',
                        'path' => '/manage/courses',
                        'active' => $current =='manage/courses'
                    ],
                    [
                        'text' => '學員管理',
                        'path' => '/manage/students',
                        'active' => $current=='manage/students'
                    ]
                );
        } 
        if($key=='SignupsAdmin'){
            return array(
                    [
                        'text' => '報名管理',
                        'path' => '/manage/signups',
                        'active' => $current =='manage/signups'
                    ],
                    [
                        'text' => '報名統計',
                        'path' => '/manage/signups/report',
                        'active' => $current=='manage/signups/report'
                    ],
                    [
                        'text' => '退費管理',
                        'path' => '/manage/quits',
                        'active' => $current =='manage/quits'
                    ],
                );
        } 
        if($key=='Reports'){
            return array(
                    [
                        'text' => '課程清單',
                        'path' => '/manage/reports/courses',
                        'active' => $current =='manage/reports/courses'
                    ],
                );
        } 
        if($key=='HomePageAdmin'){
            return array(
                    [
                        'text' => '公告管理',
                        'path' => '/manage/notices',
                        'active' => $current =='manage/notices'
                    ],
                );
        } 
    }
    
    
}