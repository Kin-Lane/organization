<?php
$route = '/organization/:organization_id/locations/:location_id';
$app->delete($route, function ($organization_id,$location_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$organization_id = prepareIdIn($organization_id,$host);
	$location_id = prepareIdIn($location_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	$DeleteQuery = "DELETE FROM company_location WHERE ID = " . $location_id;
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$location_id = prepareIdOut($location_id,$host);

	$F = array();
	$F['location_id'] = $location_id;

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
