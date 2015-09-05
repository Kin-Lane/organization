<?php
$route = '/organization/:organization_id/tools/';
$app->post($route, function ($organization_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$organization_id = prepareIdIn($organization_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['tool_id']))
		{

		$tool_id = trim(mysql_real_escape_string($param['tool_id']));

		if(isset($param['url'])){ $url = trim(mysql_real_escape_string($param['url'])); } else { $url = ''; }

		$query = "INSERT INTO company_tool_pivot(Company_ID,Tools_ID,URL) VALUES(" . $organization_id . "," . $tool_id . ",'" . $url . "'); ";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		$tool_id = mysql_insert_id();

		$tool_id = prepareIdOut($tool_id,$host);

		$F = array();
		$F['tool_id'] = $tool_id;
		$F['url'] = $url;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
