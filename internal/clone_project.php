<?php
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$origin_id = $_GET['id'];

$con = dbc_ivr();
$r = mysql_query("SELECT * FROM dean_poll_projects WHERE id=".$origin_id);
$row = mysql_fetch_array($r);

$project_date = $row['valid_users'];
$pif = $row['pif_number'];
$start_time = $row['time_start'];
$end_time = $row['time_end'];
$dial_plan = $row['dialplan_context'];
$project_name = $row['name'];
$email = $tech_email;
$comment = $row['Notes'];
$caller_string = $row['callerid'];

$r = mysql_query("SELECT NOW()");
$row = mysql_fetch_array($r);
$now = $row[0];

$q = "INSERT INTO dean_poll_projects (name, valid_users, result_email, callerid, dialplan_context, time_start, time_end, dialout_channel, pif_number, Notes, active, auth, creation, last_update, dialplan_extension) VALUES ('".$project_name."', '".$project_date."', '".$email."', '".$caller_string."', '".$dial_plan."', '".$start_time."', '".$end_time."', '".$default_dialout_channel."', '".$pif."', '".$comment."', '0', 'none', '".$now."', '".$now."', 's')";
$r = mysql_query ($q); // Run the query


mysql_close($con);

safe_redirect($_SERVER['PHP_SELF']);

?>