<?php

Auth::routes();

//Route::get('/test', 'BillsController@test');

Route::get('/test', function(){
   
    $tran= \App\Tran::first();
    $signup=$tran->getSignup();
    dd($signup->bill->getAmountShorted());
});



//client routes
Route::get('/', '\App\Http\Controllers\Client\HomeController@index')->name('home');

Route::get('/about', '\App\Http\Controllers\Client\AboutController@index');
Route::get('/faq', '\App\Http\Controllers\Client\FaqController@index');
Route::resource('/centers', '\App\Http\Controllers\Client\CentersController',['only' => ['index','show']]);
Route::resource('/courses', '\App\Http\Controllers\Client\CoursesController',['only' => ['index','show']]);

//銀行回傳資料
Route::post('/bills', '\App\Http\Controllers\Client\BillsController@store');

Route::post('/bills', array('middleware' => 'cors', 'uses' => '\App\Http\Controllers\Client\BillsController@store'));

Route::group(['middleware' => 'auth'], function()
{
  
    Route::get('/user/profile', '\App\Http\Controllers\Client\UserController@show');
    Route::get('/user/edit', '\App\Http\Controllers\Client\UserController@edit');
    Route::put('/user', '\App\Http\Controllers\Client\UserController@update');

    Route::get('/user/password/change', '\App\Http\Controllers\Client\UserController@showChangePasswordForm');
    Route::post('/user/password/change', '\App\Http\Controllers\Client\UserController@changePassword');
    Route::resource('/signups', '\App\Http\Controllers\Client\SignupsController');
  
    Route::get('/bills/{id}', '\App\Http\Controllers\Client\BillsController@show');
    Route::get('/bills/{id}/print', '\App\Http\Controllers\Client\BillsController@print');
    Route::get('/bills/{id}/credit', '\App\Http\Controllers\Client\BillsController@credit');

    Route::get('/teacher', '\App\Http\Controllers\Client\TeacherController@show');
    Route::get('/teacher/edit', '\App\Http\Controllers\Client\TeacherController@edit');
    Route::put('/teacher', '\App\Http\Controllers\Client\TeacherController@update');

    Route::get('/students', '\App\Http\Controllers\Client\StudentsController@index');
    Route::post('/students/scores/update', '\App\Http\Controllers\Client\StudentsController@updateScores');

});

//end client routes





Route::get('/manage/login', 'SessionsController@create')->name('manage-login');
Route::post('/manage/login', 'SessionsController@store');
Route::post('manage/logout', 'SessionsController@destroy');

