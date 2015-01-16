#!/usr/bin/php -q
<?php
// 
//check it every hour.
//error_reporting(E_ALL);
//ini_set('display_errors', '1');


date_default_timezone_set('UTC');

define ("DB_HOST", "localhost");
define ("DB_USER", "ivr_gui");
define ("DB_PASSWORD", "snow1in1the1summer");
define ("DB_IVR", "IVR_data_test"); //IVR_data_test


//notify email
$email = array("kevin.zhang@stratcom.ca", "tm.tech@stratcom.ca");

//notify title
$title = "IVR projects got deactivated";

$set_start_time_monday_friday_hour = 9;
$set_start_time_monday_friday_minute = 59;
$set_start_time_monday_friday_hour_minute = $set_start_time_monday_friday_hour*60+$set_start_time_monday_friday_minute;
$set_end_time_monday_friday_hour = 20;
$set_end_time_monday_friday_minute = 59;
$set_end_time_monday_friday_hour_minute = $set_end_time_monday_friday_hour*60+$set_end_time_monday_friday_minute;


$set_start_time_saturday_sunday_hour = 9;
$set_start_time_saturday_sunday_minute = 59;
$set_start_time_saturday_sunday_hour_minute = $set_start_time_saturday_sunday_hour*60+$set_start_time_saturday_sunday_minute;
$set_end_time_saturday_sunday_hour = 17;
$set_end_time_saturday_sunday_minute = 59;
$set_end_time_saturday_sunday_hour_minute = $set_end_time_saturday_sunday_hour*60+$set_end_time_saturday_sunday_minute;

/*
echo $set_start_time_monday_friday_hour_minute;
echo "<br/>";
echo $set_end_time_monday_friday_hour_minute;
echo "<br/>";
*/

function dbc_ivr() {
	$con = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if (!$con)
		die('Could not connect db: ' . mysql_error());
	if( !mysql_select_db(DB_IVR, $con) )
		die('Could not select db: ' . mysql_error());
	return $con;
}

$current_utc_time = time();
$day_of_week = date("N");

for ($timezone=-11;$timezone<=12;$timezone++) {
	$calculate_timezone_time = time() + ($timezone * 60 * 60);
	$calculate_hour = date("H", $calculate_timezone_time);
	$calculate_minute = date("i", $calculate_timezone_time);
	$calculate_hour_minute = $calculate_hour*60+$calculate_minute;
	/*
	echo $calculate_hour;
	echo "<br/>";
	echo $calculate_minute;
	echo "<br/>";
	*/
	//echo "calculate hour minute: ".$calculate_hour_minute;
	//echo "<br/>";
	
	
	if ($day_of_week <= 5) {
		if ($calculate_hour_minute <= $set_start_time_monday_friday_hour_minute) {
			//echo "timezone: ".$timezone;
			//echo "<br/>";
			$forbidden_timezones[] = $timezone;
		}
		if ($calculate_hour_minute >= $set_end_time_monday_friday_hour_minute) {
			//echo "timezone: ".$timezone;
			//echo "<br/>";
			$forbidden_timezones[] = $timezone;
		}
	} else {
		if ($calculate_hour_minute <= $set_start_time_saturday_sunday_hour_minute) {
			//echo "timezone: ".$timezone;
			//echo "<br/>";
			$forbidden_timezones[] = $timezone;
		}
		if ($calculate_hour_minute >= $set_end_time_saturday_sunday_hour_minute) {
			//echo "timezone: ".$timezone;
			//echo "<br/>";
			$forbidden_timezones[] = $timezone;
		}
	}
	
}



//print_r($forbidden_timezones);


$email_body = "the following projects got deactivated, please re-activate them if you are sure it's a mistake:<br/><br/>";

$con = dbc_ivr();

$query_active_projects = "SELECT id, name FROM dean_poll_projects WHERE active=1";
$result_active_projects = mysql_query($query_active_projects) OR die("Error selecting active projects");

$checker = false;

while ($row_active_projects = mysql_fetch_array($result_active_projects)) {
	foreach ($forbidden_timezones as $forbidden_timezone) {
		$query_forbidden_project = "SELECT * FROM dialout_numbers WHERE active=1 AND projectid=".$row_active_projects['id']." AND timezone=".$forbidden_timezone;
		$result_forbidden_project = mysql_query($query_forbidden_project) OR die("Error selecting forbidden project");
		if (mysql_num_rows($result_forbidden_project) > 0) {
			echo "there is forbidden numbers in project ".$row_active_projects['id']." with timezone ".$forbidden_timezone."\r\n";
			$email_body .= "there is forbidden numbers in project ".$row_active_projects['id']." with timezone ".$forbidden_timezone."<br/>";
			$query_deactivate = "UPDATE dean_poll_projects SET active=0, fndp_active=0 WHERE active=1 AND id=".$row_active_projects['id'];
			$result_deactivate = mysql_query($query_deactivate) OR die("Error deactivating project");
			$checker = true;
			break;
		}
	}
}

if ($checker) {
	require '/var/www/html/simpleivr/phpmailer/PHPMailerAutoload.php';

	$mail = new PHPMailer;

	$mail->From = 'alert@stratcom.ca';
	$mail->FromName = 'Stratcom Alert';
	foreach ($email as $item) {
		$mail->addAddress($item);               // Name is optional
	}

	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	$mail->isHTML(true);
	$mail->Subject = $title;
	$mail->Body    = $email_body;

	if(!$mail->send()) {
	   echo 'Message could not be sent.';
	   echo 'Mailer Error: ' . $mail->ErrorInfo;
	   exit;
	}
}


?>