<?php
//session_start();
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
//require_once("includes/defines.inc.php");
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$con = dbc_ivr();

$id = $_POST['id'];
$active = false;
if (date("I")) {
	$time_zone = "Summer";
} else {
	$time_zone = "Winter";
}

$project_dnc_dir = 'dnc/'.$id;
$project_dnc_file = 'dnc/'.$id.'/DNC.csv';

if (isset($_FILES['dncfile']) ){
	$allowedExts = array("csv");
	$temp = explode(".", $_FILES["dncfile"]["name"]);
	$extension = end($temp);
	$dnc_numbers_upload = "";
	if (in_array($extension, $allowedExts)) {
		if (($handle = fopen($_FILES['dncfile']['tmp_name'], "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$dnc_numbers_upload .= formatPhone($data[0]).", "."\n";
			}
			mkdir($project_dnc_dir);
			file_put_contents($project_dnc_file, $dnc_numbers_upload);
		}
	}
}

if (($handle = fopen("dnc/DNC.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$dnc_numbers[]=formatPhone($data[0]);
	}
	fclose($handle);
}

if (isset($_FILES['userfile'])){
	if (($handle = fopen($_FILES['userfile']['tmp_name'], "r")) !== FALSE) {
		$result = mysql_query("SELECT * FROM TimeZones") OR die("error retrieving timezones");
		$utc_table = array();
		while($row = mysql_fetch_array($result)){           
			if($time_zone == 'Winter'){
				$utc_table[$row['AreaCode']] = $row['UTCWinter'];
			}
			else{
				$utc_table[$row['AreaCode']] = $row['UTCSummer'];
			}
		}
		$firstrow = true;
		$valid_numbers_imported = 0;
		$valid_numbers_updated = 0;
		$invalid_numbers_imported = 0;
		$invalid_numbers_updated = 0;
		while (($data = fgetcsv($handle)) !== FALSE) {
			$records = "";
			$header = "";
			if ($firstrow) {
				//echo "first column: ".$data[0].", ";
				unset($data[0]);
				foreach ($data as $item) {
					$header .= $item.", ";
				}
				$header = trim($header, ", ");
				//echo $header;
				//echo "</br>";
				mysql_query("UPDATE dean_poll_projects SET extra_field_titles='".$header."' WHERE id=".$id) OR die("error importing extra field titles into dean_poll_projects table");
				$firstrow=false;
			} else {
				$phonenumber = formatPhone($data[0]);
				//echo "first column: ".$phonenumber,", ";
				if (!in_array($phonenumber, $dnc_numbers)) {
					unset($data[0]);
					foreach ($data as $item) {
						$records .= $item.", ";
					}
					$records = trim($records, ", ");
					//echo $records;
					//echo "</br>";

					$phonenumber = mysql_real_escape_string($phonenumber);
					$records = mysql_real_escape_string($records);
					
					if (isValidPhone($phonenumber)) {
						$phonenumber_timezone = $utc_table[substr($phonenumber, 0, 3)];
						mysql_query("INSERT INTO dialout_numbers(projectid, phonenumber, timezone, active, extra_fields, result, prov) VALUES (".$id.", '".$phonenumber."', '".$phonenumber_timezone."', '".$active."', '".$records."', '', '') ON DUPLICATE KEY UPDATE timezone='".$phonenumber_timezone."', active='".$active."', extra_fields='".$records."', result=''") OR die("error importing records into dialout_numbers table");
						if (mysql_affected_rows() == 1) {
							$valid_numbers_imported++;
						} else if (mysql_affected_rows() == 2) {
							$valid_numbers_updated++;
						}
					} else {
						mysql_query("INSERT INTO dialout_numbers(projectid, phonenumber, timezone, active, extra_fields, result, prov) VALUES (".$id.", '".$phonenumber."', '".'0'."', '".$active."', '".$records."', 'invalid', '') ON DUPLICATE KEY UPDATE timezone='".'0'."', active='".$active."', extra_fields='".$records."', result='invalid'") OR die("error importing records into dialout_numbers table");
						if (mysql_affected_rows() == 1) {
							$invalid_numbers_imported++;
						} else if (mysql_affected_rows() == 2) {
							$invalid_numbers_updated++;
						}
					}
				}
			}
		}
		fclose($handle);
		

		$logging = "Import numbers for project ".$id.": valid import: ".$valid_numbers_imported.", valid update: ".$valid_numbers_updated.", invalid import: ".$invalid_numbers_imported.", invalid update: ".$invalid_numbers_updated;
		$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");

		$r = mysql_query("SELECT NOW()");
		$row = mysql_fetch_array($r);
		$now = $row[0];
		$r = mysql_query("UPDATE dean_poll_projects SET list_last_upload='".$now."' WHERE id=".$id) OR die("error updating list import time");
	}
}


if (($handle = fopen($project_dnc_file, "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$dnc_numbers_remove[]=formatPhone($data[0]);
	}
	fclose($handle);
	$dnc_remove_string = implode("','", $dnc_numbers_remove);
	mysql_query("UPDATE dialout_numbers SET result='KILLED' WHERE projectid=".$id." AND result!='invalid' AND phonenumber IN ('".$dnc_remove_string."')");
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