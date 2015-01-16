<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

$id = $_POST['id'];
$con = dbc_ivr();
$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
$r = mysql_query ($q);
$row = mysql_fetch_assoc($r);
if ($row['project_type'] != "fndp") {
	safe_redirect("index.php");
}
if ($row['project_state'] == "ready") {
	$is_ready = true;
} else {
	$is_ready = false;
}
if ($row['project_state'] == "initial") {
	$is_initial = true;
} else {
	$is_initial = false;
}

$project_name = $row['name'];
$project_date = $row['valid_users'];
$start_time = $row['time_start'];
$end_time = $row['time_end'];
$dial_plan_description = $glb_dialplan_context[$row['dialplan_context']]['description'];
$caller_string = $row['callerid'];

if (isset($_POST['project_ready'])) {
	if ($_POST['project_ready'] && !$is_ready) {
		$project_state = 'ready';
		$logging = "Made Project ".$id." Ready";
		$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
		$r = mysql_query("UPDATE dean_poll_projects SET active=0 WHERE id=".$id); // Run the query
		$r = mysql_query("UPDATE dialout_numbers SET active=0 WHERE active=1 AND projectid=".$id);

		

		$q = "SELECT redial_interval,redial_instruction FROM dean_poll_projects WHERE id=".$id;
                $r = mysql_query($q);
                $row = mysql_fetch_array($r);
                $redial_interval = $row[0];
		$redial_instruction = $row[1];


		$q = "SELECT COUNT(*) FROM dialout_numbers WHERE prov!='TEST' AND (result is NULL OR (result!='invalid' AND result!='removed' AND result!='DNC')) AND projectid=".$id;
		$r = mysql_query($q);
		$row = mysql_fetch_array($r);
		$total_numbers = $row[0];

		$active_area_code = array();
		$q = "SELECT DISTINCT(SUBSTRING(phonenumber,1,3)) FROM dialout_numbers WHERE (result is NULL OR result='') AND prov!='TEST' AND projectid=".$id;
		$r = mysql_query($q);
		while ($row = mysql_fetch_array($r)) {
			$active_area_code[] = $row[0];
		}
		//print_r($active_area_code);

		$active_timezone = array();
		$q = "SELECT DISTINCT(timezone) FROM dialout_numbers WHERE (result is NULL OR result='') AND prov!='TEST' AND projectid=".$id;
		$r = mysql_query($q);
		while ($row = mysql_fetch_array($r)) {
			$active_timezone[] = $row[0];
		}

		$removed_area_code = array();
		$q = "SELECT DISTINCT(SUBSTRING(phonenumber,1,3)) FROM dialout_numbers WHERE result='removed' AND prov!='TEST' AND projectid=".$id;
		$r = mysql_query($q);
		while ($row = mysql_fetch_array($r)) {
			$removed_area_code[] = $row[0];
		}

		$removed_timezone = array();
		$q = "SELECT DISTINCT(timezone) FROM dialout_numbers WHERE result='removed' AND prov!='TEST' AND projectid=".$id;
		$r = mysql_query($q);
		while ($row = mysql_fetch_array($r)) {
			$removed_timezone[] = $row[0];
		}
		
		require 'phpmailer/PHPMailerAutoload.php';

		$mail = new PHPMailer;

		$mail->From = 'ndpbvm@stratcom.ca';
		$mail->FromName = 'BVM System';
		//$item = 'kevin.zhang@stratcom.ca';
		foreach ($stratcom_staff as $item) {
			$mail->addAddress($item);               // Name is optional
		}

		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->addAttachment("/var/spool/asterisk/PollingIVR/".$id."/".$id."00.wav","Live Message.wav");         // Add attachments
		$mail->addAttachment("/var/spool/asterisk/PollingIVR/".$id."/".$id."99.wav","Answer Machine Message.wav");         // Add attachments
		$mail->isHTML(true);
		$mail->Subject = "Project ".$id." ".$project_name." is Ready";
		$mail->Body    = "Project Name: ".$project_name."</br>";
		$mail->Body   .= "Date: ".$project_date."</br>";
		$mail->Body   .= "Start Time: ".$start_time."</br>";
		$mail->Body   .= "End Time: ".$end_time."</br>";
		$mail->Body   .= "Dial Plan: ".$dial_plan_description."</br>";
		$mail->Body   .= "Caller String: ".$caller_string."</br>";
		$mail->Body   .= "Redial Interval: ".$redial_interval."</br>";
		$mail->Body   .= "Redial Instruction: ".$redial_instruction."</br>";
		$mail->Body   .= "<hr>";
		$mail->Body   .= "Total Records: ".$total_numbers."</br>";
		foreach ($active_area_code as $item) {
			$q = "SELECT COUNT(*) FROM dialout_numbers WHERE (result is NULL OR result='') AND prov!='TEST' AND SUBSTRING(phonenumber,1,3)=".$item." AND projectid=".$id;
			$r = mysql_query($q);
			$row = mysql_fetch_array($r);
			$mail->Body   .= "Active Areacode ".$item." Count: ".$row[0]."</br>";
		}
		foreach ($active_timezone as $item) {
			$q = "SELECT COUNT(*) FROM dialout_numbers WHERE (result is NULL OR result='') AND prov!='TEST' AND timezone=".$item." AND projectid=".$id;
			$r = mysql_query($q);
			$row = mysql_fetch_array($r);
			$mail->Body   .= "Active Timezone ".$item." Count: ".$row[0]."</br>";
		}
		foreach ($removed_area_code as $item) {
			$q = "SELECT COUNT(*) FROM dialout_numbers WHERE result='removed' AND prov!='TEST' AND SUBSTRING(phonenumber,1,3)=".$item." AND projectid=".$id;
			$r = mysql_query($q);
			$row = mysql_fetch_array($r);
			$mail->Body   .= "Removed Areacode ".$item." Count: ".$row[0]."</br>";
		}
		foreach ($removed_timezone as $item) {
			$q = "SELECT COUNT(*) FROM dialout_numbers WHERE result='removed' AND prov!='TEST' AND timezone=".$item." AND projectid=".$id;
			$r = mysql_query($q);
			$row = mysql_fetch_array($r);
			$mail->Body   .= "Removed Timezone ".$item." Count: ".$row[0]."</br>";
		}
		foreach ($warning_areacode as $key=>$item) {
			$q = "SELECT COUNT(*) FROM dialout_numbers WHERE (result is NULL OR result='') AND prov!='TEST' AND SUBSTRING(phonenumber,1,3)=".$key." AND projectid=".$id;
			$r = mysql_query($q);
			$row = mysql_fetch_array($r);
			if ($row[0] > 0) {
				$mail->Body   .= "Warning: there are ".$row[0]." entries in ".$item['reason']."</br>";
			}
		}

		if(!$mail->send()) {
		   echo 'Message could not be sent.';
		   echo 'Mailer Error: ' . $mail->ErrorInfo;
		   exit;
		}
	}
} else if ($is_ready) {
	$project_state = 'initial';
	$logging = "Made Project ".$id." Not Ready";
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
}


