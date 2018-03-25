<?php

Route::get('/manage/login', 'SessionsController@create')->name('manage-login');
Route::post('/manage/login', 'SessionsController@store');
Route::post('manage/logout', 'SessionsController@destroy');

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

})->middleware('admin');;

//Auth::routes();

Route::group(['middleware' => 'admin'], function()
{
    Route::resource('/manage/change-password', 'ChangePasswordController',['only' => ['index','store']]);
    
    Route::resource('/manage/users', 'UsersController');
    Route::post('/manage/users/find', 'UsersController@find');

    Route::resource('/manage/terms', 'TermsController');

    Route::resource('/manage/admins', 'AdminsController');
    Route::post('/manage/admins/import', 'AdminsController@import');

    Route::resource('/manage/teachers', 'TeachersController');
    Route::post('/manage/teachers/import', 'TeachersController@import');


    Route::resource('/manage/categories', 'CategoriesController');
    Route::post('/manage/categories/importances', 'CategoriesController@importances');
    Route::post('/manage/categories/import', 'CategoriesController@import');

    Route::resource('/manage/centers', 'CentersController');
    Route::post('/manage/centers/importances', 'CentersController@importances');
    Route::post('/manage/centers/import', 'CentersController@import');

    Route::resource('/manage/contactInfoes', 'ContactInfoesController');

});
