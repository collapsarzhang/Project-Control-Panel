<?php
session_start();
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}

include_once("includes/defines.inc.php");

$id = $_POST['id'];
$areacode = $_POST['action'];
$con = dbc_ivr();
$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
$r = mysql_query ($q);
$row = mysql_fetch_assoc($r);

if ($row['project_type'] != "fndp") {
	safe_redirect("index.php");
}

$project_name = $row['name'];


$csv_output  = "";
$csv_output .= "Phone Number".", ";

$csv_output .= "\n";


$q = "SELECT phonenumber FROM dialout_numbers WHERE attempts=0 AND (result is NULL OR result='') AND attempts=0 AND SUBSTRING(phonenumber,1,3)=".$areacode." AND projectid=".$id;
$r = mysql_query($q);
while ($row = mysql_fetch_array($r)) {
	$csv_output .= $row['phonenumber'].", ";
	$csv_output .= "\n";
}

mysql_close($con);
$file = $project_name."_areacode_".$areacode;
$filename = $file."_".date("Y-m-d",time());


header("Content-type: application/vnd.ms-excel");
header("Content-disposition: filename=".$filename.".csv");
print $csv_output;
exit;

?>