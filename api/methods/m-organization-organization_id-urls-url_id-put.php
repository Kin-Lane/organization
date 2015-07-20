<?php
$route = '/organization/:organization_id/urls/:url_id';
$app->put($route, function ($organization_id,$URL_ID)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['type']) && isset($param['url']))
		{

		$type = trim(mysql_real_escape_string($param['type']));
		$url = trim(mysql_real_escape_string($param['url']));
		$name = trim(mysql_real_escape_string($param['name']));

		$query = "UPDATE company_urls SET Type = '" . $type . "', URL = '" . $url . "', Name = '" . $name . "' WHERE Company_URL_ID = " . $URL_ID;
		mysql_query($query) or die('Query failed: ' . mysql_error());
		$url_ID = mysql_insert_id();

		$F = array();
		$F['url_id'] = $URL_ID;
		$F['type'] = $type;
		$F['url'] = $url;
		$F['name'] = $name;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
