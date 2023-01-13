<?php
############################## Clubs ##############################
Route::get('clubs/dictionary', 'Clubs\DictionaryController');
Route::get('clubs/activities', 'Clubs\ActivityController@activities');
Route::get('clubs/club/{id}', 'Clubs\ClubController@show')->where('id', '\d*');
Route::get('clubs/organization/{id}', 'Clubs\OrganizationController@show')->where('id', '\d*');
Route::post('clubs/groups/sign-up', 'Clubs\GroupController@groupSignUp');
