<?php
Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function() {

    // Registrations
    //Route::get('/signup', 'AuthController@getSignUp');
    //Route::post('/signup', 'AuthController@postSignUp');

    // Login
    Route::get('/login', 'AuthController@getLogin');
    Route::post('/login', 'AuthController@postLogin');

    // Logout
    Route::post('/logout', 'AuthController@logout');

    // Email Verification
    //Route::get('/verify/{userId}/{key}', 'AuthController@verify');

    // Forgot & Reset Password Functionality
    Route::get('/forgotpassword', 'PasswordController@getForgotPassword');
    Route::post('/forgotpassword', 'PasswordController@postForgotPassword');

    Route::get('/resetpassword/{userId}/{key}', 'PasswordController@getResetPassword');
    Route::post('/resetpassword/{userId}/{key}', 'PasswordController@postResetPassword');

    Route::get('/isusernametaken/{username}', 'AuthController@isUsernameTaken');
});
