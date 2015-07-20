<?php
$route = '/organization/:organization_id/urls/:url_id';
$app->delete($route, function ($organization_id,$url_ID)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	$DeleteQuery = "DELETE FROM company_url WHERE Company_URL_ID = " . trim($url_ID);
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$F = array();
	$F['url_id'] = $url_ID;
	$F['type'] = '';
	$F['url'] = '';

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
