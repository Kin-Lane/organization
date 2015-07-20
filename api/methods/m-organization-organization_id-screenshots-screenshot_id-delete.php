<?php
$route = '/organization/:organization_id/screenshots/:screenshot_id';
$app->delete($route, function ($organization_id,$screenshot_id)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	$DeleteQuery = "DELETE FROM company_screenshot WHERE ID = " . $screenshot_id;
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$F = array();
	$F['screenshot_id'] = $screenshot_id;

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