Route::group(['middleware' => 'admin'], function()
{
    Route::get('/manage', function () {
        $current='manage';
        $keys=[ 'CoursesAdmin','SignupsAdmin', 'TeachersAdmin','StudentsAdmin','UsersAdmin','MainSettings' ];
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
    Route::get('/manage/SeedUsers', 'UsersController@seed');
    Route::get('/manage/SeedSignups', 'SignupsController@seed');
    Route::get('/manage/seedPays', 'BillsController@seedPays');
    Route::get('/manage/seedQuits', 'QuitsController@seedQuits');
    Route::get('/manage/seedLessons', 'LessonsController@seedLessons');


    Route::resource('/manage/change-password', 'ChangePasswordController',['only' => ['index','store']]);
    
    Route::resource('/manage/users', 'UsersController');
    Route::post('/manage/users/find', 'UsersController@find');

    Route::resource('/manage/accounts', 'AccountsController');

    Route::resource('/manage/photoes', 'PhotoesController');

    Route::resource('/manage/contactInfoes', 'ContactInfoesController');

    Route::resource('/manage/terms', 'TermsController');

    Route::resource('/manage/admins', 'AdminsController');
    Route::post('/manage/admins/import', 'AdminsController@import');
    Route::post('/manage/admins/upload', 'AdminsController@upload');

    Route::resource('/manage/discounts', 'DiscountsController');
    Route::resource('/manage/identities', 'IdentitiesController');
    Route::resource('/manage/wages', 'WagesController');

    Route::resource('/manage/teachers', 'TeachersController');
    Route::post('/manage/teachers/import', 'TeachersController@import');
    Route::post('/manage/teachers/upload', 'TeachersController@upload');
    Route::post('/manage/teachers/review', 'TeachersController@review');

    Route::resource('/manage/volunteers', 'VolunteersController');
    Route::post('/manage/volunteers/import', 'VolunteersController@import');
    Route::post('/manage/volunteers/upload', 'VolunteersController@upload');

    Route::resource('/manage/teacherGroups', 'TeacherGroupsController');
    Route::get('/manage/teacherGroups/{id}/teachers', 'TeacherGroupsController@teachers');
    Route::get('/manage/teacherGroups/{id}/EditTeacher', 'TeacherGroupsController@editTeacher');
    Route::put('/manage/teacherGroups/{id}/AddTeachers', 'TeacherGroupsController@addTeachers');
    Route::put('/manage/teacherGroups/{id}/RemoveTeacher', 'TeacherGroupsController@removeTeacher');


    Route::resource('/manage/categories', 'CategoriesController');
    Route::post('/manage/categories/importances', 'CategoriesController@importances');
    Route::post('/manage/categories/import', 'CategoriesController@import');
    Route::post('/manage/categories/upload', 'CategoriesController@upload');

    Route::resource('/manage/centers', 'CentersController');
    Route::post('/manage/centers/importances', 'CentersController@importances');
    Route::post('/manage/centers/import', 'CentersController@import');
    Route::post('/manage/centers/upload', 'CentersController@upload');

   
    // Courses
    Route::resource('/manage/courses', 'CoursesController');
    Route::get('/manage/courses/{id}/EditInfo', 'CoursesController@editInfo');
    Route::put('/manage/courses/{id}/UpdateInfo', 'CoursesController@updateInfo');
    Route::post('/manage/courses/import', 'CoursesController@import');
    Route::post('/manage/courses/upload', 'CoursesController@upload');
    Route::post('/manage/courses/review', 'CoursesController@review');
    Route::post('/manage/courses/active', 'CoursesController@active');
    Route::put('/manage/courses/{id}/shutdown', 'CoursesController@shutdown');

    Route::resource('/manage/ClassTimes', 'ClassTimesController');
    Route::post('/manage/ClassTimes/import', 'ClassTimesController@import');

    Route::resource('/manage/processes', 'ProcessesController');
    Route::post('/manage/processes/import', 'ProcessesController@import');


    //Signups
    Route::get('/manage/signups/report', 'SignupsController@report');
    Route::get('/manage/signups/courses', 'SignupsController@fetchCourses');
    Route::resource('/manage/signups', 'SignupsController');
    Route::post('/manage/signups/updatePS', 'SignupsController@updatePS');

    Route::get('/manage/bills/{id}/print', 'BillsController@print');

    Route::resource('/manage/pays', 'PaysController');

    Route::resource('/manage/students', 'StudentsController');
    Route::post('/manage/students/scores/update', 'StudentsController@updateScores');

   
    Route::get('/manage/trans/courses', 'TransController@fetchCourses');
    Route::resource('/manage/trans', 'TransController');

    Route::resource('/manage/notices', 'NoticesController');

    Route::resource('/manage/files', 'FilesController'); 
    Route::get('/manage/files/download/{name}', 'FilesController@download');
    
    Route::resource('/manage/quits', 'QuitsController');
    Route::post('/manage/quits/updateStatuses', 'QuitsController@updateStatuses');
    Route::post('/manage/quits/updatePS', 'QuitsController@updatePS');

    Route::get('/manage/reports/courses', 'ReportsController@courses');
    Route::get('/manage/reports/quits', 'ReportsController@quits');

    Route::resource('/manage/lessons', 'LessonsController');
    Route::post('/manage/lessons/init', 'LessonsController@init');
    Route::post('/manage/lessons/review', 'LessonsController@review');
    Route::post('/manage/lessons/updateMember', 'LessonsController@updateMember');

    Route::resource('/manage/payrolls', 'PayrollsController');
    Route::post('/manage/payrolls/review', 'PayrollsController@review');
    Route::post('/manage/payrolls/finish', 'PayrollsController@finish');
    Route::post('/manage/payrolls/updatePS', 'PayrollsController@updatePS');
    Route::post('/manage/payrolls/updateStatus', 'PayrollsController@updateStatus');

   

});
