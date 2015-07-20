<?php
$route = '/organization/:organization_id/locations/';
$app->get($route, function ($organization_id)  use ($app){

	$ReturnObject = array();

	$Query = "SELECT * from company_location cl";
	$Query .= " WHERE cl.Company_ID = " . $organization_id;
	//echo $Query . "<br />";
	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$location_id = $Database['ID'];
		$description = $Database['description'];
		$address1 = $Database['address1'];
		$address2 = $Database['address2'];
		$city = $Database['city'];
		$state_code = $Database['state_code'];
		$zip_code = $Database['zip_code'];
		$country_code = $Database['country_code'];
		$latitude = $Database['latitude'];
		$longitude = $Database['longitude'];

		$F = array();
		$F['location_id'] = $location_id;
		$F['description'] = $description;
		$F['address1'] = $address1;
		$F['address2'] = $address2;
		$F['city'] = $city;
		$F['state_code'] = $state_code;
		$F['zip_code'] = $zip_code;
		$F['country_code'] = $country_code;
		$F['latitude'] = $latitude;
		$F['longitude'] = $longitude;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
