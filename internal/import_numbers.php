<?php
//session_start();
if (!isset($_SESSION['user_ivr'])) {
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

if (isset($_POST['is_first_name']) && $_POST['is_first_name']) {
	$is_first_name = true;
} else {
	$is_first_name = false;
}


$active = false;
if (date("I")) {
	$time_zone = "Summer";
} else {
	$time_zone = "Winter";
}

//obtain DNC list
unset($dnc_numbers);

if (($handle = fopen("DNC.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$dnc_numbers[]=$data[0];
	}
	fclose($handle);
}

// fetch numbers.
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
				if ($is_first_name) {
					unset($data[1]);
				}
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
					if ($is_first_name) {
						$firstnames = $data[1];
						unset($data[1]);
					}
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
						if ($is_first_name) {
							$firstnames = mysql_real_escape_string($firstnames);
							mysql_query("INSERT INTO dialout_numbers(projectid, phonenumber, timezone, active, extra_fields, result, prov, temp) VALUES (".$id.", '".$phonenumber."', '".$phonenumber_timezone."', '".$active."', '".$records."', '', '', '".$firstnames."') ON DUPLICATE KEY UPDATE timezone='".$phonenumber_timezone."', active='".$active."', extra_fields='".$records."', result='', temp='".$firstnames."'") OR die("error importing first name records into dialout_numbers table");
							if (mysql_affected_rows() == 1) {
								$valid_numbers_imported++;
							} else if (mysql_affected_rows() == 2) {
								$valid_numbers_updated++;
							}
						} else {
							mysql_query("INSERT INTO dialout_numbers(projectid, phonenumber, timezone, active, extra_fields, result, prov) VALUES (".$id.", '".$phonenumber."', '".$phonenumber_timezone."', '".$active."', '".$records."', '', '') ON DUPLICATE KEY UPDATE timezone='".$phonenumber_timezone."', active='".$active."', extra_fields='".$records."', result=''") OR die("error importing records into dialout_numbers table");
							if (mysql_affected_rows() == 1) {
								$valid_numbers_imported++;
							} else if (mysql_affected_rows() == 2) {
								$valid_numbers_updated++;
							}
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
		//echo "valid import: ".$valid_numbers_imported."</br>";
		//echo "valid update: ".$valid_numbers_updated."</br>";
		//echo "invalid import: ".$invalid_numbers_imported."</br>";
		//echo "invalid update: ".$invalid_numbers_updated."</br>";
		$logging = "Import numbers for project ".$id.": valid import: ".$valid_numbers_imported.", valid update: ".$valid_numbers_updated.", invalid import: ".$invalid_numbers_imported.", invalid update: ".$invalid_numbers_updated;
		$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_SESSION['user_ivr_fndp']."', '".$logging."')");
	}
}

if (isset($_FILES['dncfile'])){
	if (($handle = fopen($_FILES['dncfile']['tmp_name'], "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$dnc_numbers_remove[]=formatPhone($data[0]);
		}
		fclose($handle);
		$dnc_remove_string = implode("','", $dnc_numbers_remove);
		$con = dbc_ivr();
		mysql_query("UPDATE dialout_numbers SET result='KILLED' WHERE projectid=".$id." AND result!='invalid' AND phonenumber IN ('".$dnc_remove_string."')");
		mysql_close($con);
	}
}

mysql_close($con);

safe_redirect($_SERVER['PHP_SELF']."?page=edit_project&id=".$id);
?>