<?php
$route = '/organization/:organization_id/buildingblocks/:building_block_id';
$app->delete($route, function ($organization_id,$building_block_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$organization_id = prepareIdIn($organization_id,$host);
	$building_block_id = prepareIdIn($building_block_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	$DeleteQuery = "DELETE FROM company_building_block_pivot WHERE Building_Block_ID = " . $building_block_id . " AND Company_ID = " . $organization_id;
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$building_block_id = prepareIdOut($building_block_id,$host);
	$organization_id = prepareIdOut($organization_id,$host);

	$F = array();
	$F['organization_id'] = $organization_id;
	$F['building_block_id'] = $building_block_id;
	$F['url'] = '';

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
