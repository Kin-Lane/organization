<?php
$route = '/organization/:organization_id/tags/:tag';
$app->delete($route, function ($organization_id,$tag)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($tag))
		{

		$tag = trim(mysql_real_escape_string($tag));

		$CheckTagQuery = "SELECT Tag_ID FROM tags where Tag = '" . $tag . "'";
		$CheckTagResults = mysql_query($CheckTagQuery) or die('Query failed: ' . mysql_error());
		if($CheckTagResults && mysql_num_rows($CheckTagResults))
			{
			$Tag = mysql_fetch_assoc($CheckTagResults);
			$tag_id = $Tag['Tag_ID'];

			$DeleteQuery = "DELETE FROM company_tag_pivot WHERE Tag_ID = " . trim($tag_id) . " AND Company_ID = " . trim($organization_id);
			$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());
			}

		$F = array();
		$F['tag_id'] = $tag_id;
		$F['tag'] = $tag;
		$F['company_count'] = 0;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
