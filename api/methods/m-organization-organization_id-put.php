<?php
$route = '/organization/:organization_id/';
$app->put($route, function ($organization_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$organization_id = prepareIdIn($organization_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['name'])){ $name = trim(mysql_real_escape_string($param['name'])); } else { $name = ''; }
	if(isset($param['details'])){ $details = trim(mysql_real_escape_string($param['details'])); } else { $details = ''; }
	if(isset($param['summary'])){ $summary = trim(mysql_real_escape_string($param['summary'])); } else { $summary = ''; }
	if(isset($param['url'])){ $url = trim(mysql_real_escape_string($param['url'])); } else { $url = ''; }
	if(isset($param['phone'])){ $phone = trim(mysql_real_escape_string($param['phone'])); } else { $phone = ''; }
	if(isset($param['email'])){ $email = trim(mysql_real_escape_string($param['email'])); } else { $email = ''; }
	if(isset($param['address'])){ $address = trim(mysql_real_escape_string($param['address'])); } else { $address = ''; }
	if(isset($param['city'])){ $city = trim(mysql_real_escape_string($param['city'])); } else { $city = ''; }
	if(isset($param['state'])){ $state = trim(mysql_real_escape_string($param['state'])); } else { $state = ''; }
	if(isset($param['postal_code'])){ $postal_code = trim(mysql_real_escape_string($param['postal_code'])); } else { $postal_code = ''; }
	if(isset($param['country'])){ $country = trim(mysql_real_escape_string($param['country'])); } else { $country = ''; }
	if(isset($param['rank'])){ $rank = trim(mysql_real_escape_string($param['rank'])); } else { $rank = ''; }
	if(isset($param['location'])){ $location = trim(mysql_real_escape_string($param['location'])); } else { $location = ''; }
	if(isset($param['photo'])){ $photo = trim(mysql_real_escape_string($param['photo'])); } else { $photo = ''; }

	$post_date = date('Y-m-d H:i:s');

  	$Query = "SELECT * FROM company WHERE Company_ID = " . $organization_id;
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	if($Database && mysql_num_rows($Database))
		{

		$Company = mysql_fetch_assoc($Database);
		$post_date = $Company['Post_Date'];

		$query = "UPDATE company SET";

		if($name!='') { $query .= " name = '" . mysql_real_escape_string($name) . "'"; }
		if($details!='') { $query .= ", Details = '" . mysql_real_escape_string($details) . "'"; }
		if($summary!='') { $query .= ", Summary = '" . mysql_real_escape_string($summary) . "'"; }
		if($url!='') { $query .= ", URL = '" . mysql_real_escape_string($url) . "'"; }
		if($phone!='') { $query .= ", Phone = '" . mysql_real_escape_string($phone) . "'"; }
		if($email!='') { $query .= ", Email = '" . mysql_real_escape_string($email) . "'"; }
		if($address!='') { $query .= ", Address = '" . mysql_real_escape_string($address) . "'"; }
		if($city!='') { $query .= ", City = '" . mysql_real_escape_string($city) . "'"; }
		if($state!='') { $query .= ", State = '" . mysql_real_escape_string($state) . "'"; }
		if($postal_code!='') { $query .= ", Zip = '" . mysql_real_escape_string($postal_code) . "'"; }
		if($country!='') { $query .= ", Country = '" . mysql_real_escape_string($country) . "'"; }
		if($rank!='') { $query .= ", Kin_Rank = '" . mysql_real_escape_string($rank) . "'"; }
		if($location!='') { $query .= ", Location = '" . mysql_real_escape_string($location) . "'"; }
		if($photo!='') { $query .= ", Photo = '" . mysql_real_escape_string($photo) . "'"; }

		$query .= " WHERE Company_ID = " . $organization_id;

		//echo $query . "<br />";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		}

	$organization_id = prepareIdOut($organization_id,$host);

	$F = array();
	$F['organization_id'] = $organization_id;
	$F['name'] = $name;
	$F['details'] = $details;
	$F['summary'] = $summary;
	$F['post_date'] = $post_date;
	$F['url'] = $url;
	$F['phone'] = $phone;
	$F['email'] = $email;
	$F['address'] = $address;
	$F['city'] = $city;
	$F['state'] = $state;
	$F['postal_code'] = $postal_code;
	$F['country'] = $country;
	$F['rank'] = $rank;
	$F['location'] = $location;
	$F['photo'] = $photo;

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
