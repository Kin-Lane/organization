<?php
$route = '/organization/:organization_id/tags/';
$app->get($route, function ($organization_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$organization_id = prepareIdIn($organization_id,$host);

	$ReturnObject = array();

	$Query = "SELECT t.Tag_ID, t.Tag, count(*) AS Company_Count from tags t";
	$Query .= " JOIN company_tag_pivot ctp ON t.Tag_ID = ctp.Tag_ID";
	$Query .= " WHERE ctp.Company_ID = " . $organization_id;
	$Query .= " GROUP BY t.Tag ORDER BY count(*) DESC";

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$tag_id = $Database['Tag_ID'];
		$tag = $Database['Tag'];
		$organization_count = $Database['Company_Count'];

		$tag_id = prepareIdOut($tag_id,$host);	

		$F = array();
		$F['tag_id'] = $tag_id;
		$F['tag'] = $tag;
		$F['organization_count'] = $organization_count;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
