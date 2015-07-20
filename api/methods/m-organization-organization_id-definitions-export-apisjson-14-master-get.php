<?php
$route = '/organization/:organization_id/definitions/export/apisjson/.14/master/';	;
$app->get($route, function ($organization_id)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['filterobject'])){ $filterobject = $param['filterobject']; } else { $filterobject = '';}

	$Company_Count = 0;
	$Write_APIS_JSON_File = 0;

	$GetCompanyQuery = "SELECT DISTINCT c.Company_ID,c.Name,c.Details,c.Twitter_Bio,c.Bio FROM company c WHERE c.Company_ID = " . $organization_id . " ORDER BY c.Name ASC;";
	//echo $GetCompanyQuery . "<br />";
	$GetCompanyResult = mysql_query($GetCompanyQuery) or die('Query failed: ' . mysql_error());
	if($GetCompanyResult && mysql_num_rows($GetCompanyResult))
		{
		while ($Company = mysql_fetch_assoc($GetCompanyResult))
			{
			$Company_ID = $Company['Company_ID'];

			$Company_Name = $Company['Name'];
			//echo $Company_Name . "<br />";

			$Company_Name_Slug = PrepareFileName($Company_Name);

			$API_JSON_URL = "http://theapistack.com/" . $Company_Name_Slug . "/apis.json";

			$Body = $Company['Details'];

			$Body = str_replace(chr(34),"",$Body);
			$Body = str_replace(chr(39),"",$Body);
			$Body = strip_tags($Body);
			$Body = mysql_real_escape_string($Body);

			// Add Company As Include in Master APIs.json
			$APIJSON_Include = array();
			$APIJSON_Include['name'] = $Company_Name;
			$APIJSON_Include['url'] = $API_JSON_URL;

			// Logo Image
			$Logo_Image_Width = 0;
			$Company_Image_ID = 0;
			$LogoImageQuery = "SELECT Company_Image_ID, Image_Path,Width FROM company_image WHERE Type = 'logo' AND Company_ID = " . $Company_ID . " ORDER BY Company_Image_ID DESC LIMIT 1";
			//echo $LogoImageQuery . "<br />";
			$LogoImageResult = mysql_query($LogoImageQuery) or die('Query failed: ' . mysql_error());
			while ($LogoImage = mysql_fetch_assoc($LogoImageResult))
				{
				$Company_Image_ID = $LogoImage['Company_Image_ID'];
				$Logo_Image_Path = $LogoImage['Image_Path'];
				$Logo_Image_Width = $LogoImage['Width'];
				}

			// Base URL
			$Base_URL = "";
			$query = "SELECT Company_URL_ID,Type,URL FROM company_url WHERE Company_ID = " . $Company_ID . " AND Type = 'BaseURL'";
			$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
			if($linkResult && mysql_num_rows($linkResult))
				{
				while ($link = mysql_fetch_assoc($linkResult))
					{
					$Base_URL = $link['URL'];
					}
				}

			// Website
			$Website_URL = "";
			$query = "SELECT Company_URL_ID,Type,URL FROM company_url WHERE Company_ID = " . $Company_ID . " AND Type = 'Website'";
			$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
			if($linkResult && mysql_num_rows($linkResult))
				{
				while ($link = mysql_fetch_assoc($linkResult))
					{
					$Website_URL = $link['URL'];
					}
				}

			// Email
			$Email_Address = "";
			$query = "SELECT Company_URL_ID,Type,URL FROM company_url WHERE Company_ID = " . $Company_ID . " AND Type = 'Email'";
			$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
			if($linkResult && mysql_num_rows($linkResult))
				{
				while ($link = mysql_fetch_assoc($linkResult))
					{
					$Email_Address = $link['URL'];
					}
				}

			// Twitter
			$Twitter_URL = "";
			$query = "SELECT Company_URL_ID,Type,URL FROM company_url WHERE Company_ID = " . $Company_ID . " AND Type = 'Twitter'";
			$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
			if($linkResult && mysql_num_rows($linkResult))
				{
				while ($link = mysql_fetch_assoc($linkResult))
					{
					$Twitter_URL = $link['URL'];
					}
				}

			// Begin Individual APIs.json
			$APIJSON = array();
			$APIJSON['name'] = trim($Company_Name);
			$APIJSON['description'] = trim($Body);
			$APIJSON['image'] = trim($Logo_Image_Path);

			// Maange the API.json Tags
			$Tags = array();
			$Tag =  array('api');
			array_push($Tags, $Tag);

			$Tag =  array('application programming interfaces');
			array_push($Tags, $Tag);

			$Tags = array();
			$TagQuery = "SELECT DISTINCT t.Tag FROM tags t JOIN company_tag_pivot ctp ON t.Tag_ID = ctp.Tag_ID WHERE ctp.Company_ID = " . $Company_ID . " AND t.Tag NOT LIKE '%-Stack' ORDER BY t.Tag";
			//echo $TagQuery;
			$TagResult = mysql_query($TagQuery) or die('Query failed: ' . mysql_error());
			$rowcount = 1;
			while ($ThisTag = mysql_fetch_assoc($TagResult))
				{
				$Tag = strtolower($ThisTag['Tag']);
				array_push($Tags, $Tag);
				}

			$APIJSON['tags'] = $Tags;

			$APIJSON['created'] = date('Y-m-d');
			$APIJSON['modified'] = date('Y-m-d');

			$APIJSON['url'] = $API_JSON_URL;
			$APIJSON['specificationVersion'] = "0.14";

			$APIJSON['apis'] = array();

			$API = array();
			$API['name'] = $Company_Name;
			$API['description'] = $Body;
			$API['image'] = trim($Logo_Image_Path);

			$API['humanURL'] = trim($Website_URL);

			if($Base_URL!='')
				{
				$API['baseURL'] = trim($Base_URL);
				}
			else
				{
				$API['baseURL'] = trim($Website_URL);
				}

			$API['tags'] = $Tags;

			$API['properties'] = array();

			$CompanyURLQuery = "SELECT * FROM company_url WHERE Company_ID = " . $Company_ID . " ORDER BY Name, Type";
			//echo $CompanyURLQuery . "<br />";
			$CompanyURLResult = mysql_query($CompanyURLQuery) or die('Query failed: ' . mysql_error());

			while ($CompanyURL = mysql_fetch_assoc($CompanyURLResult))
				{
				$Company_URL_ID = $CompanyURL['Company_URL_ID'];

				$API_URL = $CompanyURL['URL'];
				$API_URL_Name = $CompanyURL['Name'];
				$API_URL_Type = $CompanyURL['Type'];

				$API_Building_Block_ID = $CompanyURL['Building_Block_ID'];

				$API_Building_Block_Name = "";
				$API_Building_Block_Description = "";
				$API_Building_Block_Icon = "";

				if($API_Building_Block_ID>0)
					{

					$Building_Block_Query = "SELECT Building_Block_ID, bb.Name AS Building_Block_Name, bb.About, bbc.Name AS Building_Block_Category_Name, bbc.Type as Type FROM building_block bb JOIN building_block_category bbc ON bb.Building_Block_Category_ID = bbc.BuildingBlockCategory_ID WHERE Building_Block_ID = " . $API_Building_Block_ID;
					//echo $Building_Block_Query . "<br />";
					$Building_Block_Result = mysql_query($Building_Block_Query) or die('Query failed: ' . mysql_error());
					if($Building_Block_Result && mysql_num_rows($Building_Block_Result))
						{
						$HaveBuildingBlock = 1;
						$Building_Block = mysql_fetch_assoc($Building_Block_Result);

						$Building_Block_Image_Query = "SELECT Image_Name,Image_Path FROM building_block_image WHERE Image_Path <> '' AND Building_Block_ID = " . $API_Building_Block_ID . " ORDER BY Building_Block_Image_ID DESC";
						$Building_Block_Image_Result = mysql_query($Building_Block_Image_Query) or die('Query failed: ' . mysql_error());
						while ($Building_Block_Image = mysql_fetch_assoc($Building_Block_Image_Result))
							{
							$API_Building_Block_Icon = $Building_Block_Image['Image_Path'];
							}

						$API_Building_Block_Name = $Building_Block['Building_Block_Name'];
						//echo "Building Block Name: " . $API_Building_Block_Name . "<br />";
						$API_Building_Block_Description = $Building_Block['About'];

						}

					$API_URL_Type_Slug = PrepareFileName($API_Building_Block_Name);

					$Link = array();
					$Link['type'] = "X-" . $API_URL_Type_Slug;
					$Link['url'] = trim($API_URL);
					array_push($API['properties'], $Link);

					}
				}

			array_push($APIJSON['apis'], $API);

			$APIJSON['include'] = array();

			// Begin APIs
			$APIQuery = "SELECT * FROM api WHERE Company_ID = " . $Company_ID . " ORDER BY Name";
			//echo $TagQuery;
			$APIResult = mysql_query($APIQuery) or die('Query failed: ' . mysql_error());
			$rowcount = 1;

			if($APIResult && mysql_num_rows($APIResult))
				{
				while ($API = mysql_fetch_assoc($APIResult))
					{

					$API_ID = $API['API_ID'];
					$API_Name = $API['Name'];
					$API_About = $API['About'];

					$API_About = str_replace(chr(34),"",$API_About);
					$API_About = str_replace(chr(39),"",$API_About);
					$API_About = strip_tags($API_About);
					$API_About = mysql_real_escape_string($API_About);

					// Website
					$API_Website_URL = "";
					$query = "SELECT URL FROM api_url WHERE API_ID = " . $API_ID . " AND Type = 'Website'";
					$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
					if($linkResult && mysql_num_rows($linkResult))
						{
						while ($link = mysql_fetch_assoc($linkResult))
							{
							$API_Website_URL = $link['URL'];
							}
						}

					$Include = array();
					$Include['name'] = $API_Name;
					$Include['url'] = $API_Website_URL;
					array_push($APIJSON['include'], $Include);

					}

				$APIJSON['maintainers'] = array();

				$Maintainer = array();
				$Maintainer['FN'] = "Kin";
				$Maintainer['X-twitter'] = "apievangelist";
				$Maintainer['email'] = "kin@email.com";

				array_push($APIJSON['maintainers'], $Maintainer);

				$ReturnEachAPIJSON = stripslashes(format_json(json_encode($APIJSON)));

				$API['contact'] = array();
				$Contact = array();
				$Contact['FN'] = $Company_Name;
				if($Email_Address!='')
					{
					$Contact['email'] = trim(str_replace("mailto:","",$Email_Address));
					}

				if($Twitter_URL!='')
					{
					$Contact['X-twitter'] = $Twitter_URL;
					}
				array_push($API['contact'], $Contact);

				array_push($APIJSON['apis'], $API);

				$APIJSON['maintainers'] = array();

				$Maintainer = array();
				$Maintainer['FN'] = "Kin";
				$Maintainer['X-twitter'] = "apievangelist";
				$Maintainer['email'] = "kin@email.com";

				array_push($APIJSON['maintainers'], $Maintainer);

				}
			}
		}

	$ReturnObject = $APIJSON;

	$app->response()->header("Content-Type", "application/json");
	echo format_json(json_encode($ReturnObject));

	});


