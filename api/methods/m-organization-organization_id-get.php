<?php
$route = '/organization/:organization_id/';
$app->get($route, function ($company_ID)  use ($app){

	$ReturnObject = array();

	$Query = "SELECT * FROM company WHERE Company_ID = " . $company_ID;
	//echo $Query . "<br />";

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$organization_id = $Database['Company_ID'];
		$name = $Database['Name'];
		$details = $Database['Details'];
		$details = scrub($details);
		$summary = $Database['Summary'];
		$summary = scrub($summary);
		$post_date = $Database['Post_Date'];
		$url = $Database['URL'];
		$phone = $Database['Phone'];
		$email = $Database['Email'];
		$address = $Database['Address'];
		$city = $Database['City'];
		$state = $Database['State'];
		$postal_code = $Database['Zip'];
		$country = $Database['Country'];
		$rank = $Database['Kin_Rank'];
		$location = $Database['Location'];
		$photo = $Database['Photo'];
		// manipulation zone

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
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
