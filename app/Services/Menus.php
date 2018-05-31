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
                 [
                    'text' => '折扣設定',
                    'path' => '/manage/discounts',
                    'active' => $current=='manage/discounts'
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
                        'text' => '權限管理',
                        'path' => '/manage/admins',
                        'active' => $current=='manage/admins'
                    ],
                    [
                        'text' => '志工管理',
                        'path' => '/manage/volunteers',
                        'active' => $current=='manage/volunteers'
                    ],
                    [
                        'text' => '身分管理',
                        'path' => '/manage/identities',
                        'active' => $current=='manage/identities'
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
                        'text' => '課堂紀錄',
                        'path' => '/manage/lessons',
                        'active' => $current=='manage/lessons'
                    ],
                    
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
                        'text' => '學員統計',
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
        if($key=='TeachersAdmin'){
            return array(
                    [
                        'text' => '教師管理',
                        'path' => '/manage/teachers',
                        'active' => $current =='manage/teachers'
                    ],
                    [
                        'text' => '薪酬標準',
                        'path' => '/manage/wages',
                        'active' => $current=='manage/wages'
                    ],
                    [
                        'text' => '教師鐘點費',
                        'path' => '/manage/payrolls',
                        'active' => $current=='manage/payrolls'
                    ],
                );
        } 

        if($key=='StudentsAdmin'){
            return array(
                    [
                        'text' => '學員管理',
                        'path' => '/manage/students',
                        'active' => $current=='manage/students'
                    ],
                    [
                        'text' => '轉班紀錄',
                        'path' => '/manage/trans',
                        'active' => $current=='manage/trans'
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
        if($key=='MyReports'){
            return array(
                    [
                        'text' => '我的報表',
                        'path' => '/manage/my-reports',
                        'active' => $current =='manage/my-reports'
                    ],
                );
        } 
    }
    
    
}