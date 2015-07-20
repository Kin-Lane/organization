<?php
$route = '/organization/:organization_id/images/:image_id';
$app->delete($route, function ($organization_id,$image_id)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	$DeleteQuery = "DELETE FROM company_image WHERE Company_Image_ID = " . $image_id;
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$F = array();
	$F['image_id'] = $image_id;
	$F['type'] = "";
	$F['path'] = "";
	$F['name'] = "";

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