if (isset($_POST['project_complete']) && $_POST['project_complete']) {
	$project_state = 'complete';
	$logging = "Made Project ".$id." Complete";
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
	$r = mysql_query("UPDATE dean_poll_projects SET active=0 WHERE id=".$id); // Run the query
	$r = mysql_query("UPDATE dialout_numbers SET active=0 WHERE active=1 AND projectid=".$id);

	require ("pdf_report.php"); 

	require 'phpmailer/PHPMailerAutoload.php';

	$mail = new PHPMailer;

	$mail->From = 'tm.tech@stratcom.ca';
	$mail->FromName = 'TM Tech';
	//$item = 'kevin.zhang@stratcom.ca';
	foreach ($stratcom_report_staff as $item) {
		$mail->addAddress($item);               // Name is optional
	}

	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	$mail->addAttachment($fname);         // Add attachments
	$mail->isHTML(true);
	$mail->Subject = "Project ".$id." Report";
	$mail->Body = "Please see attachment";

	if(!$mail->send()) {
	   echo 'Message could not be sent.';
	   echo 'Mailer Error: ' . $mail->ErrorInfo;
	   exit;
	}

}



$con = dbc_ivr();
if (isset($project_state)) {
	$q = "UPDATE dean_poll_projects SET project_state='".$project_state."' WHERE id=".$id;
	$r = mysql_query($q); // Run the query
	mysql_close($con);

}

?>

<script>
	function submitForm(projectid)
	{
		$('#projectform').submit();
	}
</script>
<form method="POST" id="projectform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="page" value="edit_project">
	<input type="hidden" id="projectid" name="id" value=<?php echo $id; ?>>
<form>
<script>
	submitForm()
</script>