$route = '/organization/definitions/import/apisjson/.14/';	;
$app->get($route, function ()  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['apisjson_url'])){ $apisjson_url = $param['apisjson_url']; } else { $apisjson_url = '';}

	$http = curl_init();
	curl_setopt($http, CURLOPT_URL, $apisjson_url);
	curl_setopt($http, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($http, CURLOPT_SSL_VERIFYPEER, false);

	$http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);

	$ObjectText = curl_exec($http);
	$ObjectResult = json_decode($ObjectText,true);
	//echo $output;
	$info = curl_getinfo($http);
	//var_dump($info);

	//echo $http_status . "<br />";

	if(is_array($ObjectResult))
		{
		//$ObjectText = file_get_contents($apisjson_url);
		//$ObjectResult = json_decode($ObjectText,true);

		if(isset($ObjectResult['name']))
			{
			$name = $ObjectResult['name'];
			}
		else
			{
			$name = "No Name?";
			}

		if(isset($ObjectResult['description']))
			{
			$description = $ObjectResult['description'];
			}
		else
			{
			$description = "";
			}

		if(isset($ObjectResult['image']))
			{
			$image = $ObjectResult['image'];
			}
		else
			{
			$image = "";
			}

		if(isset($ObjectResult['url']))
			{
			$url = $ObjectResult['url'];  // add as APIs.json - Authoritative

			$CheckTagPivotQuery = "SELECT * FROM company_url WHERE (Type = 'APIs.json' OR Type = 'API.json - Authoratative') AND URL = '" . $url . "'";
			$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());

			if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
				{

				}
			else
				{

			  	$Query = "SELECT Company_ID FROM company WHERE Name = '" . mysql_real_escape_string($name) . "'";
				$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());
				if($Database && mysql_num_rows($Database))
					{
					$ThisItem = mysql_fetch_assoc($Database);
					$organization_id = $ThisItem['Company_ID'];
					}
				else
					{
					$post_date = date('Y-m-d H:i:s');
					$Query = "INSERT INTO company(";
					$Query .= "Name,";
					$Query .= "Post_Date,";
					$Query .= "Details,";
					$Query .= "Photo";
					$Query .= ") VALUES(";
					$Query .= "'" . mysql_real_escape_string($name) . "',";
					$Query .= "'" . mysql_real_escape_string($post_date) . "',";
					$Query .= "'" . mysql_real_escape_string($description) . "',";
					$Query .= "'" . mysql_real_escape_string($image) . "'";
					$Query .= ")";
					mysql_query($Query) or die('Query failed: ' . mysql_error());
					$organization_id = mysql_insert_id();
					}

				$tags = $ObjectResult['tags'];
				if(isset($tags) && is_array($tags))
					{
					foreach($tags as $tag)
						{

						///See if the type exists
						$CheckTagQuery = "SELECT Tag_ID FROM tags where Tag = '" . mysql_real_escape_string(trim($tag)) . "'";
						$CheckTagResults = mysql_query($CheckTagQuery) or die('Query failed: ' . mysql_error());

						if($CheckTagResults && mysql_num_rows($CheckTagResults))
							{
							$Tag = mysql_fetch_assoc($CheckTagResults);
							$Tag_ID = $Tag['Tag_ID'];
							}
						else
							{
							$query = "INSERT INTO tags(Tag) VALUES('" . mysql_real_escape_string(trim($tag)) . "'); ";
							mysql_query($query) or die('Query failed: ' . mysql_error());
							$Tag_ID = mysql_insert_id();
							}

						$CheckTagPivotQuery = "SELECT * FROM company_tag_pivot where Tag_ID = " . trim($Tag_ID) . " AND Company_ID = " . trim($organization_id);
						$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());

						if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
							{
							$CheckTagPivot = mysql_fetch_assoc($CheckTagPivotResult);
							}
						else
							{
							$query = "INSERT INTO company_tag_pivot(Tag_ID,Company_ID) VALUES(" . $Tag_ID . "," . $organization_id . "); ";
							mysql_query($query) or die('Query failed: ' . mysql_error());
							}
						}
					}

					$ImageQuery = "SELECT Company_Image_ID FROM company_image WHERE Company_ID = " . $organization_id . " AND Image_Path = '" . $image . "'";
					//echo $ImageQuery . "<br />";
					$ImageResults = mysql_query($ImageQuery) or die('Query failed: ' . mysql_error());
					if($ImageResults && mysql_num_rows($ImageResults))
						{
						$Image = mysql_fetch_assoc($ImageResults);
						}
					else
						{
						$query = "INSERT INTO company_image(Company_ID,Image_Path,Type) VALUES(" . $organization_id . ",'" . $image . "','logo')";
						mysql_query($query) or die('Query failed: ' . mysql_error());
						}

				//$created = $ObjectResult['name'];
				//$modified = $ObjectResult['name'];

				$apis = $ObjectResult['apis'];

				foreach($apis as $apis)
					{
					$name = $apis['name'];
					if(isset($apis['description']))
						{
						$description = $apis['description'];
						}
					else
						{
						$description = "";
						}

					if(isset($apis['image']))
						{
						$image = $apis['image'];
						}
					else
						{
						$image = "";
						}

					$humanURL = $apis['humanURL'];

					if(isset($apis['baseURL']))
						{
						$baseURL = $apis['baseURL'];
						}
					else
						{
						$baseURL = "";
						}

					//$tags = $apis['tags'];
					if(isset($apis['properties']))
						{
						$properties = $apis['properties'];
						}

					// Add as Authorative APIs.json
					$APIQuery = "SELECT API_ID FROM api WHERE Name = '" . mysql_real_escape_string($name) . "'";
					//echo $APIQuery . "<br />";
					$APIResult = mysql_query($APIQuery) or die('Query failed: ' . mysql_error());

					if($APIResult && mysql_num_rows($APIResult))
						{
						$API = mysql_fetch_assoc($APIResult);
						$API_ID = $API['API_ID'];
						}
					else
						{
						$query = "INSERT INTO api(Company_ID,Name,About,URL) VALUES(" . $organization_id . ",'" . mysql_real_escape_string($name) . "','" . mysql_real_escape_string($description) . "','" . mysql_real_escape_string($humanURL) . "')";
						//echo $query . "<br />";
						mysql_query($query) or die('Query failed: ' . mysql_error());
						$API_ID = mysql_insert_id();
						}

					$CheckTagPivotQuery = "SELECT * FROM api_url WHERE API_ID = " . $API_ID . " AND Type = 'Base URL' AND URL = '" . $baseURL . "'";
					$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());

					if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
						{
						$CheckTagPivot = mysql_fetch_assoc($CheckTagPivotResult);
						}
					else
						{
						$query = "INSERT INTO api_url(API_ID,URL,Type) VALUES(" . $API_ID . ",'" . $baseURL . "','Base URL'); ";
						mysql_query($query) or die('Query failed: ' . mysql_error());
						}

					$CheckTagPivotQuery = "SELECT * FROM api_url WHERE API_ID = " . $API_ID . " AND Type = 'Website' AND URL = '" . $humanURL . "'";
					$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());

					if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
						{
						$CheckTagPivot = mysql_fetch_assoc($CheckTagPivotResult);
						}
					else
						{
						$query = "INSERT INTO api_url(API_ID,URL,Type) VALUES(" . $API_ID . ",'" . $humanURL . "','Website'); ";
						mysql_query($query) or die('Query failed: ' . mysql_error());
						}
					var_dump($properties);
					if(isset($properties))
						{
						foreach($properties as $property)
							{

							$p_type = $property['type'];
							$p_url = $property['url'];

							$CheckTagPivotQuery = "SELECT * FROM api_url WHERE API_ID = " . $API_ID . " AND Type = '" . $p_type . "' AND URL = '" . $p_url . "'";
							$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());

							if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
								{
								$CheckTagPivot = mysql_fetch_assoc($CheckTagPivotResult);
								}
							else
								{
								$query = "INSERT INTO api_url(API_ID,URL,Type) VALUES(" . $API_ID . ",'" . $p_url . "','" . $p_type . "'); ";
								mysql_query($query) or die('Query failed: ' . mysql_error());
								}

							$CheckTagPivotQuery = "SELECT * FROM company_url WHERE Company_ID = " . $organization_ID . " AND Type = '" . $p_type . "' AND URL = '" . $p_url . "'";
							$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());

							if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
								{
								$CheckTagPivot = mysql_fetch_assoc($CheckTagPivotResult);
								}
							else
								{
								$query = "INSERT INTO company_url(Company_ID,URL,Type) VALUES(" . $API_ID . ",'" . $p_url . "','" . $p_type . "'); ";
								mysql_query($query) or die('Query failed: ' . mysql_error());
								}

							}
						}

					//$contact = $apis['contact'];
					}

				$include = $ObjectResult['name'];
				$maintainers = $ObjectResult['name'];

				// Add as Authorative APIs.json
				$CheckTagPivotQuery = "SELECT * FROM company_url WHERE Company_ID = " . $organization_id . " AND (Type = 'APIs.json' OR Type = 'API.json - Authoratative') AND URL = '" . $apisjson_url . "'";
				$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());

				if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
					{
					$CheckTagPivot = mysql_fetch_assoc($CheckTagPivotResult);
					}
				else
					{
					$query = "INSERT INTO company_url(Company_ID,URL,Type) VALUES(" . $organization_id . ",'" . $apisjson_url . "','API.json - Authoratative'); ";
					mysql_query($query) or die('Query failed: ' . mysql_error());
					}

				}
			}
		}

	$ReturnObject['apisjson_url'] = $apisjson_url;
	//echo "<br />" . count($ReturnObject) . "<br />";
	$app->response()->header("Content-Type", "application/json");
	echo format_json(json_encode($ReturnObject));

	});
?>
