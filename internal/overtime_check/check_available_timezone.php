<?php



define ("DB_HOST", "localhost");
define ("DB_USER", "ivr_gui");
define ("DB_PASSWORD", "snow1in1the1summer");
define ("DB_IVR", "IVR_data_test"); //IVR_data_test



$set_start_time = "15:00";
$set_end_time = "21:00";
function get_available_timezone($set_start_time, $set_end_time) {
	date_default_timezone_set('UTC');
	$current_utc_time = time();
	$day_of_week = date("N");
	$start_time_minute = date("H", strtotime($set_start_time))*60+ date("i", strtotime($set_start_time));
	$end_time_minute = date("H", strtotime($set_end_time))*60+ date("i", strtotime($set_end_time));
	for ($timezone=-11;$timezone<=12;$timezone++) {
		$calculate_timezone_time = time() + ($timezone * 60 * 60);
		$calculate_hour = date("H", $calculate_timezone_time);
		$calculate_minute = date("i", $calculate_timezone_time);
		$calculate_hour_minute = $calculate_hour*60+$calculate_minute;
		if ($calculate_hour_minute > $start_time_minute AND $calculate_hour_minute < $end_time_minute) {
			return $timezone;
		}
	}
	return "-5";
}


print_r(get_available_timezone($set_start_time, $set_end_time));

?>