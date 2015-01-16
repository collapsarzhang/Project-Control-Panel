<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}

include_once("includes/defines.inc.php");

$id = $_GET['projectid'];

$con = dbc_ivr();
$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
$r = mysql_query ($q);
$row = mysql_fetch_assoc($r);
if ($row['project_type'] != "fndp") {
	safe_redirect("index.php");
}
$project_name = $row["name"];
mysql_close($con);

$filename = $project_name.".zip";
if (copy("/home/project_backup/".$id.".zip", "reports/".$filename)) {
	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename='.$filename);
	header('Content-Length: ' . filesize("reports/".$filename));
	readfile("reports/".$filename);
} else {
	echo "failed";
}
exit;

?>