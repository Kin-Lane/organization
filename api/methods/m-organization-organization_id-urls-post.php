<?php
$route = '/organization/:organization_id/urls/';
$app->post($route, function ($organization_id)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['type']) && isset($param['url']))
		{

		$type = trim(mysql_real_escape_string($param['type']));
		$url = trim(mysql_real_escape_string($param['url']));
		$name = trim(mysql_real_escape_string($param['name']));

		$query = "INSERT INTO company_url(Company_ID,Type,URL,Name) VALUES(" . $organization_id . ",'" . $type . "','" . $url . "','" . $name . "')";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		$url_id = mysql_insert_id();

		$F = array();
		$F['url_id'] = $url_id;
		$F['type'] = $type;
		$F['url'] = $url;
		$F['name'] = $name;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
