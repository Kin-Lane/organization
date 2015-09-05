<?php
$route = '/organization/:organization_id/logs/';
$app->get($route, function ($organization_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$organization_id = prepareIdIn($organization_id,$host);

	$ReturnObject = array();

	$Query = "SELECT * FROM company_log al";
	$Query .= " WHERE al.Company_ID = " . $organization_id;

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$log_id = $Database['Company_Log_ID'];
		$details = $Database['About'];
		$pattern = '/[^\w ]+/';
		$replacement = '';
		$about = preg_replace($pattern, $replacement, $about);

		$Type = $Database['Type'];
		$log_date = $Database['Log_Date'];

		$log_id = prepareIdOut($log_id,$host);

		$F = array();
		$F['log_id'] = $log_id;
		$F['type'] = $Type;
		$F['details'] = $details;
		$F['log_date'] = $log_date;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
