<?php
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
$calls_per_second = $_POST['calls_per_second'];

$channel_percentage = $_POST['channel_percentage'];

$con = dbc_ivr();

$q = "UPDATE config SET value='$calls_per_second' WHERE id=3";		
$r1 = mysql_query ($q); // Run the query.

$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr']."', '".mysql_real_escape_string($q)."')");

$q = "UPDATE config SET value='$channel_percentage' WHERE id=15";		
$r4 = mysql_query ($q); // Run the query.

$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr']."', '".mysql_real_escape_string($q)."')");

mysql_close($con);

safe_redirect($_SERVER['PHP_SELF']);
?>