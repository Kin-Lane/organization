<?php
$route = '/organization/:organization_id/apis/';
$app->post($route, function ($organization_id)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($organization_id) && isset($param['api_id']))
		{

		$api_id = trim(mysql_real_escape_string($param['api_id']));

		$CheckTagQuery = "SELECT API_ID FROM company_api_pivot WHERE API_ID = " . $api_id . " AND Company_ID = " . $organization_id;
		//echo $CheckTagQuery . "<br />";
		$CheckTagResults = mysql_query($CheckTagQuery) or die('Query failed: ' . mysql_error());
		if($CheckTagResults && mysql_num_rows($CheckTagResults))
			{
			$API = mysql_fetch_assoc($CheckTagResults);
			}
		else
			{

			$query = "INSERT INTO company_api_pivot(API_ID,Company_ID) VALUES(" . $api_id . "," . $organization_id . ")";
			//echo $query . "<br />";
			mysql_query($query) or die('Query failed: ' . mysql_error());
			}
		$F = array();
		$F['api_id'] = $api_id;
		$F['organization_id'] = $organization_id;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
