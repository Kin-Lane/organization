<?php
$route = '/organization/:organization_id/apis/:api_id';
$app->delete($route, function ($organization_id,$api_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$organization_id = prepareIdIn($organization_id,$host);
	$api_id = prepareIdIn($api_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	$DeleteQuery = "DELETE FROM company_api_pivot WHERE API_ID = " . $api_id . " AND Company_ID = " . $organization_id;
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$api_id = prepareIdOut($api_id,$host);
	$organization_id = prepareIdOut($organization_id,$host);

	$api_id = prepareIdOut($api_id,$host);

	$F = array();
	$F['api_id'] = $api_id;
	$F['organization_id'] = $organization_id;

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
