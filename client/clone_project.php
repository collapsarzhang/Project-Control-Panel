<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
$origin_id = $_POST['id'];

$con = dbc_ivr();
$r = mysql_query("SELECT * FROM dean_poll_projects WHERE id=".$origin_id);
$row = mysql_fetch_array($r);

$project_date = $row['valid_users'];
$project_date_full = $row['project_date'];
$redial_interval = $row['redial_interval'];
$redial_rounds = $row['redial_rounds'];
$redial_instruction = "";

$start_time = $row['time_start'];
$end_time = $row['time_end'];
$dial_plan = $row['dialplan_context'];
$project_name = $row['name']."[clone]";
$email = $consultants[$_SESSION['user_ivr_fndp']]['email'];
$comment = $row['Notes'];
$caller_string = $row['callerid'];

$r = mysql_query("SELECT NOW()");
$row = mysql_fetch_array($r);
$now = $row[0];

$r = mysql_query("SELECT pif_number FROM dean_poll_projects WHERE project_type='fndp' ORDER BY creation DESC");
$row = mysql_fetch_array($r);
$pif = 'FNDP'.sprintf('%04d', substr($row[0], 4)+1);

$q = "INSERT INTO dean_poll_projects (name, valid_users, result_email, callerid, dialplan_context, time_start, time_end, dialout_channel, pif_number, Notes, active, auth, creation, dialplan_extension, project_type, project_state, last_update, project_date, redial_interval, redial_rounds, redial_instruction) VALUES ('".$project_name."', '".$project_date."', '".$email."', '".$caller_string."', '".$dial_plan."', '".$start_time."', '".$end_time."', '".$default_dialout_channel."', '".$pif."', '".$comment."', '0', 'none', '".$now."', 's', 'fndp', 'initial', '".$now."', '".$project_date_full."', '".$redial_interval."', '".$redial_rounds."', '".$redial_instruction."')";
$r = mysql_query ($q); // Run the query

$r = mysql_query("SELECT id FROM dean_poll_projects WHERE creation='".$now."' AND project_type='fndp' LIMIT 1");
$row = mysql_fetch_array($r);
$id = $row[0];

$r = mysql_query("SELECT * FROM project_billing_info WHERE projectid=".$origin_id);
$row = mysql_fetch_array($r);

$bill_name = $row['billname'];
$bill_address = $row['billaddress'];
$bill_phone = $row['billphone'];
$bill_email = $row['billemail'];
$bill_type = $row['billtype'];
$bill_setup_types = $row['billsetuptypes'];
$bill_data_return_types = $row['billdatareturntypes'];
$bill_firstname_verify_types = $row['billfirstnameverifytypes'];
$bill_inbound_number_types = $row['billinboundnumbertypes'];
$bill_language_types = $row['billlanguagetypes'];

$q = "INSERT INTO project_billing_info (projectid, billname, billaddress, billphone, billemail, billtype, billsetuptypes, billdatareturntypes, billfirstnameverifytypes, billinboundnumbertypes, billlanguagetypes) VALUES ('".$id."', '".$bill_name."', '".$bill_address."', '".$bill_phone."', '".$bill_email."', '".$bill_type."', '".$bill_setup_types."', '".$bill_data_return_types."', '".$bill_firstname_verify_types."', '".$bill_inbound_number_types."', '".$bill_language_types."')";
$r = mysql_query ($q); // Run the query



$logging = "Cloned project ".$project_name;
$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");


mysql_close($con);


safe_redirect("index.php");
?>
