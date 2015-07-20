<?php
$route = '/organization/:organization_id/notes/';
$app->get($route, function ($organization_id)  use ($app){

	$ReturnObject = array();

	$Query = "SELECT ID,Company_ID,Type,Note From company_notes cn";
	$Query .= " WHERE cn.Company_ID = " . $organization_id;

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$Note_ID = $Database['ID'];
		$Type = $Database['Type'];
		$Note = $Database['Note'];

		$F = array();
		$F['note_id'] = $Note_ID;
		$F['type'] = $Type;
		$F['note'] = $Note;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
