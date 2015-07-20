<?php
$route = '/organization/';
$app->post($route, function () use ($app){

	$Add = 1;
	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['name'])){ $name = trim(mysql_real_escape_string($param['name'])); } else { $name = 'No Name'; }
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
	if(isset($param['rank'])){ $rank = trim(mysql_real_escape_string($param['rank'])); } else { $rank = 7; }
	if(isset($param['location'])){ $location = trim(mysql_real_escape_string($param['location'])); } else { $location = ''; }
	if(isset($param['photo'])){ $photo = trim(mysql_real_escape_string($param['photo'])); } else { $photo = ''; }

	$post_date = date('Y-m-d H:i:s');

  	$Query = "SELECT * FROM company WHERE Name = '" . $name . "'";
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	if($Database && mysql_num_rows($Database))
		{
		$ThisItem = mysql_fetch_assoc($Database);
		}
	else
		{
		$Query = "INSERT INTO company(";
		$Query .= "Name,";
		$Query .= "Post_Date,";

		if($details!='') { $Query .= "Details,"; }
		if($summary!='') { $Query .= "Summary,"; }
		if($url!='') { $Query .= "URL,"; }
		if($phone!='') { $Query .= "Phone,"; }
		if($email!='') { $Query .= "Email,"; }
		if($address!='') { $Query .= "Address,"; }
		if($city!='') { $Query .= "City,"; }
		if($state!='') { $Query .= "State,"; }
		if($postal_code!='') { $Query .= "Zip,"; }
		if($country!='') { $Query .= "Country,"; }
		if($rank!='') { $Query .= "Kin_Rank,"; }
		if($location!='') { $Query .= "Location,"; }
		if($photo!='') { $Query .= "Photo,"; }

		$Query .= "Closing";
		$Query .= ") VALUES(";
		$Query .= "'" . mysql_real_escape_string($name) . "',";
		$Query .= "'" . mysql_real_escape_string($post_date) . "',";

		if($details!='') { $Query .= "'" . mysql_real_escape_string($details) . "',"; }
		if($summary!='') { $Query .= "'" . mysql_real_escape_string($summary) . "',"; }
		if($url!='') { $Query .= "'" . mysql_real_escape_string($url) . "',"; }
		if($phone!='') { $Query .= "'" . mysql_real_escape_string($phone) . "',"; }
		if($email!='') { $Query .= "'" . mysql_real_escape_string($email) . "',"; }
		if($address!='') { $Query .= "'" . mysql_real_escape_string($address) . "',"; }
		if($city!='') { $Query .= "'" . mysql_real_escape_string($city) . "',"; }
		if($state!='') { $Query .= "'" . mysql_real_escape_string($state) . "',"; }
		if($postal_code!='') { $Query .= "'" . mysql_real_escape_string($postal_code) . "',"; }
		if($country!='') { $Query .= "'" . mysql_real_escape_string($country) . "',"; }
		if($rank!='') { $Query .= "'" . mysql_real_escape_string($rank) . "',"; }
		if($location!='') { $Query .= "'" . mysql_real_escape_string($location) . "',"; }
		if($photo!='') { $Query .= "'" . mysql_real_escape_string($photo) . "',"; }

		$Query .= "'nothing'";

		$Query .= ")";

		//echo $query . "<br />";
		mysql_query($Query) or die('Query failed: ' . mysql_error());
		$organization_id = mysql_insert_id();
		}


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
