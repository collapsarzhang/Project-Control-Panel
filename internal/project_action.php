<?php
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
$action = $_GET['action'];
$id = $_GET['id'];
$con = dbc_ivr();
if ($action==='activate_number'){
	$q = "UPDATE dialout_numbers SET active=1 WHERE prov!='TEST' AND active=0 AND projectid=$id AND (result is null or result='') AND attempts=0";
	$result = mysql_query($q);
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr']."', '".mysql_real_escape_string($q)."')");
	$info = mysql_error();
}
if ($action==='reset_undeliver'){
	$q = "UPDATE dialout_numbers SET active=1 WHERE prov!='TEST' AND active=0 AND projectid=$id AND (result is null or result='') AND attempts>0 AND attempts<".$max_rounds;
	$result = mysql_query($q);
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr']."', '".mysql_real_escape_string($q)."')");
	$info = mysql_error();
}
else if ($action==='reset_am'){
	$q = "UPDATE dialout_numbers SET active=1 WHERE prov!='TEST' AND active=0 AND projectid=$id AND result='MACHINE' AND attempts>0 AND attempts<".$max_rounds;
	$result=mysql_query($q);
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr']."', '".mysql_real_escape_string($q)."')");
	$info = mysql_error();
}
else if ($action==='deactivate'){
	$q = "UPDATE dialout_numbers SET active=0 WHERE prov!='TEST' AND active=1 AND projectid=$id";
	$result=mysql_query($q);
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr']."', '".mysql_real_escape_string($q)."')");
	$info = mysql_error();
}
else if ($action==='finish_project'){
	$project_state = 'complete';
	$q = "UPDATE dean_poll_projects SET active=0, project_state='".$project_state."' WHERE id=".$id;
	$result=mysql_query($q);
	$q = "UPDATE dialout_numbers SET active=0 WHERE prov!='TEST' AND active=1 AND projectid=$id";
	$result=mysql_query($q);
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr']."', '".mysql_real_escape_string($q)."')");
	$info = mysql_error();
	
	require 'pdf_report.php'; 

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
else if ($action==='disable_am_message'){
	rename($wav_file_path.$id."/".$id."99.wav", $wav_file_path.$id."/".$id."AM.wav");
}
else if ($action==='enable_am_message'){
	rename($wav_file_path.$id."/".$id."AM.wav", $wav_file_path.$id."/".$id."99.wav");
}
mysql_close($con);

safe_redirect($_SERVER['PHP_SELF']."?page=edit_project&id=".$id);
?>
