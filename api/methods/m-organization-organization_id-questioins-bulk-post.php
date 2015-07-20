<?php
$route = '/organization/:organization_id/questions/bulk/';
$app->post($route, function ($organization_id)  use ($app){

	$ReturnObject = array();
	$ReturnObject['inserted'] = array();
	$ReturnObject['not-inserted'] = array();

	$request = $app->request();
	$body = $request->getBody();
	$ObjectResult = json_decode($body,true);

	foreach($ObjectResult as $key => $value)
		{
		//echo $key . "<br />";
		foreach($value as $q)
			{
			$question = $q['question'];
			$reference = $q['reference'];
			$answer = $q['answer'];
			$ask_date = $q['ask_date'];

		  	$QuestionQuery = "SELECT * FROM company_question_pivot WHERE question = '" . $question . "'";
			//echo $QuestionQuery . "<br />";
			$QuestionResult = mysql_query($QuestionQuery) or die('Query failed: ' . mysql_error());

			if($QuestionResult && mysql_num_rows($QuestionResult))
				{
				$A = array();
				$A['question'] = $question;
				$A['reference'] = $reference;
				$A['answer'] = $answer;
				$A['ask_date'] = $ask_date;

				array_push($ReturnObject['not-inserted'], $A);

				}
			else
				{

				$query = "INSERT INTO company_question_pivot(";
				$query .= "company_id,";
				$query .= "question,";
				$query .= "reference,";
				$query .= "answer,";
				$query .= "ask_date";
				$query .= ") VALUES(";
				$query .= mysql_real_escape_string($organization_id) . ",";
				$query .= "'" . mysql_real_escape_string($question) . "',";
				$query .= "'" . mysql_real_escape_string($reference) . "',";
				$query .= "'" . mysql_real_escape_string($answer) . "',";
				$query .= "'" . mysql_real_escape_string($ask_date) . "'";
				$query .= ")";

				//echo $query . "<br />";
				mysql_query($query) or die('Query failed: ' . mysql_error());

				$A = array();
				$A['question'] = $question;
				$A['reference'] = $reference;
				$A['answer'] = $answer;
				$A['ask_date'] = $ask_date;

				array_push($ReturnObject['inserted'], $A);

				}
			}
		}

	$app->response()->header("Content-Type", "application/json");
	echo format_json(json_encode($ReturnObject));

	});
?>
