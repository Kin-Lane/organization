<?php
$route = '/organization/:organization_id/tools/:tool_id';
$app->delete($route, function ($organization_id,$tool_id)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	$DeleteQuery = "DELETE FROM company_tool_pivot WHERE Tools_ID = " . $tool_id . " AND Company_ID = " . $organization_id;
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$F = array();
	$F['building_block_id'] = $tool_id;
	$F['tools_id'] = 0;
	$F['url'] = '';

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
