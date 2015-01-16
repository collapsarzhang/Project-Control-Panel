<?php
define ("DB_HOST", "localhost");
define ("DB_USER", "ivr");
define ("DB_PASSWORD", "ivr");
define ("DB_IVR", "IVR_data"); //IVR_data_test
define ("DB_CDR", "asteriskcdrdb");


define ("IS_MINUTE", true); //use false for second

$glb_dialplan_context = array(
'app-evan-bvm' => array('description' => 'BVM [regular one]', 'type' => 'BVM'),
'app-ibvm-press-1-to-repeat' => array('description' => 'IBVM [press 1 to repeat msg]', 'type' => 'IBVM'),
'app-ibvm-press-2-to-leave-message' => array('description' => 'IBVM [press 2 to leave msg]', 'type' => 'IBVM'),
'app-ibvm-press-3-to-dnc' => array('description' => 'IBVM [press 3 to add to DNC]', 'type' => 'IBVM'),
'app-ibvm-1-repeat-2-message' => array('description' => 'IBVM [1 to repeat, 2 to message]', 'type' => 'IBVM'),
'app-ibvm-1-repeat-2-message-3-dnc' => array('description' => 'IBVM [1 repeat, 2 message, 3 dnc]', 'type' => 'IBVM'),
'app-ibvm-1-repeat-3-dnc' => array('description' => 'IBVM [1 to repeat, 3 to dnc]', 'type' => 'IBVM'),
'app-ibvm-2-message-3-dnc' => array('description' => 'IBVM [2 to message, 3 to dnc]', 'type' => 'IBVM'),
'app-interactive-bvm-generic-thankyou' => array('description' => 'IBVM [1 question generic thankyou]', 'type' => 'IBVM'),
'app-ibvm-press-1-to-repeat-fr' => array('description' => 'IBVM Fr[press 1 to repeat msg]', 'type' => 'IBVM'),
'app-ibvm-press-2-to-leave-message-fr' => array('description' => 'IBVM Fr[press 2 to leave msg]', 'type' => 'IBVM'),
'app-ibvm-press-3-to-dnc-fr' => array('description' => 'IBVM Fr [press 3 to add to DNC]', 'type' => 'IBVM'),
'app-ibvm-1-repeat-2-message-fr' => array('description' => 'IBVM Fr [1 to repeat, 2 to message]', 'type' => 'IBVM'),
'app-ibvm-1-repeat-2-message-3-dnc-fr' => array('description' => 'IBVM Fr [1 repeat, 2 message, 3 dnc]', 'type' => 'IBVM'),
'app-ibvm-1-repeat-3-dnc-fr' => array('description' => 'IBVM Fr [1 to repeat, 3 to dnc]', 'type' => 'IBVM'),
'app-ibvm-2-message-3-dnc-fr' => array('description' => 'IBVM Fr [2 to message, 3 to dnc]', 'type' => 'IBVM'),
'app-interactive-bvm-generic-thankyou-fr' => array('description' => 'IBVM Fr [1 question generic thankyou]', 'type' => 'IBVM'),
'app-polling-outbound-pause-before-deliver' => array('description' => 'Polling [without name verify]', 'type' => 'Polling'),
'app-evan-test-text-to-speech' =>  array('description' => 'Polling [with name verify]', 'type' => 'Polling'),
);

$glb_dialout_channel = array('SIP/thinktel/NUMBER','SIP/TheBigOne/10001NUMBER','SIP/Gafachi/1NUMBER');
$default_dialout_channel = 'SIP/thinktel/NUMBER';

$glb_dialplan_extension = array('s');

$glb_poll_type = array('single-digit','dtmf','recording','postal','phone','announce');

$monthly_billsec_receive = array('tm.tech@stratcom.ca','evan.jiang@stratcom.ca','matt.smith@stratcom.ca','mike.dockstader@stratcom.ca','sonny.ramanaidu@stratcom.ca');

