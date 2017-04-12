<?php
Route::group(['prefix' => 'missions'], function() {
    Route::get('/future', 'MissionsController@allFutureMissions');
    Route::get('/past', 'MissionsController@allPastMissions');
    Route::get('/next', 'MissionsController@getNextMission');
	
    Route::get('/all', 'MissionsController@all');

    Route::group(['middleware' => ['mustBe:Administrator']], function() {
        Route::get('/create', 'MissionsController@getCreate');
        Route::post('/create', 'MissionsController@postCreate');
		Route::get('/order', 'MissionsController@allFutureMissionsToOrder');
		
        Route::get('/{slug}/edit', 'MissionsController@getEdit')->middleware('doesExist:Mission');
        Route::patch('/{slug}/edit', 'MissionsController@patchEdit')->middleware('doesExist:Mission');
		Route::post('/editMissionOrder', 'MissionsController@editMissionOrder');
    });

    Route::group(['middleware' => ['doesExist:Mission']], function() {

        Route::get('/{slug}', 'MissionsController@get');
        Route::get('/{slug}/launchdatetime', 'MissionsController@launchdatetime');
        Route::get('/{slug}/telemetry', 'MissionsController@telemetry');
        Route::get('/{slug}/launchevents', 'MissionsController@launchEvents');

        Route::group(['middleware' => ['mustBe:Subscriber']], function() {
            Route::get('/{slug}/orbitalelements', 'MissionsController@orbitalElements');
            Route::get('/{slug}/raw', 'MissionsController@raw');
        });
    });
});
