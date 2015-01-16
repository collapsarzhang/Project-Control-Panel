<?php
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
$areacode = $_POST['action'];
$id = $_POST['id'];

$con = dbc_ivr();
$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
$r = mysql_query ($q);
$row = mysql_fetch_assoc($r);

$q = "UPDATE dialout_numbers SET result='removed', active=0 WHERE attempts=0 AND (result is NULL OR result='') AND prov!='TEST' AND SUBSTRING(phonenumber,1,3)=".$areacode." AND projectid=".$id;
$result = mysql_query($q);
$logging = "Remove Areacode ".$areacode." for Project ".$id;
$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");

mysql_close($con);

safe_redirect($_SERVER['PHP_SELF']."?page=number_analysis&id=".$id);
?>
