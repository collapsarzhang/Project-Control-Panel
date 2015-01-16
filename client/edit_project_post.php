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
$project_date = date("md", strtotime($_POST['project_date']));
$project_date_full = date("Y-m-d", strtotime($_POST['project_date'])).' 00:00:00';
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$client_name = $_POST['client_name'];
$consultant_name = $_POST['consultant_name'];
$dial_plan = $_POST['dial_plan'];
$dial_plan_type = $glb_dialplan_context[$dial_plan]['type'];
$dial_plan_description = $glb_dialplan_context[$dial_plan]['description'];

/*
echo $id;
echo "</br>";
echo $project_date;
echo "</br>";
echo $pif;
echo "</br>";
echo $start_time;
echo "</br>";
echo $end_time;
echo "</br>";
echo $client_name;
echo "</br>";
echo $consultant_name;
echo "</br>";
echo $dial_plan;
echo "</br>";
*/

if (isset($_POST['activate_project']) && $_POST['activate_project']) {
	$activate_project = 1;
} else {
	$activate_project = 0;
}

//echo $activate_project;

$project_name = $dial_plan_type."_".$project_date."_".$client_name;
$email = $consultants[$_SESSION['user_ivr_fndp']]['email'];
$comment = $consultant_name."; ".$client_name;

if (isset($_POST['caller_name']) && $_POST['caller_name'] != "") {
	$caller_string = '"'.$_POST['caller_name'].'"<'.$_POST['caller_id'].'>';
	$caller_name = $_POST['caller_name'];
	$caller_id = $_POST['caller_id'];
} else {
	$caller_string = $_POST['caller_id'];
	$caller_name = "";
	$caller_id = $_POST['caller_id'];
}

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

if ($is_initial) {
	$r = mysql_query("SELECT NOW()");
	$row = mysql_fetch_array($r);
	$now = $row[0];

	$q = "UPDATE dean_poll_projects SET active=".$activate_project.", name='".$project_name."', valid_users='".$project_date."', result_email='".$email."', callerid='".$caller_string."', time_start='".$start_time."', time_end='".$end_time."', dialplan_context='".$dial_plan."', Notes='".$comment."', last_update='".$now."', project_date='".$project_date_full."', redial_interval='".$redial_interval."', redial_rounds='".$redial_rounds."', redial_instruction='".$redial_instruction."' WHERE id=".$id;
	$r = mysql_query ($q); // Run the query
	$logging = "Updated Project ".$id." from client interface";
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
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
