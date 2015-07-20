<?php
$route = '/organization/:organization_id/definitions/export/apisjson/.14/';
$app->get($route, function ($organization_id)  use ($app,$guser,$gpass){

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
			$Logo_Image_Path = "";
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
				if (strpos($Tag,'Stack') !== false)
					{
					}
				else
					{
					if (strpos($Tag,'stack') !== false)
						{
						}
					else
						{
						array_push($Tags, $Tag);
						}
					}
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

					if($API_URL_Type_Slug!='email')
						{
						$Link = array();
						$Link['type'] = "X-" . $API_URL_Type_Slug;
						$Link['url'] = trim($API_URL);
						array_push($API['properties'], $Link);
						}

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

					}
				}

			array_push($APIJSON['apis'], $API);

			$APIJSON['include'] = array();

			// Begin APIs
			$APIQuery = "SELECT a.* FROM api a INNER JOIN company_api_pivot cap ON a.API_ID = cap.API_ID WHERE cap.Company_ID = " . $Company_ID . " ORDER BY a.Name";
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
					$query = "SELECT URL FROM api_url WHERE API_ID = " . $API_ID . " AND Type = 'Website' LIMIT 1";
					$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
					if($linkResult && mysql_num_rows($linkResult))
						{
						while ($link = mysql_fetch_assoc($linkResult))
							{
							$API_Website_URL = $link['URL'];
							}
						}

					// Documentation
					$API_Documentation_URL = "";
					$query = "SELECT URL FROM api_url WHERE API_ID = " . $API_ID . " AND Type = 'Documentation' LIMIT 1";
					$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
					if($linkResult && mysql_num_rows($linkResult))
						{
						while ($link = mysql_fetch_assoc($linkResult))
							{
							$API_Documentation_URL = $link['URL'];
							}
						}

					// Swagger
					$API_Swagger_URL = "";
					$query = "SELECT URL FROM api_url WHERE API_ID = " . $API_ID . " AND Type LIKE 'Swagger%' LIMIT 1";
					//echo $query
					$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
					if($linkResult && mysql_num_rows($linkResult))
						{
						while ($link = mysql_fetch_assoc($linkResult))
							{
							$API_Swagger_URL = $link['URL'];
							}
						}
					//echo "Swagger: " . $API_Swagger_URL . "<br />";
					// I was pushing to include but doesn't work, will have to play with different formats
					//$Include = array();
					//$Include['name'] = $API_Name;
					//$Include['url'] = $API_Website_URL;
					//array_push($APIJSON['include'], $Include);

					$API = array();
					$API['name'] = $API_Name;
					$API['description'] = $API_About;
					$API['image'] = trim($Logo_Image_Path);

					if($API_Documentation_URL!='')
						{
						$API['humanURL'] = trim($API_Documentation_URL);
						}
					else
						{
						$API['humanURL'] = trim($API_Website_URL);
						}

					if($Base_URL!='')
						{
						$API['baseURL'] = trim($Base_URL);
						}
					else
						{
						$API['baseURL'] = trim($Website_URL);
						}

					$API['properties'] = array();

					if($API_Documentation_URL!='')
						{
						$Link = array();
						$Link['type'] = "x-documentation";
						$Link['url'] = trim($API_Documentation_URL);
						array_push($API['properties'], $Link);
						}

					if($API_Swagger_URL!='')
						{
						$Link = array();
						$Link['type'] = "Swagger";
						$Link['url'] = trim($API_Swagger_URL);
						array_push($API['properties'], $Link);
						}

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

					}
				}
			}
		}

	$APIJSON['maintainers'] = array();

	$Maintainer = array();
	$Maintainer['FN'] = "Kin";
	$Maintainer['X-twitter'] = "apievangelist";
	$Maintainer['email'] = "kin@email.com";

	array_push($APIJSON['maintainers'], $Maintainer);

	$ReturnObject = format_json(stripslashes(json_encode($APIJSON)));

	$API_JSON_Store_File = "data/" . $Company_Name_Slug . "/apis.json";

	//echo $API_JSON_Store_File;

	$apisjson_url = "http://theapistack.com/" . $API_JSON_Store_File;
	//echo $apisjson_url;

	$CompanyQuery = "SELECT * FROM company_url WHERE Company_ID = " . $organization_id . " AND Type = 'APIs.json'";
	//echo $CompanyQuery . "<br />";
	$CompanyResults = mysql_query($CompanyQuery) or die('Query failed: ' . mysql_error());
	if($CompanyResults && mysql_num_rows($CompanyResults))
		{
		$Company = mysql_fetch_assoc($CompanyResults);
		$Company_URL_ID = $Company['Company_URL_ID'];
		$UpdateQuery = "UPDATE company_url SET URL = '" . $apisjson_url . "' WHERE Company_URL_ID = " . $Company_URL_ID;
		//echo $UpdateQuery . "<br />";
		$UpdateResult = mysql_query($UpdateQuery) or die('Query failed: ' . mysql_error());
		}
	else
		{
		$CompanyQuery = "INSERT INTO company_url(Company_ID,URL,TYPE) VALUES(" . $organization_id . ",'" . $apisjson_url . "','APIs.json')";
		//echo $CompanyQuery . "<br />";
		$CompanyResults = mysql_query($CompanyQuery) or die('Query failed: ' . mysql_error());
		}

	$GitHubClient = new GitHubClient();
	$GitHubClient->setCredentials($guser, $gpass);

	$owner = "api-stack";
	$Project_Github_Repo = "api-stack";
	$ref = "gh-pages";
	try
		{
		$CheckFile = $GitHubClient->repos->contents->getContents($owner, $Project_Github_Repo, $ref, $API_JSON_Store_File);
		$name = $CheckFile->getname();
		$content = base64_decode($CheckFile->getcontent());
		$sha = $CheckFile->getsha();
		$message = "Updating " . $API_JSON_Store_File . " via Laneworks Publish";
		$content = base64_encode($ReturnObject);
		$updateFile = $GitHubClient->repos->contents->updateFile($owner, $Project_Github_Repo, $API_JSON_Store_File, $message, $content, $sha, $ref);
		}
	catch (Exception $e)
		{
		$message = "Adding " . $API_JSON_Store_File . " via Laneworks Publish";
		$content = base64_encode($ReturnObject);
		$updateFile = $GitHubClient->repos->contents->createFile($owner, $Project_Github_Repo, $API_JSON_Store_File, $message, $content, $ref);
		}

	$GitHubClient = new GitHubClient();
	$GitHubClient->setCredentials($guser, $gpass);

	$owner = "api-stack";
	$Project_Github_Repo = "api-stack";
	$ref = "master";
	try
		{
		$CheckFile = $GitHubClient->repos->contents->getContents($owner, $Project_Github_Repo, $ref, $API_JSON_Store_File);
		$name = $CheckFile->getname();
		$content = base64_decode($CheckFile->getcontent());
		$sha = $CheckFile->getsha();
		$message = "Updating " . $API_JSON_Store_File . " via Laneworks Publish";
		$content = base64_encode($ReturnObject);
		$updateFile = $GitHubClient->repos->contents->updateFile($owner, $Project_Github_Repo, $API_JSON_Store_File, $message, $content, $sha, $ref);
		}
	catch (Exception $e)
		{
		$message = "Adding " . $API_JSON_Store_File . " via Laneworks Publish";
		$content = base64_encode($ReturnObject);
		$updateFile = $GitHubClient->repos->contents->createFile($owner, $Project_Github_Repo, $API_JSON_Store_File, $message, $content, $ref);
		}

	//$ReturnObject['content'] = $Parameters;

	$app->response()->header("Content-Type", "application/json");
	echo $ReturnObject;

	});
?>
