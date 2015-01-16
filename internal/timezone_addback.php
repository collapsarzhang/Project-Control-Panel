<?php
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
$timezone = $_POST['action'];
$id = $_POST['id'];

$con = dbc_ivr();
$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
$r = mysql_query ($q);
$row = mysql_fetch_assoc($r);

$q = "UPDATE dialout_numbers SET result='' WHERE attempts=0 AND result='removed' AND prov!='TEST' AND timezone=".$timezone." AND projectid=".$id;
$result = mysql_query($q);
$logging = "Addback Timezone ".$timezone." for Project ".$id;
$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");

mysql_close($con);

safe_redirect($_SERVER['PHP_SELF']."?page=number_analysis&id=".$id);
?>