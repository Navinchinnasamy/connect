<?php
$app->group(['prefix' => 'api/v1'], function ($app) {
	$app->post('/userCheck','EmployeeService@userCheck');
	$app->post('/login','EmployeeService@login');
});

$app->group(['prefix' => 'api/v1', 'middleware' => ['tokenBefore', 'tokenAfter']], function ($app) {
	$app->get('/', function () use ($app) {
	    return response()->json([
	    	'message' => 'Employee portal Api V1',
	    	'code' => 200,
	    	'data' => [
	    		'version' => 'v1'
	    	]
	    ]);
	});

	$app->post('/getGroupPosts','PostService@getGroupPosts');
	$app->post('/changePassword','EmployeeService@changePassword');
	$app->post('/forgotPassword','EmployeeService@forgotPassword');
	$app->post('/getProfileDetails','EmployeeService@getProfileDetails');
	$app->post('/profileDetailsUpdate','EmployeeService@profileDetailsUpdate');
	$app->post('/profileFilter','EmployeeService@profileFilter');
	$app->post('/userToGroup','EmployeeService@userToGroup');
	$app->post('/getAllGroup','EmployeeService@getAllGroup');
	$app->post('/postDetails','PostService@postDetails');
	$app->post('/saveComment', 'PostService@comment');
	$app->post('/saveLikes', 'PostService@saveLikes');
	$app->post('/profilePictureUpdate', 'EmployeeService@profilePictureUpdate');
	$app->get('/getEvents', 'PostService@getEvents');
});
