<?php
$route = '/organization/:organization_id/buildingblocks/';
$app->post($route, function ($organization_id)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($organization_id) && isset($param['building_block_id']))
		{

		$building_block_id = trim(mysql_real_escape_string($param['building_block_id']));

		if(isset($param['tools_id']))
			{
			$tools_id = trim(mysql_real_escape_string($param['tools_id']));
			}
		else
			{
			$tools_id = 0;
			}

		if(isset($param['url']))
			{
			$url = trim(mysql_real_escape_string($param['url']));
			}
		else
			{
			$url = "";
			}

		$query = "INSERT INTO company_building_block_pivot(Company_ID,Building_Block_ID,Tools_ID,URL) VALUES(" . $organization_id . "," . $building_block_id . "," . $tools_id . ",'" . $url . "'); ";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		$buildingblock_ID = mysql_insert_id();

		$F = array();
		$F['building_block_id'] = $building_block_id;
		$F['tools_id'] = $tools_id;
		$F['url'] = $url;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
