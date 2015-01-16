<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$con = dbc_ivr();

$project_date = date("md", strtotime($_POST['project_date']));
$project_date_full = date("Y-m-d", strtotime($_POST['project_date'])).' 00:00:00';
//echo $project_date_full;

$r = mysql_query("SELECT pif_number FROM dean_poll_projects WHERE project_type='fndp' ORDER BY creation DESC");
$row = mysql_fetch_array($r);
$pif = 'FNDP'.sprintf('%04d', substr($row[0], 4)+1);

$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$client_name = $_POST['client_name'];
$consultant_name = $_POST['consultant_name'];
$dial_plan = $_POST['dial_plan'];
$dial_plan_type = $glb_dialplan_context[$dial_plan]['type'];


$project_name = $dial_plan_type."_".$project_date."_".$client_name;
$email = $consultants[$_SESSION['user_ivr_fndp']]['email'];
$comment = $consultant_name."; ".$client_name;
$caller_string = '"'.$_POST['caller_name'].'"<'.$_POST['caller_id'].'>';


$project_name = mysql_real_escape_string($project_name);
$project_date = mysql_real_escape_string($project_date);
$email = mysql_real_escape_string($email);
$caller_string = mysql_real_escape_string($caller_string);
$dial_plan = mysql_real_escape_string($dial_plan);
$start_time = mysql_real_escape_string($start_time);
$end_time = mysql_real_escape_string($end_time);
$pif = mysql_real_escape_string($pif);
$comment = mysql_real_escape_string($comment);

$redial_interval = mysql_real_escape_string($_POST['redial_interval']);
$redial_rounds = mysql_real_escape_string($_POST['redial_rounds']);
$redial_instruction = mysql_real_escape_string($_POST['redial_instruction']);

$bill_name = mysql_real_escape_string($_POST['bill_name']);
$bill_phone = mysql_real_escape_string($_POST['bill_phone']);
$bill_email = mysql_real_escape_string($_POST['bill_email']);
$bill_address = mysql_real_escape_string($_POST['bill_address']);

$bill_type = mysql_real_escape_string($_POST['bill_type']);
$bill_setup_types = mysql_real_escape_string($_POST['bill_setup_types']);
$bill_data_return_types = mysql_real_escape_string($_POST['bill_data_return_types']);
$bill_firstname_verify_types = mysql_real_escape_string($_POST['bill_firstname_verify_types']);
$bill_inbound_number_types = mysql_real_escape_string($_POST['bill_inbound_number_types']);
$bill_language_types = mysql_real_escape_string($_POST['bill_language_types']);

$r = mysql_query("SELECT NOW()");
$row = mysql_fetch_array($r);
$now = $row[0];

$q = "INSERT INTO dean_poll_projects (name, valid_users, result_email, callerid, dialplan_context, time_start, time_end, dialout_channel, pif_number, Notes, active, auth, creation, dialplan_extension, project_type, project_state, last_update, project_date, redial_interval, redial_rounds, redial_instruction) VALUES ('".$project_name."', '".$project_date."', '".$email."', '".$caller_string."', '".$dial_plan."', '".$start_time."', '".$end_time."', '".$default_dialout_channel."', '".$pif."', '".$comment."', '0', 'none', '".$now."', 's', 'fndp', 'initial', '".$now."', '".$project_date_full."', '".$redial_interval."', '".$redial_rounds."', '".$redial_instruction."')";
$r = mysql_query($q); // Run the query

$r = mysql_query("SELECT id FROM dean_poll_projects WHERE creation='".$now."' AND project_type='fndp' LIMIT 1");
$row = mysql_fetch_array($r);
$id = $row[0];

$q = "INSERT INTO project_billing_info (projectid, billname, billaddress, billphone, billemail, billtype, billsetuptypes, billdatareturntypes, billfirstnameverifytypes, billinboundnumbertypes, billlanguagetypes) VALUES ('".$id."', '".$bill_name."', '".$bill_address."', '".$bill_phone."', '".$bill_email."', '".$bill_type."', '".$bill_setup_types."', '".$bill_data_return_types."', '".$bill_firstname_verify_types."', '".$bill_inbound_number_types."', '".$bill_language_types."')";
$r = mysql_query ($q); // Run the query


$logging = "Added project ".$project_name;
$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");

mysql_close($con);

safe_redirect($_SERVER['PHP_SELF']);

?>