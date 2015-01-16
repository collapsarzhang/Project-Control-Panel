<?php
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

$project_date = date("md", strtotime($_POST['project_date']));
$pif = $_POST['pif'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$client_name = $_POST['client_name'];
$consultant_name = $_POST['consultant_name'];
$dial_plan = $_POST['dial_plan'];
$dial_plan_type = $glb_dialplan_context[$dial_plan]['type'];


$project_name = $dial_plan_type."_".$project_date."_".$client_name;
$email = $consultants[$_SESSION['user_ivr']]['email'];
$comment = $consultant_name."; ".$client_name;
$caller_string = '"'.$_POST['caller_name'].'"<'.$_POST['caller_id'].'>';

$con = dbc_ivr();

$r = mysql_query("SELECT NOW()");
$row = mysql_fetch_array($r);
$now = $row[0];

$project_name = mysql_real_escape_string($project_name);
$project_date = mysql_real_escape_string($project_date);
$email = mysql_real_escape_string($email);
$caller_string = mysql_real_escape_string($caller_string);
$dial_plan = mysql_real_escape_string($dial_plan);
$start_time = mysql_real_escape_string($start_time);
$end_time = mysql_real_escape_string($end_time);
$pif = mysql_real_escape_string($pif);
$comment = mysql_real_escape_string($comment);

$q = "INSERT INTO dean_poll_projects (name, valid_users, result_email, callerid, dialplan_context, time_start, time_end, dialout_channel, pif_number, Notes, active, auth, creation, last_update, dialplan_extension) VALUES ('".$project_name."', '".$project_date."', '".$email."', '".$caller_string."', '".$dial_plan."', '".$start_time."', '".$end_time."', '".$default_dialout_channel."', '".$pif."', '".$comment."', '0', 'none', '".$now."', '".$now."', 's')";
$r = mysql_query ($q); // Run the query

$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr']."', '".mysql_real_escape_string($q)."')");

mysql_close($con);

safe_redirect($_SERVER['PHP_SELF']);

?>