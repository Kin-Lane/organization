<?php
$route = '/organization/:organization_id/';
$app->delete($route, function ($organization_id) use ($app){

	$Add = 1;
	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	$query = "DELETE FROM company WHERE Company_ID = " . $organization_id;
	//echo $query . "<br />";
	mysql_query($query) or die('Query failed: ' . mysql_error());

	$F = array();
	$F['organization_id'] = $organization_id;

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
