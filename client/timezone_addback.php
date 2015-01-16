<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
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
if ($row['project_type'] != "fndp") {
	safe_redirect("index.php");
}

$q = "UPDATE dialout_numbers SET result='' WHERE attempts=0 AND result='removed' AND prov!='TEST' AND timezone=".$timezone." AND projectid=".$id;
$result = mysql_query($q);
$logging = "Addback Timezone ".$timezone." for Project ".$id;
$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");

mysql_close($con);

?>

<script>
	function submitForm(projectid)
	{
		$('#projectform').submit();
	}
</script>
<form method="POST" id="projectform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="page" value="number_analysis">
	<input type="hidden" id="projectid" name="id" value=<?php echo $id; ?>>
<form>
<script>
	submitForm()
</script>