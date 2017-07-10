<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

function routesInDirectory($app = '') {
    $routeDir = app_path('Http/Routes/' . $app . ($app !== '' ? '/' : NULL));
    $iterator = new RecursiveDirectoryIterator($routeDir);
    $iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);

    foreach ($iterator as $route) {
        $isDotFile = strpos($route->getFilename(), '.') === 0;

        if (!$isDotFile && !$route->isDir()) {
            require $routeDir . $route->getFilename();
        }
    }
}

routesInDirectory();

/* MISCELLANEOUS ROUTES */
Route::get('webcast/getstatus', 'WebcastStatusController@getStatus');

Route::get('calendars/all', 'CalendarController@getAll');
Route::get('calendars/{slug}', 'CalendarController@get')->middleware(['doesExist:Mission']);

Route::get('faq/get', 'QuestionsController@get');
Route::get('faq','QuestionsController@index');

Route::get('admin', 'AdminController@index')->middleware(['mustBe:Administrator']);
Route::get('admin/clearcache', 'AdminController@clearcache')->middleware(['mustBe:Administrator']);

Route::get('newssummaries', 'NewsSummariesController@index');

// ABOUT Routes
Route::get('about', function() {
    return view('about');
});

Route::get('about/rulesandtermsofservice', function() {
    return view('about.rulesAndTermsOfService');
});

Route::get('about/docs', function() {
    return view('about.docs');
});

Route::get('about/contact', function() {
    return view('about.contact');
});