$keep_alive = 12000;
$allowed_login_attempts = 5;
$max_calls_per_sec = 8;
$max_prime_channel_percentage = 100;
$tech_email = 'tm.tech@stratcom.ca';
$max_rounds = 4;

$wav_file_path = "/var/spool/asterisk/PollingIVR/";
$live_wav_suffix = "00.wav";
$machine_wav_suffix = "99.wav";
$machine_wav_disable_suffix = "AM.wav";
$live_not_exist_msg = "Live message not exists, please upload it";
$live_exist_msg = "Live message exists, upload again to override";
$machine_not_exist_msg = "Answer Machine message not exists, please upload it";
$machine_exist_msg = "Answer Machine message exists, upload again to override";
$dir_not_exist_msg = "You can upload different messages for live pickup and answer machines, but most of the time it's fine to upload the same one. If you only upload either one, then it will cause an immediate hangup for the other.";

$consultants = array(
	'mike.dockstader' => array('name' => 'Mike Dockstader', 'email' => 'mike.dockstader@stratcom.ca', 'number' => '4163126019', 'recogniz' => 'mike_d', 'password' => 'q8v2kF'),
	'jonathan.bleackley' => array('name' => 'Jonathan Bleackley', 'email' => 'jonathan.bleackley@stratcom.ca', 'number' => '6043394069', 'recogniz' => 'jonathan_b', 'password' => 'PYk77p'),
	'angela.lee' => array('name' => 'Angela Lee', 'email' => 'angela.lee@stratcom.ca', 'number' => '6479869061', 'recogniz' => 'angela_l', 'password' => 'xAQuv1'),
	'matt.smith' => array('name' => 'Matt Smith', 'email' => 'matt.smith@stratcom.ca', 'number' => '4168775524', 'recogniz' => 'matt_s', 'password' => '3Oled8'),
	'kevin.zhang' => array('name' => 'Kevin Zhang', 'email' => 'kevin.zhang@stratcom.ca', 'number' => '7782333109', 'recogniz' => 'kevin_z', 'password' => 'V6J4Y6'),
	'evan.jiang' => array('name' => 'Evan Jiang', 'email' => 'evan.jiang@stratcom.ca', 'number' => '6048898597', 'recogniz' => 'evan_j', 'password' => 'con4cord'),
	'wilson.lei' => array('name' => 'Wilson Lei', 'email' => 'wilson.lei@stratcom.ca', 'number' => '7782333109', 'recogniz' => 'wilson_l', 'password' => 'scwl20*'),
);

$fndp_staff = array(
	'Voice.Broadcasting@ndp.ca',
	'ndpbvm@stratcom.ca',
);

$stratcom_report_staff = array(
	'ndpbvm@stratcom.ca',
	'sviatlana.vernikouskaya@stratcom.ca',
	'kevin.zhang@stratcom.ca',
);

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

function dbc_ivr() {
	$con = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if (!$con)
		die('Could not connect db: ' . mysql_error());
	if( !mysql_select_db(DB_IVR, $con) )
		die('Could not select db: ' . mysql_error());
	return $con;
}

function dbc_cdr() {
	$con_cdr = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if (!$con_cdr)
		die('Could not connect db: ' . mysql_error());
	if( !mysql_select_db(DB_CDR, $con_cdr) )
		die('Could not select db: ' . mysql_error());
	return $con_cdr;
}

function getDuration($file) {
	$fp = fopen($file, 'r');
	$size_in_bytes = filesize($file);
	fseek($fp, 20);
	$rawheader = fread($fp, 16);
	$header = unpack('vtype/vchannels/Vsamplerate/Vbytespersec/valignment/vbits',$rawheader);
	$sec = ceil($size_in_bytes/$header['bytespersec']);
	return $sec;
}

