<?php
$route = '/organization/:organization_id/buildingblocks/:buildingblock_id';
$app->put($route, function ($organization_id,$building_block_id)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($organization_id) && isset($param['building_block_id']))
		{

		$organization_id = trim(mysql_real_escape_string($param['organization_id']));
		$building_block_id = trim(mysql_real_escape_string($param['building_block_id']));

		$tools_id = trim(mysql_real_escape_string($param['tools_id']));
		if($tools_id==''){ $tools_id = 0; }

		$url = trim(mysql_real_escape_string($param['url']));

		$query = "UPDATE company_building_block_pivot SET";
		$query .= "  Tools_ID = " . $tools_id . ",";
		$query .= "  URL = '" . $url . "'";
		$query .= "  WHERE Company_ID = " . $organization_id . " AND Building_Block_ID = " . $building_block_id;

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
