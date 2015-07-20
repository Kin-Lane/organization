<?php
$route = '/organization/:organization_id/images/';
$app->get($route, function ($organization_id)  use ($app){

	$ReturnObject = array();

	$Query = "SELECT * from company_image ai";
	$Query .= " WHERE ai.Company_ID = " . $organization_id;

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$image_id = $Database['Company_Image_ID'];
		$type = $Database['Type'];
		$path = $Database['Image_Path'];
		$name = $Database['Image_Name'];

		$F = array();
		$F['image_id'] = $image_id;
		$F['type'] = $type;
		$F['path'] = $path;
		$F['name'] = $name;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
