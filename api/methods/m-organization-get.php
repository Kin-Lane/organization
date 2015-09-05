<?php
$route = '/organization/';
$app->get($route, function ()  use ($app){

	$ReturnObject = array();

	if(isset($_REQUEST['query'])){ $query = trim(mysql_real_escape_string($_REQUEST['query'])); } else { $query = '';}

	if($query!='')
		{
		$Query = "SELECT * FROM company WHERE Name = '%" . $query . "%' OR Details LIKE '%" . $query . "%' OR Summary LIKE '%" . $query . "%'";
		}
	else
		{
		$Query = "SELECT * FROM company";
		}

	$Query .= " ORDER BY Name ASC";

	$Query .= " LIMIT 250";
	//echo $Query . "<br />";

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$organization_id = $Database['Company_ID'];
		$name = $Database['Name'];
		$details = $Database['Details'];
		$details = strip_tags($details);
		$summary = $Database['Summary'];
		$summary = strip_tags($summary);
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

		$host = $_SERVER['HTTP_HOST'];
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

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
