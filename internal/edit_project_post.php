<?php
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

$id = $_POST['id'];
$project_date = date("md", strtotime($_POST['project_date']));
$pif = $_POST['pif'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$client_name = $_POST['client_name'];
$consultant_name = $_POST['consultant_name'];
$dial_plan = $_POST['dial_plan'];
$dial_plan_type = $glb_dialplan_context[$dial_plan]['type'];

if ($dial_plan_type == "")
$dial_plan_type = "Polling";

$test_timezone = get_available_timezone($start_time, $end_time);

if (isset($_POST['activate_project']) && $_POST['activate_project']) {
	$activate_project = 1;
} else {
	$activate_project = 0;
}

//echo $activate_project;

$project_name = $dial_plan_type."_".$project_date."_".$client_name;
$email = $consultants[$_SESSION['user_ivr']]['email'];
$comment = $consultant_name."; ".$client_name;

if (isset($_POST['caller_name']) && $_POST['caller_name'] != "") {
	$caller_string = '"'.$_POST['caller_name'].'"<'.$_POST['caller_id'].'>';
} else {
	$caller_string = $_POST['caller_id'];
}

$con = dbc_ivr();

$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
$r = mysql_query ($q);
$row = mysql_fetch_assoc($r);
if (($row['project_type'] == 'fndp') AND ($row['project_state'] == 'ready')) {
	$is_ready = true;
} else {
	$is_ready = false;
}
if (($row['project_type'] == 'fndp') AND ($row['project_state'] == 'approved')) {
	$is_approved = true;
} else {
	$is_approved = false;
}

if (isset($_POST['project_approved'])) {
	if ($_POST['project_approved'] AND $is_ready) {
		$project_state = 'approved';
		$logging = "Made Project ".$id." Approved";
		$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr']."', '".$logging."')");
		$r = mysql_query("UPDATE dean_poll_projects SET active=0 WHERE id=".$id); // Run the query
		$r = mysql_query("UPDATE dialout_numbers SET active=0 WHERE active=1 AND projectid=".$id);

		require 'phpmailer/PHPMailerAutoload.php';

		$mail = new PHPMailer;

		$mail->From = 'tm.tech@stratcom.ca';
		$mail->FromName = 'TM Tech';
		//$item = 'kevin.zhang@stratcom.ca';
		foreach ($fndp_staff as $item) {
			$mail->addAddress($item);               // Name is optional
		}

		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->isHTML(true);
		$mail->Subject = "Project ".$project_name." is Approved";
		$mail->Body = "Project ".$project_name." is Approved";
		if(!$mail->send()) {
		   echo 'Message could not be sent.';
		   echo 'Mailer Error: ' . $mail->ErrorInfo;
		   exit;
		}
	}
} else if ($is_approved) {
		$project_state = 'ready';
		$logging = "Made Project ".$id." Not Approved";
		$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr']."', '".$logging."')");
		$r = mysql_query("UPDATE dean_poll_projects SET active=0 WHERE id=".$id); // Run the query
		$r = mysql_query("UPDATE dialout_numbers SET active=0 WHERE active=1 AND projectid=".$id);
}

if (isset($project_state)) {
	$q = "UPDATE dean_poll_projects SET project_state='".$project_state."' WHERE id=".$id;
	$r = mysql_query ($q); // Run the query
} 


$project_name = mysql_real_escape_string($project_name);
$project_date = mysql_real_escape_string($project_date);
$email = mysql_real_escape_string($email);
$caller_string = mysql_real_escape_string($caller_string);
$dial_plan = mysql_real_escape_string($dial_plan);
$start_time = mysql_real_escape_string($start_time);
$end_time = mysql_real_escape_string($end_time);
$pif = mysql_real_escape_string($pif);
$comment = mysql_real_escape_string($comment);
$q = "UPDATE dean_poll_projects SET active=".$activate_project.", name='".$project_name."', valid_users='".$project_date."', result_email='".$email."', callerid='".$caller_string."', time_start='".$start_time."', time_end='".$end_time."', dialplan_context='".$dial_plan."', pif_number='".$pif."', Notes='".$comment."' WHERE id=".$id;
//echo $q;
$r = mysql_query ($q); // Run the query
$logging = "Updated Project ".$id;
$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr']."', '".$logging."')");




foreach ($consultants as $username => $item) {
	$q = "SELECT * FROM dialout_numbers WHERE projectid=".$id." AND phonenumber='".$item['number']."'";
	$r = mysql_query ($q); // Run the query
	if (mysql_num_rows($r) > 0) {
		$update=true;
	} else {
		$update=false;
	}
	if (isset($_POST[$item['recogniz']]) && $_POST[$item['recogniz']]) {
		if ($update) {
			$q = "UPDATE dialout_numbers SET active=1, result='', timezone='".$test_timezone."' WHERE phonenumber=".$item['number']." AND projectid=".$id;
		} else {
			$q = "INSERT INTO dialout_numbers (projectid, phonenumber,timezone,active,result,prov) VALUES (".$id.",'".$item['number']."','".$test_timezone."',1,'','TEST')";
		}
	} else {
		if ($update) {
			$q = "UPDATE dialout_numbers SET active=0, result='invalid', timezone='".$test_timezone."' WHERE phonenumber=".$item['number']." AND projectid=".$id;
		} else {
			$q = "INSERT INTO dialout_numbers (projectid, phonenumber,timezone,active,result,prov) VALUES (".$id.",'".$item['number']."','".$test_timezone."',0,'invalid','TEST')";
		}
	}
	$r = mysql_query ($q); // Run the query
}


mysql_close($con);

safe_redirect($_SERVER['PHP_SELF']."?page=edit_project&id=".$id);

?>