function my_trim($phone){
	//****remove non-digits******
	$phone = preg_replace('/\D/', '', $phone);
	// $phone = trim ($phone);
	// echo $phone." number is loaded <br />";
	//****remove first digits if it is 0 or 1
	if (substr($phone,0,1)==1 || substr($phone,0,1)==0){
		$phone = substr($phone,1,10);
		//echo $phone.' phone number got trimed <br />';
	}

	//*****return false if the number does not matach NANPA(north american number plan)
	$regex = '/^(?:1)?(?(?!(37|96))[2-9][0-8][0-9](?<!(11)))?[2-9][0-9]{2}(?<!(11))[0-9]{4}(?<!(555(01([0-9][0-9])|1212)))$/'; 
  
	if ( strlen($phone) != 10 ){
		$result[0] =FALSE;
		$result[1] =$phone;
		return $result;
    }
	else if(preg_match($regex, $phone)){
		//echo $phone.' Valid!<br />';
		$result[0] =TRUE;
		$result[1] =$phone;
		return $result;
	}
	else{
		$result[0] =FALSE;
		$result[1] =$phone;
		return $result;
	}
}

function rm_dup($m_array, $isvalid){
	if ($isvalid=='valid'){ 
		$num_of_row = count($m_array['num']);
		$m_array['num'] =  array_unique($m_array['num']);
		for($i=0; $i<$num_of_row; $i++ ){
			if (!isset($m_array['num'][$i])){
				unset ($m_array['offset'][$i]);
				unset ($m_array['province'][$i]);
				unset ($m_array['temp'][$i]);
			}
		}
		$m_array['num']= array_values($m_array['num']);
		$m_array['offset']= array_values($m_array['offset']);
		$m_array['province']= array_values($m_array['province']);
		$m_array['temp']= array_values($m_array['temp']);
	}
	else if ($isvalid=='invalid'){
		$num_of_row = count($m_array['num_t']);
		$m_array['num_t'] =  array_unique($m_array['num_t']);
		
		for($i=0;$i<$num_of_row;$i++){
			if (!isset($m_array['num_t'][$i] )){
				unset ( $m_array['comments'][$i]);
				unset ( $m_array['num_o'][$i]);
			}
		}
		$m_array['num_t']= array_values($m_array['num_t']);
		$m_array['num_o']= array_values($m_array['num_o']);
		$m_array['comments']= array_values($m_array['comments']);
	}
  
	else{
		echo '<h4 class="alert_error">Error in function rm_dup</h4>';
	}
	
	return $m_array;
}

function isvalid_questionid($questionid,$projectid){
	if ( is_numeric($questionid) ){
		if ($questionid!=0){  //the last question is 0
			$len =strlen($projectid);
			if( substr($questionid,0,$len) ==$projectid){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return true;//return true if questionid is 0
		}
	}
	else{
		return false;
	}
}

function safe_redirect($url, $exit=true) {
 
    // Only use the header redirection if headers are not already sent
    if (!headers_sent()){
 
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url);
 
        // Optional workaround for an IE bug (thanks Olav)
        header("Connection: close");
    }
 
    // HTML/JS Fallback:
    // If the header redirection did not work, try to use various methods other methods
 
    print '<html>';
    print '<head><title>Redirecting you...</title>';
    print '<meta http-equiv="Refresh" content="0;url='.$url.'" />';
    print '</head>';
    print '<body onload="location.replace(\''.$url.'\')">';
 
    // If the javascript and meta redirect did not work, 
    // the user can still click this link
    print 'You should be redirected to this URL:<br />';
    print "<a href=".$url.">".$url."</a><br /><br />";
 
    print 'If you are not, please click on the link above.<br />';    
 
    print '</body>';
    print '</html>';
 
    // Stop the script here (optional)
    if ($exit) exit;
}

function formatPhone($num) {
	$num = preg_replace('/[^0-9]/', '', $num);
	$num = substr($num, -10);
	return $num;
}

function isValidPhone($num) {
	$regex = '/^(?:1)?(?(?!(37|96))[2-9][0-8][0-9](?<!(11)))?[2-9][0-9]{2}(?<!(11))[0-9]{4}(?<!(555(01([0-9][0-9])|1212)))$/';
	if (strlen($num) == 10) {
		if (preg_match($regex, $num)) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
?>