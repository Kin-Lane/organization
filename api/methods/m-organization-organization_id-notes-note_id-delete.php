<?php

$route = '/organization/:organization_id/notes/:note_id';
$app->delete($route, function ($organization_id,$note_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$organization_id = prepareIdIn($organization_id,$host);
	$note_id = prepareIdIn($note_id,$host);

	$ReturnObject = array();

	$DeleteQuery = "DELETE FROM company_notes WHERE ID = " . trim(mysql_real_escape_string($note_id)) . " AND Company_ID = " . trim(mysql_real_escape_string($organization_id));
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$note_id = prepareIdOut($note_id,$host);

	$F = array();
	$F['note_id'] = $note_id;
	$F['type'] = '';
	$F['note'] = '';

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});

?>
