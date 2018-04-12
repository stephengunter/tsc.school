<?php

Auth::routes();

Route::get('/test', function () {
   
    $model=[
        'title' => '',
        'topMenus' => [
            'current' => '',
            'user' => \App\User::find(54),
            'teacher' => false,
            'students' => false
        ],
        'user' => \App\User::find(54),

       
    ];

    

    return view('client.signups.edit')->with($model);
});

//client routes
Route::get('/', '\App\Http\Controllers\Client\HomeController@index')->name('home');

Route::get('/about', '\App\Http\Controllers\Client\AboutController@index');
Route::get('/faq', '\App\Http\Controllers\Client\FaqController@index');
Route::resource('/centers', '\App\Http\Controllers\Client\CentersController',['only' => ['index','show']]);
Route::resource('/courses', '\App\Http\Controllers\Client\CoursesController',['only' => ['index','show']]);

Route::group(['middleware' => 'auth'], function()
{
  
    Route::get('/user/profile', '\App\Http\Controllers\Client\UserController@show');
    Route::get('/user/edit', '\App\Http\Controllers\Client\UserController@edit');
    Route::put('/user', '\App\Http\Controllers\Client\UserController@update');

    Route::get('/user/password/change', '\App\Http\Controllers\Client\UserController@showChangePasswordForm');
    Route::post('/user/password/change', '\App\Http\Controllers\Client\UserController@changePassword');
    Route::resource('/signups', '\App\Http\Controllers\Client\SignupsController');

});

//end client routes





Route::get('/manage/login', 'SessionsController@create')->name('manage-login');
Route::post('/manage/login', 'SessionsController@store');
Route::post('manage/logout', 'SessionsController@destroy');

Route::group(['middleware' => 'admin'], function()
{
    Route::get('/manage', function () {
        $current='manage';
        $keys=[ 'CoursesAdmin','SignupsAdmin','UsersAdmin','MainSettings' ];
        $systems=[];
    
        foreach ($keys as $key) {
          
            $item=[
                'key' => $key,
                'menus' => \App\Services\Menus::adminMenus($current,$key)
            ];
            array_push($systems, $item);
            
        }
        
        return view('manage')->with([ 'systems' => $systems ]);
    
    });

    //test
    Route::get('/manage/SeedDiscountCenters', 'CentersController@seedDiscountCenters');
    Route::get('/manage/SeedSignups', 'SignupsController@seed');
    Route::get('/manage/SeedBills', 'SignupsController@seedBills');

    Route::get('/manage/SeedContents', function () {
       
    
    });


    Route::resource('/manage/change-password', 'ChangePasswordController',['only' => ['index','store']]);
    
    Route::resource('/manage/users', 'UsersController');
    Route::post('/manage/users/find', 'UsersController@find');

    Route::resource('/manage/terms', 'TermsController');

    Route::resource('/manage/admins', 'AdminsController');
    Route::post('/manage/admins/import', 'AdminsController@import');

    Route::resource('/manage/teachers', 'TeachersController');
    Route::post('/manage/teachers/import', 'TeachersController@import');
    Route::post('/manage/teachers/review', 'TeachersController@review');

    Route::resource('/manage/teacherGroups', 'TeacherGroupsController');
    Route::get('/manage/teacherGroups/{id}/teachers', 'TeacherGroupsController@teachers');
    Route::get('/manage/teacherGroups/{id}/EditTeacher', 'TeacherGroupsController@editTeacher');
    Route::put('/manage/teacherGroups/{id}/AddTeachers', 'TeacherGroupsController@addTeachers');
    Route::put('/manage/teacherGroups/{id}/RemoveTeacher', 'TeacherGroupsController@removeTeacher');


    Route::resource('/manage/categories', 'CategoriesController');
    Route::post('/manage/categories/importances', 'CategoriesController@importances');
    Route::post('/manage/categories/import', 'CategoriesController@import');

    Route::resource('/manage/centers', 'CentersController');
    Route::post('/manage/centers/importances', 'CentersController@importances');
    Route::post('/manage/centers/import', 'CentersController@import');

   
    // Courses
    Route::resource('/manage/courses', 'CoursesController');
    Route::get('/manage/courses/{id}/EditInfo', 'CoursesController@editInfo');
    Route::put('/manage/courses/{id}/UpdateInfo', 'CoursesController@updateInfo');
    Route::post('/manage/courses/import', 'CoursesController@import');
    Route::post('/manage/courses/review', 'CoursesController@review');
    Route::post('/manage/courses/active', 'CoursesController@active');

    Route::resource('/manage/ClassTimes', 'ClassTimesController');
    Route::post('/manage/ClassTimes/import', 'ClassTimesController@import');

    Route::resource('/manage/processes', 'ProcessesController');
    Route::post('/manage/processes/import', 'ProcessesController@import');


    //Signups
    Route::get('/manage/signups/report', 'SignupsController@report');
    Route::resource('/manage/signups', 'SignupsController');

    Route::resource('/manage/students', 'StudentsController');


    Route::resource('/manage/notices', 'NoticesController');


    
    Route::resource('/manage/contactInfoes', 'ContactInfoesController');

});
