<?php
$route = '/organization/:organization_id/tools/:tool_id';
$app->delete($route, function ($organization_id,$tool_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$organization_id = prepareIdIn($organization_id,$host);
	$tool_id = prepareIdIn($tool_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	$DeleteQuery = "DELETE FROM company_tool_pivot WHERE Tools_ID = " . $tool_id . " AND Company_ID = " . $organization_id;
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$tool_id = prepareIdOut($tool_id,$host);

	$F = array();
	$F['tools_id'] = $tool_id;
	$F['url'] = '';

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
