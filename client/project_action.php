<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
date_default_timezone_set('America/Vancouver');
$vancouver_date = date('Y-m-d').' 00:00:00';

$action = $_POST['action'];
$id = $_POST['id'];


$con = dbc_ivr();
$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
$r = mysql_query ($q);
$row = mysql_fetch_assoc($r);
if ($row['project_type'] != "fndp") {
	safe_redirect("index.php");
}

$project_date = $row['project_date'];


if ($action==='start_dialing'){
	if (file_exists($wav_file_path.$id."/".$id.$live_wav_suffix) AND $project_date<=$vancouver_date) {
		$q = "UPDATE dean_poll_projects SET active=1, fndp_active=1 WHERE id=".$id." AND project_type='fndp' AND project_state='approved'";
		$result = mysql_query($q);
		$logging = "Started Project ".$id." from client interface";
		$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
		$info = mysql_error();
	} else {
	}
}
if ($action==='pause_dialing'){
	$q = "UPDATE dean_poll_projects SET active=0, fndp_active=0 WHERE id=".$id." AND project_type='fndp'";
	$result = mysql_query($q);
	$logging = "Paused Project ".$id."from client interface";
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
	$info = mysql_error();
}

if ($action==='add_back_dnc'){
	$q = "UPDATE dialout_numbers SET active=0, result='' WHERE projectid=".$id." AND result='DNC'";
	$result = mysql_query($q);
	$logging = "Add back DNC numbers for Project ".$id."from client interface";
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
	$info = mysql_error();
}

if ($action==='cancel_project'){
	$q = "UPDATE dean_poll_projects SET active=0, fndp_active=0, project_state='cancelled' WHERE id=".$id." AND project_type='fndp' AND project_state!='complete'";
	$result = mysql_query($q);
	$logging = "cancel Project ".$id."from client interface";
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
	$info = mysql_error();
}
/*
if ($action==='reset_undeliver'){
	$q = "UPDATE dialout_numbers SET active=1 WHERE prov!='TEST' AND active=0 AND projectid=".$id." AND (result is null or result='') AND attempts>0 AND attempts<3";
	$result = mysql_query($q);
	$logging = "Re-ran Project ".$id."from client interface";
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
	$info = mysql_error();
}
if ($action==='deactivate_all'){
	$q = "UPDATE dialout_numbers SET active=0 WHERE prov!='TEST' AND active=1 AND projectid=".$id;
	$result = mysql_query($q);
	$logging = "Pause Project ".$id."from client interface";
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
	$info = mysql_error();
}
*/
mysql_close($con);

if ($action==='disable_am'){
	rename($wav_file_path.$id."/".$id.$machine_wav_suffix, $wav_file_path.$id."/".$id."AM.wav");
	//echo 'disable am';
}
if ($action==='enable_am'){
	rename($wav_file_path.$id."/".$id."AM.wav", $wav_file_path.$id."/".$id.$machine_wav_suffix);
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