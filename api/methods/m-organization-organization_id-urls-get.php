<?php
$route = '/organization/:organization_id/urls/';
$app->get($route, function ($organization_id)  use ($app){

	$ReturnObject = array();

	$Query = "SELECT * from company_url cn";
	$Query .= " WHERE cn.Company_ID = " . $organization_id;

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$url_ID = $Database['Company_URL_ID'];
		$type = $Database['Type'];
		$url = $Database['URL'];
		$name = $Database['Name'];

		$F = array();
		$F['url_id'] = $url_ID;
		$F['type'] = $type;
		$F['url'] = $url;
		$F['name'] = $name;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
