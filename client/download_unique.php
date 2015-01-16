<?php
session_start();
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}

include_once("includes/defines.inc.php");

$cur_projectid=$_GET['projectid'];
$con = dbc_ivr();
$q = "SELECT * FROM dean_poll_projects WHERE id=".$cur_projectid;
$r = mysql_query ($q);
$row = mysql_fetch_assoc($r);
if ($row['project_type'] != "fndp") {
	safe_redirect("index.php");
}
$project_name=$row["name"];

$csv_output .= "Last Attempt Time".", ";
$csv_output .= "Phone Number".", ";
$csv_output .= "Result".", ";
$csv_output .= "Attemps".", ";


$csv_output .= "\n";


$con = dbc_ivr();

$values = mysql_query("SELECT projectid, phonenumber, result, attempts, lastattempt FROM dialout_numbers WHERE result='".$_GET['type']."' AND prov!='TEST' AND projectid=".$_GET['projectid']." AND result IS NOT NULL") OR die("error executing else SQL");
while ($rowr = mysql_fetch_array($values)) {
	$csv_output .= $rowr['lastattempt'].", ";
	$csv_output .= $rowr['phonenumber'].", ";
	$csv_output .= $rowr['result'].", ";
	$csv_output .= $rowr['attempts'].", ";

	$csv_output .= "\n";
}

$values = mysql_query("SELECT projectid, phonenumber, result, attempts, lastattempt FROM dialout_numbers WHERE result='".$_GET['type']."' AND prov!='TEST' AND projectid=".$_GET['projectid']." AND result IS NULL") OR die("error executing else SQL");
while ($rowr = mysql_fetch_array($values)) {
	$csv_output .= $rowr['lastattempt'].", ";
	$csv_output .= $rowr['phonenumber'].", ";
	$csv_output .= "NIS OR NVM".", ";
	$csv_output .= $rowr['attempts'].", ";

	$csv_output .= "\n";
}


mysql_close($con);
$file = "Detail_report_for_".$project_name."_".$_GET['type'];
$filename = $file."_".date("Y-m-d",time());


header("Content-type: application/vnd.ms-excel");
header("Content-disposition: filename=".$filename.".csv");
print $csv_output;
exit;

    

?>