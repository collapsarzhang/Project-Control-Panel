<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
$id = $_POST['id'];

$con = dbc_ivr();
$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
$r = mysql_query ($q);
$row = mysql_fetch_assoc($r);

$test_timezone = get_available_timezone($row['time_start'], $row['time_end']);

$is_test = false;

foreach ($consultants as $username => $item) {
	$q = "SELECT * FROM dialout_numbers WHERE projectid=".$id." AND phonenumber='".$item['number']."'";
	$r = mysql_query ($q); // Run the query
	if (mysql_num_rows($r) > 0) {
		$update=true;
	} else {
		$update=false;
	}
	if (isset($_POST[$item['recogniz']]) && $_POST[$item['recogniz']]) {
		$is_test = true;
		if ($update) {
			$q = "UPDATE dialout_numbers SET active=1, result='', timezone='".$test_timezone."' WHERE phonenumber=".$item['number']." AND projectid=".$id;
		} else {
			$q = "INSERT INTO dialout_numbers (projectid, phonenumber,timezone,active,result,prov) VALUES (".$id.",'".$item['number']."','".$test_timezone."',1,'','TEST')";
		}
	} else {
		if ($update) {
			$q = "UPDATE dialout_numbers SET active=0, result='invalid', timezone='".$test_timezone."' WHERE phonenumber=".$item['number']." AND projectid=".$id;
		} else {
			$q = "INSERT INTO dialout_numbers (projectid, phonenumber,timezone,active,result,prov) VALUES (".$id.",'".$item['number']."','".$test_timezone."',0,'invalid','TEST')";
		}
	}
	$r = mysql_query ($q); // Run the query
}

if (isset($_POST['test_number']) AND strlen(formatPhone($_POST['test_number'])) == 10) {
	$is_test = true;
	$number = $_POST['test_number'];
	$logging = "Added Test Number ".$number." for Project ".$row['id'];
	$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
	$q = "SELECT * FROM dialout_numbers WHERE projectid=".$id." AND phonenumber='".$number."'";
	$r = mysql_query ($q); // Run the query
	if (mysql_num_rows($r) > 0) {
		$update=true;
	} else {
		$update=false;
	}
	if ($update) {
		$q = "UPDATE dialout_numbers SET active=1, result='', timezone='".$test_timezone."' WHERE phonenumber=".$number." AND projectid=".$id;
	} else {
		$q = "INSERT INTO dialout_numbers (projectid, phonenumber,timezone,active,result,prov) VALUES (".$id.",'".$number."','".$test_timezone."',1,'','TEST')";
	}
	$r = mysql_query ($q);
}

if ($is_test) {
	$q = "UPDATE dean_poll_projects SET active=1 WHERE id=".$id;
	$r = mysql_query ($q); // Run the query
}

mysql_close($con);
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