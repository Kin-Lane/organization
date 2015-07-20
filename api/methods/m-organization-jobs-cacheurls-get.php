<?php
$route = '/organization/jobs/cacheurls/';
$app->get($route, function ()  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	$url_cache = date('Y') . date('m') . date('d');

	$CompanyQuery = "SELECT DISTINCT c.*,";
	$CompanyQuery .= " (SELECT Image_Path FROM company_image WHERE Company_ID = c.Company_ID ORDER BY Company_Image_ID DESC LIMIT 1 ORDER BY Company_Image_ID DESC) as Photo,";
	$CompanyQuery .= " (SELECT Width FROM company_image WHERE Company_ID = c.Company_ID ORDER BY Company_Image_ID DESC LIMIT 1 ORDER BY Company_Image_ID DESC) as Photo_Width,";
	$CompanyQuery .= " (SELECT URL FROM company_url WHERE Company_ID = c.Company_ID AND Type = 'Website' LIMIT 1) AS Website_URL,";
	$CompanyQuery .= " (SELECT URL FROM company_url WHERE Company_ID = c.Company_ID AND Type = 'Twitter' LIMIT 1) AS Twitter_URL,";
	$CompanyQuery .= " (SELECT URL FROM company_url WHERE Company_ID = c.Company_ID AND Type = 'Blog' LIMIT 1) AS Blog_URL,";
	$CompanyQuery .= " (SELECT URL FROM company_url WHERE Company_ID = c.Company_ID AND Type = 'Blog RSS' LIMIT 1) AS Blog_RSS_URL,";
	$CompanyQuery .= " (SELECT URL FROM company_url WHERE Company_ID = c.Company_ID AND Type = 'Github' LIMIT 1) AS Github_URL,";
	$CompanyQuery .= " (SELECT URL FROM company_url WHERE Company_ID = c.Company_ID AND Type = 'APIs.json' LIMIT 1) AS APISJSON_URL,";
	$CompanyQuery .= " (SELECT URL FROM company_url WHERE Company_ID = c.Company_ID AND Type = 'SDKs.io' LIMIT 1) AS SDKSIO_URL,";
	$CompanyQuery .= " (SELECT URL FROM company_url WHERE Company_ID = c.Company_ID AND Type = 'Postman Collection' LIMIT 1) AS Postman_URL";
	$CompanyQuery .= " FROM company c";
	$CompanyQuery .= " WHERE url_cache <> " . $url_cache;
	$CompanyQuery .= " ORDER BY c.Name LIMIT 50";
	//echo $CompanyQuery;
	$CompanyResult = mysql_query($CompanyQuery) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($CompanyResult))
		{
		$organization_id = $Database['Company_ID'];
		$name = $Database['Name'];

		$photo = $Database['Photo'];
		$photo_width = $Database['Photo_Width'];
		if($photo_width==''){ $photo_width = 0; }

		$website_url = $Database['Website_URL'];
		$twitter_url = $Database['Twitter_URL'];

		$blog_url = $Database['Blog_URL'];
		$blog_rss_url = $Database['Blog_RSS_URL'];

		$github_url = $Database['Github_URL'];
		$apisjson_url = $Database['APISJSON_URL'];
		$sdksio_url = $Database['SDKSIO_URL'];
		$postman_url = $Database['Postman_URL'];

		$org_tags = "";
		$TagQuery = "SELECT t.Tag_ID, t.Tag FROM tags t INNER JOIN company_tag_pivot sptp ON t.Tag_ID = sptp.Tag_ID WHERE sptp.Company_ID = " . $organization_id . " ORDER BY Tag";
		$TagResult = mysql_query($TagQuery) or die('Query failed: ' . mysql_error());
		$First = 1;
		while ($ThisTag = mysql_fetch_assoc($TagResult))
			{
			$Tag = $ThisTag['Tag'];
			if($First==1){
				$First=2;
				$org_tags .= $Tag;
				}
			else {
				$org_tags .= "," . $Tag;
				}
			}

		$UpdateQuery = "UPDATE company";
		$UpdateQuery .= " SET Website_URL = '" . $website_url . "',";
		$UpdateQuery .= " Twitter_URL = '" . $twitter_url . "',";
		$UpdateQuery .= " blog_url = '" . $blog_url . "',";
		$UpdateQuery .= " blog_rss_url = '" . $blog_rss_url . "',";
		$UpdateQuery .= " github_url = '" . $github_url . "',";
		$UpdateQuery .= " apisjson_url = '" . $apisjson_url . "',";
		$UpdateQuery .= " sdksio_url = '" . $sdksio_url . "',";
		$UpdateQuery .= " postman_url = '" . $postman_url . "',";
		$UpdateQuery .= " Photo = '" . $photo . "',";
		$UpdateQuery .= " photo_width = " . $photo_width . ",";
		$UpdateQuery .= " url_cache = " . $url_cache;
		$UpdateQuery .= " WHERE Company_ID = " . $organization_id;
		//echo $UpdateQuery . "<br />";
		$UpdateResults = mysql_query($UpdateQuery) or die('Query failed: ' . mysql_error());

		$F = array();
		$F['organization_id'] = $organization_id;
		$F['name'] = $name;
		$F['website_url'] = $website_url;
		$F['blog_url'] = $blog_url;
		$F['blog_rss_url'] = $blog_rss_url;
		$F['twitter_url'] = $twitter_url;
		$F['github_url'] = $github_url;
		$F['apisjson_url'] = $apisjson_url;
		$F['sdksio_url'] = $sdksio_url;
		$F['postman_url'] = $postman_url;
		$F['photo'] = $photo;
		$F['photo_width'] = $photo_width;

		$F['tags'] = $org_tags;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
