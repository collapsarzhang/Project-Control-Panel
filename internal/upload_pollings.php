<?php
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
date_default_timezone_set('America/Vancouver');
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

$con = dbc_ivr();
$i=0;
$dummy_question_start = 60;
$dummy_question_end = 90;
$error = false;
$error_message = '';
$dummy_question_type = "announce";
$projectid=$_POST['id'];
//echo checkip();
$count = 0;
if (($handle = fopen($_FILES['polling_file']['tmp_name'], "r")) !== FALSE) {
	while (($data = fgetcsv($handle)) !== FALSE) {
		if ($count > 0) {
			if ($data[0] != '') {
				if ($i>0) {
					$question_info[$i]["id"] = $current_question_id;
					$question_info[$i]["type"] = $current_question_type;
					if ($current_question_type == "single-digit") {
						//$data_array["valid"] = array();
						$data_array["valid"] = $options;
						$question_info[$i]["options"] = serialize($data_array);
					} else {
						$question_info[$i]["options"] = "";
					}
					if (sizeof($next_questions)==1){
						$question_info[$i]["questions"] = $next_questions[0];
					}
					else{
						$question_info[$i]["questions"] = serialize($next_questions);
					}
					unset($next_questions);
				}
				if ($data[0]<10) $data[0]="0".$data[0];
				$current_question_id = $projectid.$data[0];
				$current_question_type = $data[1];
				$options = $data[2];
				$check_if_random = explode('/', $data[3]);
				if (sizeof($check_if_random)>1) {
					foreach ($check_if_random as $item) {
						$dummy_question_next_questions[] = $projectid*100+$item;
					}
					$i++;
					$dummy_question = $projectid*100+$dummy_question_start;
					$next_questions[] = $dummy_question;
					$question_info[$i]["id"] = $dummy_question;
					$question_info[$i]["type"] = $dummy_question_type;
					$question_info[$i]["options"] = '';
					$dummy_question_next_questions["random"] = 1;
					$question_info[$i]["questions"] = serialize($dummy_question_next_questions);
					unset($dummy_question_next_questions);
					$dummy_question_start++;
					mkdir($wav_file_path.$projectid);
					copy('includes/blank.wav', $wav_file_path.$projectid.'/'.$dummy_question.'.wav');
					if ($dummy_question_start>=$dummy_question_end) {
						$error = true;
						$error_message = "too many random questions";
					}
				} else {
					if ($data[3] == "end") {
						$next_questions[] = 0;
					} else {
						$next_questions[] = $projectid*100+$data[3];
					}
				}
				$i++;
			} else {
				$options .= $data[2];
				$check_if_random = explode('/', $data[3]);
				if (sizeof($check_if_random)>1) {
					foreach ($check_if_random as $item) {
						$dummy_question_next_questions[] = $projectid*100+$item;
					}
					$i++;
					$dummy_question = $projectid*100+$dummy_question_start;
					$next_questions[] = $dummy_question;
					$question_info[$i]["id"] = $dummy_question;
					$question_info[$i]["type"] = $dummy_question_type;
					$question_info[$i]["options"] = '';
					$dummy_question_next_questions["random"] = 1;
					$question_info[$i]["questions"] = serialize($dummy_question_next_questions);
					unset($dummy_question_next_questions);
					$dummy_question_start++;
					mkdir($wav_file_path.$projectid);
					copy('includes/blank.wav', $wav_file_path.$projectid.'/'.$dummy_question.'.wav');
					if ($dummy_question_start>=$dummy_question_end) {
						$error = true;
						$error_message = "too many random questions";
					}
					$i++;
				} else {
					if ($data[3] == "end") {
						$next_questions[] = 0;
					} else {
						$next_questions[] = $projectid*100+$data[3];
					}
				}
			}
		}
		$count++;
	}
	$question_info[$i]["id"] = $current_question_id;
	$question_info[$i]["type"] = $current_question_type;
	if ($current_question_type == "single-digit") {
		//$data_array["valid"] = array();
		$data_array["valid"] = $options;
		$question_info[$i]["options"] = serialize($data_array);
	} else {
		$question_info[$i]["options"] = "";
	}
	if (sizeof($next_questions)==1){
		$question_info[$i]["questions"] = $next_questions[0];
	}
	else{
		$question_info[$i]["questions"] = serialize($next_questions);
	}
	fclose($handle);
}


$first_q = true;
//echo "<table border='1'>";
foreach ($question_info as $question) {
	//echo "<tr>";
	//echo "<td>".$question["id"]."</td>";
	//echo "<td>".$question["type"]."</td>";
	//echo "<td>".$question["options"]."</td>";
	//echo "<td>".$question["questions"]."</td>";
	if ($first_q) {
		$is_first = 1;
		$first_q = false;
	} else {
		$is_first = 0;
	}
	//echo "<td>".$is_first."</td>";
	//echo "</tr>";
	mysql_query("INSERT INTO dean_poll_questions (question_id, project_id, type, data, next, first) VALUES (".$question['id'].", ".$projectid.", '".$question['type']."', '".$question['options']."', '".$question['questions']."', ".$is_first.") ON DUPLICATE KEY UPDATE question_id=".$question['id'].", project_id=".$projectid.", type='".$question['type']."', data='".$question['options']."', next='".$question['questions']."', first=".$is_first) OR die("error excuting query");
}
//echo "</table>";
//print_r($question_info);
mysql_close($con);

safe_redirect($_SERVER['PHP_SELF']."?page=edit_project&id=".$projectid);
?>
