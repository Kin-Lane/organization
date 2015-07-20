<?php
$route = '/organization/:organization_id/screenshots/';
$app->get($route, function ($organization_id)  use ($app){


	$ReturnObject = array();

	$Query = "SELECT * FROM company_screenshot cs";
	$Query .= " WHERE cs.Company_ID = " . $organization_id;

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$screenshot_id = $Database['ID'];
		$path = $Database['Image_URL'];
		$name = $Database['Image_Name'];
		$type = $Database['Type'];

		$F = array();
		$F['screenshot_id'] = $screenshot_id;
		$F['name'] = $name;
		$F['path'] = $path;
		$F['type'] = $type;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
