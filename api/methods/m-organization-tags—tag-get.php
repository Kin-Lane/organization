<?php
$route = '/organization/tags/:tag/';
$app->get($route, function ($tag)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	$tag = mysql_real_escape_string($tag);

	$CompanyQuery = "SELECT DISTINCT c.*,";
	$CompanyQuery .= " ctp.URL as Tag_URL,ctp.Description as Tag_Description";
	$CompanyQuery .= " FROM company c";
	$CompanyQuery .= " JOIN company_tag_pivot ctp ON c.Company_ID = ctp.Company_ID";
	$CompanyQuery .= " JOIN tags t ON t.Tag_ID = ctp.Tag_ID";
	$CompanyQuery .= " WHERE t.Tag = '" . $tag . "'";
	$CompanyQuery .= " ORDER BY c.Name";

	//echo $CompanyQuery;
	$CompanyResult = mysql_query($CompanyQuery) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($CompanyResult))
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

		$tag_url = $Database['Tag_URL'];
		$tag_description = $Database['Tag_Description'];

		$photo = $Database['Photo'];
		$photo_width = $Database['photo_width'];

		$url = $Database['Website_URL'];
		$twitter_url = $Database['Twitter_URL'];

		$blog_url = $Database['blog_url'];
		$blog_rss_url = $Database['blog_rss_url'];

		$github_url = $Database['github_url'];
		$apisjson_url = $Database['apisjson_url'];
		$sdksio_url =  $Database['sdksio_url'];
		$postman_url =  $Database['postman_url'];
		$portal_url =  $Database['portal_url'];

		$org_tags =  $Database['tags'];

		$F = array();
		$F['organization_id'] = $organization_id;
		$F['name'] = $name;
		$F['details'] = $details;
		$F['summary'] = $summary;
		$F['post_date'] = $post_date;
		$F['url'] = $url;

		$F['blog_url'] = $blog_url;
		$F['blog_rss_url'] = $blog_rss_url;
		$F['twitter_url'] = $twitter_url;
		$F['github_url'] = $github_url;
		$F['apisjson_url'] = $apisjson_url;
		$F['sdksio_url'] = $sdksio_url;
		$F['postman_url'] = $postman_url;
		$F['portal_url'] = $portal_url;

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
		$F['photo_width'] = $photo_width;

		$F['tags'] = $org_tags;
		$F['tag_url'] = $tag_url;
		$F['tag_description'] = $tag_description;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
