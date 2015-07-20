<?php
$route = '/organization/:organization_id/tools/';
$app->get($route, function ($organization_id)  use ($app){

	$ReturnObject = array();

	$Query = "SELECT * FROM tools t";
	$Query .= " JOIN company_tool_pivot ctp ON t.Tools_ID = ctp.Tools_ID";
	$Query .= " WHERE ctp.Company_ID = " . $organization_id;

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$tool_id = $Database['Tools_ID'];
		$name = $Database['Name'];
		$details = $Database['Details'];
		$details = scrub($details);
		$post_date = $Database['Post_Date'];

		$F = array();
		$F['tool_id'] = $tool_id;
		$F['name'] = $name;
		$F['details'] = $details;
		$F['post_date'] = $post_date;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
