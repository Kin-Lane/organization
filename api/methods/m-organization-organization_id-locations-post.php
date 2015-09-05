<?php
$route = '/organization/:organization_id/locations/';
$app->post($route, function ($organization_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$organization_id = prepareIdIn($organization_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['description']))
		{

		if(isset($param['description'])){ $description = trim(mysql_real_escape_string($param['description'])); }
		if(isset($param['address1'])){ $address1 = trim(mysql_real_escape_string($param['address1'])); } else { $address1 = ''; }
		if(isset($param['address2'])){ $address2 = trim(mysql_real_escape_string($param['address2'])); } else { $address2 = ''; }
		if(isset($param['city'])){ $city = trim(mysql_real_escape_string($param['city'])); } else { $city = ''; }
		if(isset($param['state_code'])){ $state_code = trim(mysql_real_escape_string($param['state_code'])); } else { $state_code = ''; }
		if(isset($param['zip_code'])){ $zip_code = trim(mysql_real_escape_string($param['zip_code'])); } else { $zip_code = ''; }
		if(isset($param['country_code'])){ $country_code = trim(mysql_real_escape_string($param['country_code'])); } else { $country_code = ''; }
		if(isset($param['latitude'])){ $latitude = trim(mysql_real_escape_string($param['latitude'])); } else { $latitude = ''; }
		if(isset($param['longitude'])){ $longitude = trim(mysql_real_escape_string($param['longitude'])); } else { $longitude = ''; }

		$Query = "INSERT INTO company_location(";
		$Query .= "Company_ID,";
		$Query .= "description,";

		if($address1!='') { $Query .= "address1,"; }
		if($address2!='') { $Query .= "address2,"; }
		if($city!='') { $Query .= "city,"; }
		if($state_code!='') { $Query .= "state_code,"; }
		if($zip_code!='') { $Query .= "zip_code,"; }
		if($country_code!='') { $Query .= "country_code,"; }
		if($latitude!='') { $Query .= "latitude,"; }
		if($longitude!='') { $Query .= "longitude,"; }

		$Query .= "Closing";
		$Query .= ") VALUES(";
		$Query .= mysql_real_escape_string($organization_id) . ",";
		$Query .= "'" . mysql_real_escape_string($description) . "',";

		if($address1!='') { $Query .= "'" . mysql_real_escape_string($address1) . "',"; }
		if($address2!='') { $Query .= "'" . mysql_real_escape_string($address2) . "',"; }
		if($city!='') { $Query .= "'" . mysql_real_escape_string($city) . "',"; }
		if($state_code!='') { $Query .= "'" . mysql_real_escape_string($state_code) . "',"; }
		if($zip_code!='') { $Query .= "'" . mysql_real_escape_string($zip_code) . "',"; }
		if($country_code!='') { $Query .= "'" . mysql_real_escape_string($country_code) . "',"; }
		if($latitude!='') { $Query .= "'" . mysql_real_escape_string($latitude) . "',"; }
		if($longitude!='') { $Query .= "'" . mysql_real_escape_string($longitude) . "',"; }

		$Query .= "'nothing'";

		$Query .= ")";

		//echo $Query . "<br />";
		mysql_query($Query) or die('Query failed: ' . mysql_error());
		$location_id = mysql_insert_id();

		$location_id = prepareIdOut($location_id,$host);

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
