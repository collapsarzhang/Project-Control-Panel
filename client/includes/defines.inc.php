<?php
define ("DB_HOST", "localhost");
define ("DB_USER", "ivr");
define ("DB_PASSWORD", "ivr");
define ("DB_IVR", "IVR_data"); //IVR_data_test
define ("DB_CDR", "asteriskcdrdb");


define ("IS_MINUTE", true); //use false for second

$glb_dialplan_context = array(
'app-evan-bvm' => array('description' => 'BVM [regular]', 'type' => 'BVM', 'options' => 0),
'app-ibvm-press-1-to-repeat' => array('description' => 'IBVM {press 1 to repeat msg} En', 'type' => 'IBVM', 'options' => 1),
'app-ibvm-press-2-to-leave-message' => array('description' => 'IBVM {press 2 to leave msg} En', 'type' => 'IBVM', 'options' => 1),
'app-ibvm-press-3-to-dnc' => array('description' => 'IBVM {press 3 to add to DNC} En', 'type' => 'IBVM', 'options' => 1),
'app-ibvm-1-repeat-2-message' => array('description' => 'IBVM {1 to repeat, 2 to message} En', 'type' => 'IBVM', 'options' => 2),
'app-ibvm-1-repeat-2-message-3-dnc' => array('description' => 'IBVM {1 repeat, 2 message, 3 dnc} En', 'type' => 'IBVM', 'options' => 3),
'app-ibvm-1-repeat-3-dnc' => array('description' => 'IBVM {1 to repeat, 3 to dnc} En', 'type' => 'IBVM', 'options' => 2),
'app-ibvm-2-message-3-dnc' => array('description' => 'IBVM {2 to message, 3 to dnc} En', 'type' => 'IBVM', 'options' => 2),

'app-interactive-bvm-generic-thankyou' => array('description' => 'IBVM {1 question generic thankyou} En', 'type' => 'IBVM', 'options' => 1),
);

$bill_types = array(
	'Party',
	'MP_Office',
	'Riding_Association',
);

$bill_setup_types = array(
	'Standard',
	'Rush',
	'IVR',
);

$bill_data_return_types = array(
	'None',
	'Standard',
	'Rush',
);

$bill_firstname_verify_types = array(
	'No',
	'Yes',
);

$bill_inbound_number_types = array(
	'No',
	'Yes',
);

$bill_language_types = array(
	'English',
	'French',
);

$glb_dialout_channel = array('SIP/thinktel/NUMBER','SIP/TheBigOne/10001NUMBER','SIP/Gafachi/1NUMBER');
$default_dialout_channel = 'SIP/thinktel/NUMBER';

$glb_dialplan_extension = array('s');

$glb_poll_type = array('single-digit','dtmf','recording','postal','phone','announce');

$monthly_billsec_receive = array('kevin.zhang@stratcom.ca','tm.tech@stratcom.ca');

$keep_alive = 12000;
$allowed_login_attempts = 5;
$max_calls_per_sec = 8;
$max_prime_channel_percentage = 100;
$tech_email = 'tm.tech@stratcom.ca';

$max_redial_interval = 2*60;
$max_redial_rounds = 6;

$stratcom_staff = array(
	'kevin.zhang@stratcom.ca',
	'ndpbvm@stratcom.ca',
);

$stratcom_report_staff = array(
	'ndpbvm@stratcom.ca',
	'sviatlana.vernikouskaya@stratcom.ca',
	'kevin.zhang@stratcom.ca',
);

$wav_file_path = "/var/spool/asterisk/PollingIVR/";
$live_wav_suffix = "00.wav";
$machine_wav_suffix = "99.wav";
$machine_wav_disable_suffix = "AM.wav";

$live_not_exist_msg = "Live message not exists, please upload it";

$machine_not_exist_msg = "If no Answering Machine Message is uploaded, system will hang up when an answering machine is detected.";

$dir_not_exist_msg = "If no Answering Machine Message is uploaded, system will hang up when an answering machine is detected.";

$consultants = array(
	'tm.tech' => array('name' => 'TM TECH', 'email' => 'tm.tech@stratcom.ca', 'number' => '7782333109', 'recogniz' => 'tm_tech', 'password' => 'V6J4Y6'),
	'angela.lee' => array('name' => 'Angela Lee', 'email' => 'angela.lee@stratcom.ca', 'number' => '6479869061', 'recogniz' => 'angela_l', 'password' => 'xAQuv1'),
	'jonathan.bleackley' => array('name' => 'Jonathan Bleackley', 'email' => 'jonathan.bleackley@stratcom.ca', 'number' => '6043394069', 'recogniz' => 'jonathan_b', 'password' => 'PYk77p'),
	'mike.dockstader' => array('name' => 'Mike Dockstader', 'email' => 'mike.dockstader@stratcom.ca', 'number' => '4163126019', 'recogniz' => 'mike_d', 'password' => 'q8v2kF'),
	'pat.cutrone' => array('name' => 'Pat Cutrone', 'email' => 'pat.cutrone@ndp.ca', 'number' => '8199628228', 'recogniz' => 'pat_c', 'password' => 'Sn97p5'),
);

$warning_areacode = array(
	'867' => array('reason' => 'Yukon Distinct'),
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

function getDetailOutput($id) {
	$con = dbc_ivr();
	$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
	$r = mysql_query ($q);
	$row = mysql_fetch_assoc($r);
	
	$extra_field_titles = $row["extra_field_titles"];

	$csv_output  = "Last Attempt Time".", ";
	$csv_output .= "Phone Number".", ";
	$csv_output .= "Result".", ";
	$csv_output .= "Attemps".", ";
	//$csv_output .= "Billtime in seconds".", ";
	$csv_output .= $extra_field_titles.", ";

	$csv_output .= "\n";

	$con = dbc_cdr();

	$values = mysql_query("SELECT COUNT(*) FROM cdr WHERE accountcode LIKE '".$id."<\%>%'") OR die("error executing cdr SQL");
	$rowr = mysql_fetch_row($values);

	$cdr_records = array();

	if ($rowr[0] > 0) {
		
		$con = dbc_cdr();
		$values = mysql_query("SELECT billsec, accountcode FROM cdr WHERE accountcode LIKE '".$id."<\%>%'") OR die("error executing if SQL");
		while ($rowr = mysql_fetch_array($values)) {
			$pieces = explode("<%>", $rowr['accountcode']);
			$rowr['projectid'] = $pieces[0];
			$rowr['phonenumber'] = $pieces[1];
			$cdr_records[] = $rowr;
		}
		$con = dbc_ivr();

		foreach ($cdr_records as $cdr_record) {
			$single_billsec = ceil($cdr_record['billsec'] / 30) * 30;
			$values = mysql_query("SELECT result, attempts, lastattempt, extra_fields FROM dialout_numbers WHERE prov!='TEST' AND projectid=".$cdr_record['projectid']." AND phonenumber='".$cdr_record['phonenumber']."'") OR die("error executing if inside SQL");
			if (mysql_num_rows($values) > 0) {
				$rowr = mysql_fetch_array($values);
				$csv_output .= $rowr['lastattempt'].", ";
				$csv_output .= $cdr_record['phonenumber'].", ";
				$csv_output .= $rowr['result'].", ";
				$csv_output .= $rowr['attempts'].", ";
				//$csv_output .= $cdr_record['billsec'].", ";
				$csv_output .= $rowr['extra_fields'].", ";
				
				$csv_output .= "\n";
			}
		}
		$values = mysql_query("SELECT projectid, phonenumber, result, attempts, lastattempt, extra_fields FROM dialout_numbers WHERE prov!='TEST' AND projectid=".$id." AND result IS NULL") OR die("error executing else SQL");
		while ($rowr = mysql_fetch_array($values)) {
			$csv_output .= $rowr['lastattempt'].", ";
			$csv_output .= $rowr['phonenumber'].", ";
			$csv_output .= "NIS OR NVM".", ";
			$csv_output .= $rowr['attempts'].", ";
			$csv_output .= $rowr['extra_fields'].", ";
			
			$csv_output .= "\n";
		}
	} else {
		$con = dbc_ivr();

		$values = mysql_query("SELECT projectid, phonenumber, result, attempts, lastattempt, extra_fields FROM dialout_numbers WHERE prov!='TEST' AND projectid=".$id." AND result IS NOT NULL") OR die("error executing else SQL");
		while ($rowr = mysql_fetch_array($values)) {
			$csv_output .= $rowr['lastattempt'].", ";
			$csv_output .= $rowr['phonenumber'].", ";
			$csv_output .= $rowr['result'].", ";
			$csv_output .= $rowr['attempts'].", ";
			$csv_output .= $rowr['extra_fields'].", ";

			$csv_output .= "\n";
		}

		$values = mysql_query("SELECT projectid, phonenumber, result, attempts, lastattempt, extra_fields FROM dialout_numbers WHERE prov!='TEST' AND projectid=".$id." AND result IS NULL") OR die("error executing else SQL");
		while ($rowr = mysql_fetch_array($values)) {
			$csv_output .= $rowr['lastattempt'].", ";
			$csv_output .= $rowr['phonenumber'].", ";
			$csv_output .= "NIS OR NVM".", ";
			$csv_output .= $rowr['attempts'].", ";
			$csv_output .= $rowr['extra_fields'].", ";

			$csv_output .= "\n";
		}
	}

	mysql_close($con);

	return $csv_output;
}


function getInvoice($id, $invoice_type) {

	include_once ("/var/www/html/simpleivr/mpdf/mpdf.php");

	$glb_dialplan_context = array(
		'app-evan-bvm' => array('description' => 'BVM [regular]', 'type' => 'BVM', 'options' => 0),
		'app-ibvm-press-1-to-repeat' => array('description' => 'IBVM [press 1 to repeat msg]', 'type' => 'IBVM', 'options' => 1),
		'app-ibvm-press-2-to-leave-message' => array('description' => 'IBVM [press 2 to leave msg]', 'type' => 'IBVM', 'options' => 1),
		'app-ibvm-press-3-to-dnc' => array('description' => 'IBVM [press 3 to add to DNC]', 'type' => 'IBVM', 'options' => 1),
		'app-ibvm-1-repeat-2-message' => array('description' => 'IBVM [1 to repeat, 2 to message]', 'type' => 'IBVM', 'options' => 2),
		'app-ibvm-1-repeat-2-message-3-dnc' => array('description' => 'IBVM [1 repeat, 2 message, 3 dnc]', 'type' => 'IBVM', 'options' => 3),
		'app-ibvm-1-repeat-3-dnc' => array('description' => 'IBVM [1 to repeat, 3 to dnc]', 'type' => 'IBVM', 'options' => 2),
		'app-ibvm-2-message-3-dnc' => array('description' => 'IBVM [2 to message, 3 to dnc]', 'type' => 'IBVM', 'options' => 2),

		'app-interactive-bvm-generic-thankyou' => array('description' => 'IBVM [1 question generic thankyou]', 'type' => 'IBVM', 'options' => 1),
	);
	
	$con = dbc_ivr();
	
	$q = "SELECT * FROM project_billing_info WHERE projectid=".$id;
	$r = mysql_query($q);
	$row = mysql_fetch_array($r);
	$billname = $row['billname'];
	$billaddress = $row['billaddress'];
	$billphone = $row['billphone'];
	$billemail = $row['billemail'];
	
	$projecttype = $row['billtype'];
	$billsetuptypes = $row['billsetuptypes'];
	$billdatareturntypes = $row['billdatareturntypes'];
	$billfirstnameverifytypes = $row['billfirstnameverifytypes'];
	$billinboundnumbertypes = $row['billinboundnumbertypes'];
	$billlanguagetypes = $row['billlanguagetypes'];
	
	if ($projecttype == 'MP_Office') {
		$standard_setup_fee = 35;
	} 
	
	else {
		$standard_setup_fee = 5;
	}
	
	//$invoice_type could be either fndp or stratcom
	if ($invoice_type == 'stratcom') {
		$data_pull_standard_fee = 0;
		//$standard_setup_fee = 35;
		$data_pull_rush_fee = 0;
		$logo = '<img src="/var/www/html/fndp/images/logo.jpg">';
	} 
	  
	else {
		$data_pull_standard_fee = 25;
		$data_pull_rush_fee = 45;
		$logo = '&nbsp;';
	}

	$billsetuptypes_array = array(
		'Standard' => array(
			'Unit' => 1,
			'Price' => $standard_setup_fee
		),
		'Rush' => array(
			'Unit' => 1,
			'Price' => 175
		),
		'IVR' => array(
			'Unit' => 1,
			'Price' => 45
		),
	);
	
	$billdatareturntypes_array = array(
		'None' => array(
			'Unit' => 0,
			'Price' => 0
		),
		'Standard' => array(
			'Unit' => 1,
			'Price' => $data_pull_standard_fee
		),
		'Rush' => array(
			'Unit' => 1,
			'Price' => $data_pull_rush_fee
		),
	);
	
	$billfirstnameverifytypes_array = array(
		'No' => array(
			'Unit' => 0,
			'Price' => 0
		),
		'Yes' => array(
			'Unit' => 1,
			'Price' => 250
		),
	);
	
	$billinboundnumbertypes_array = array(
		'No' => array(
			'Unit' => 0,
			'Price' => 0
		),
		'Yes' => array(
			'Unit' => 1,
			'Price' => 25
		),
	);

	$wav_file_path = "/var/spool/asterisk/PollingIVR/";
	$live_wav_suffix = "00.wav";
	$machine_wav_suffix = "99.wav";

	$result = mysql_query("SELECT * FROM dean_poll_projects WHERE id=".$id);
	$row = mysql_fetch_array($result);
	$projectname = $row['name'];
	$pif = $row['pif_number'];
	$dialplanraw = $row['dialplan_context'];
	if (isset($glb_dialplan_context[$dialplanraw])) {
		$dialplanname = $glb_dialplan_context[$dialplanraw]['description'];
	} else {
		$dialplanname = "Custom IVR";
	}

	if ($dialplanname == "Custom IVR") {
		$livemessageduration = "Custom IVR";
		$ammessageduration = "Custom IVR";
		$livemessagedurationwithivr = "Custom IVR";
	} else {
		$livemessagepath = $wav_file_path.$id."/".$id.$live_wav_suffix;
		$ammessagepath = $wav_file_path.$id."/".$id.$machine_wav_suffix;
		if (isset($livemessagepath)) {
			$livemessageduration = getDuration($livemessagepath);
		} else {
			$livemessageduration = "N/A";
		}
		if (isset($ammessagepath)) {
			$ammessageduration = getDuration($ammessagepath);
		} else {
			$ammessageduration = "N/A";
		}
		if ($livemessageduration == "N/A") {
			$livemessagedurationwithivr = "N/A";
		} else {
			$livemessagedurationwithivr = $livemessageduration + $glb_dialplan_context[$dialplanraw]['options']*5;
		}
	}

	if ($livemessagedurationwithivr > 60) {
		$priceperlivemessage = (0.055/60)*$livemessagedurationwithivr;
	} else {
		$priceperlivemessage = 0.055;
	}
	$live_subtotal = $priceperlivemessage;
	$priceperlivemessage = "$".$priceperlivemessage;

	if ($ammessageduration > 60) {
		$priceperammessage = (0.055/60)*$ammessageduration;
	} else {
		$priceperammessage = 0.055;
	}
	$am_subtotal = $priceperammessage;
	$priceperammessage = "$".$priceperammessage;

	if ($livemessageduration != "Custom IVR" AND $livemessageduration != "N/A") {
		$livemessageduration = $livemessageduration." seconds";
		$livelength = $livemessageduration;
	}
	if ($ammessageduration != "Custom IVR" AND $ammessageduration != "N/A") {
		$ammessageduration = $ammessageduration." seconds";
	}
	if ($livemessagedurationwithivr != "Custom IVR" AND $livemessagedurationwithivr != "N/A") {
		$livemessagedurationwithivr = $livemessagedurationwithivr." seconds";
		$livelength = $livemessagedurationwithivr;
	}

	$callerstring = $row['callerid'];
	$matches = array();
	preg_match('/"(.*?)"/s', $callerstring, $matches);
	if (isset($matches[1])) {
		$callername = $matches[1];
	} else {
		$callername = "";
	}
	preg_match('/<(.*?)>/s', $callerstring, $matches);
	if (isset($matches[1])) {
		$callerid = $matches[1];
	} else {
		$callerid = $callerstring;
	}

	$result = mysql_query("SELECT COUNT(*) FROM dnc_list WHERE projectid=".$id);
	$row = mysql_fetch_array($result);
	$DNC_added = $row[0];

	$result = mysql_query("SELECT COUNT(*) FROM dialout_numbers WHERE prov!='TEST' AND result!='invalid' AND result!='removed' AND projectid=".$id);
	$row = mysql_fetch_array($result);
	$total_num = $row[0];

	$result = mysql_query("SELECT COUNT(*) FROM dialout_numbers WHERE prov!='TEST' AND (result='HUMAN' OR result like'PRESS%') AND projectid=".$id);
	$row = mysql_fetch_array($result);
	$num_human = $row[0];

	$result = mysql_query("SELECT COUNT(*) FROM dialout_numbers WHERE prov!='TEST' AND (result='MACHINE' OR result='NOTSURE') AND projectid=".$id);
	$row = mysql_fetch_array($result);
	$num_machine = $row[0];

	$result = mysql_query("SELECT COUNT(*) FROM dialout_numbers WHERE prov!='TEST' AND (result is NULL OR result='') AND projectid=".$id);
	$row = mysql_fetch_array($result);
	$num_noreach = $row[0];

	$result=mysql_query("SELECT MAX(lastattempt) FROM dialout_numbers WHERE prov!='TEST' AND projectid=".$id);
	$row = mysql_fetch_array($result);
	$projectend = substr($row[0],0,10);

	$result=mysql_query("SELECT MIN(lastattempt) FROM dialout_numbers WHERE prov!='TEST' AND attempts>0 AND projectid=".$id);
	$row = mysql_fetch_array($result);
	$projectstart = substr($row[0],0,10);

	mysql_close($con);
	
	$live_subtotal = $live_subtotal*$num_human;
	$am_subtotal = $am_subtotal*$num_machine;

	$num_delivered = $num_human + $num_machine;
	if ($num_delivered > 0) {
		$connectpercentage = number_format(($num_delivered/$total_num)*100, 0)."%";
		$amindelivered = number_format(($num_machine/$num_delivered)*100, 1)."%";
		$humanindelivered = number_format(($num_human/$num_delivered)*100, 1)."%";
	} else {
		$connectpercentage = "0%";
		$amindelivered = "0%";
		$humanindelivered = "0%";
	}

	$aminall = number_format(($num_machine/$total_num)*100, 1)."%";
	$humaninall = number_format(($num_human/$total_num)*100, 1)."%";
	$undeliveredinall = number_format(($num_noreach/$total_num)*100, 1)."%";
	
	$total_price = $live_subtotal + $am_subtotal + $billsetuptypes_array[$billsetuptypes]['Price'] + $billdatareturntypes_array[$billdatareturntypes]['Price'] + $billfirstnameverifytypes_array[$billfirstnameverifytypes]['Price'] + $billinboundnumbertypes_array[$billinboundnumbertypes]['Price'];
	
	$total_price = number_format($total_price, 2);
	$live_subtotal = number_format($live_subtotal, 2);
	$am_subtotal = number_format($am_subtotal, 2);

	$reportdate = date('Y-m-d');

	$mpdf=new mPDF('UTF-8','Letter','','',20,15,48,25,10,10); 
	$mpdf->SetTitle("STRATCOM NDP BVM Report");
	$mpdf->SetAuthor("STRATCOM");
	$mpdf->SetDisplayMode('fullpage');
	
	

	$html = '
	<html>
	<head>
	<style>
	body {font-family: sans-serif;
		font-size: 10pt;
	}
	p {    margin: 0pt;
	}
	td { vertical-align: top; }
	.items td {
		border-left: 0.1mm solid #000000;
		border-right: 0.1mm solid #000000;
	}
	table thead td { background-color: #EEEEEE;
		text-align: center;
		border: 0.1mm solid #000000;
	}
	.items td.blanktotal {
		background-color: #FFFFFF;
		border: 0mm none #000000;
		border-top: 0.1mm solid #000000;
		border-right: 0.1mm solid #000000;
	}
	.items td.totals {
		text-align: right;
		border: 0.1mm solid #000000;
	}
	</style>
	</head>
	<body>

	<!--mpdf
	<htmlpageheader name="myheader">
	<table width="100%">
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>'.$logo.'</td></tr>
	</table>
	</htmlpageheader>

	<htmlpagefooter name="myfooter">
	</htmlpagefooter>

	<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
	<sethtmlpagefooter name="myfooter" value="on" />
	mpdf-->


	<table width="100%" style="font-size: 11pt;">
	<tr>
	<td align="left" width="20%"></td>
	<td align="left" width="20%"></td>
	<td align="left" width="20%"></td>
	<td align="left" width="20%"></td>
	<td align="left" width="20%"></td>
	</tr>
	<tr>
	<td>Billing Name:</td>
	<td colspan=4>'.$billname.'</td>
	</tr>
	<tr>
	<td>Billing Address:</td>
	<td colspan=4>'.$billaddress.'</td>
	</tr>
	<tr>
	<td>Billing Phone:</td>
	<td colspan=4>'.$billphone.'</td>
	</tr>
	<tr>
	<td>Billing Email:</td>
	<td colspan=4>'.$billemail.'</td>
	</tr>
	
	<tr>
	<td>&nbsp;</td>
	</tr>
	
	<tr>
	<td><b>BVM Information</b></td>
	</tr>
	
	<tr>
	<td>&nbsp;</td>
	</tr>
	
	<tr>
	<td>Project Name:</td>
	<td colspan=4>'.$projectname.'</td>
	</tr>
	<tr>
	<td>Project #:</td>
	<td colspan=4>'.$pif.'</td>
	</tr>

	<tr>
	<td>Project Type:</td>
	<td>'.$projecttype.'</td>
	<td>List Size:</td>
	<td>'.$total_num.'</td>
	</tr>
	
	<tr>
	<td colspan=3>Live Message Length (IVR Options Included):</td>
	<td colspan=2>'.$livelength.'</td>
	</tr>
	
	<tr>
	<td colspan=3>Answer Machine Message Length:</td>
	<td colspan=2>'.$ammessageduration.'</td>
	</tr>
	
	<tr>
	<td>Dial Plan:</td>
	<td colspan=4>'.$dialplanname.'</td>
	</tr>
	
	<tr>
	<td>Project Start Date:</td>
	<td>'.$projectstart.'</td>
	<td>Caller ID #:</td>
	<td>'.$callerid.'</td>
	</tr>
	<tr>
	<td>Project End Date:</td>
	<td>'.$projectend.'</td>
	<td>Caller ID Name:</td>
	<td>'.$callername.'</td>
	</tr>

	<tr>
	<td>&nbsp;</td>
	</tr>
	
	<tr>
	<td><b>Charges</b></td>
	</tr>
	
	<tr>
	<td>&nbsp;</td>
	</tr>
	
	<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>Units</td>
	<td>Price/Unit</td>
	<td>Subtotal</td>
	</tr>

	<tr>
	<td>Setup</td>
	<td>'.$billsetuptypes.'</td>
	<td>'.$billsetuptypes_array[$billsetuptypes]['Unit'].'</td>
	<td>$'.$billsetuptypes_array[$billsetuptypes]['Price'].'</td>
	<td>$'.$billsetuptypes_array[$billsetuptypes]['Price'].'</td>
	</tr>
	
	<tr>
	<td>List/Data Pull</td>
	<td>'.$billdatareturntypes.'</td>
	<td>'.$billdatareturntypes_array[$billdatareturntypes]['Unit'].'</td>
	<td>$'.$billdatareturntypes_array[$billdatareturntypes]['Price'].'</td>
	<td>$'.$billdatareturntypes_array[$billdatareturntypes]['Price'].'</td>
	</tr>
	
	<tr>
	<td>Inbound #</td>
	<td>'.$billinboundnumbertypes.'</td>
	<td>'.$billinboundnumbertypes_array[$billinboundnumbertypes]['Unit'].'</td>
	<td>$'.$billinboundnumbertypes_array[$billinboundnumbertypes]['Price'].'</td>
	<td>$'.$billinboundnumbertypes_array[$billinboundnumbertypes]['Price'].'</td>
	</tr>
	
	<tr>
	<td>First Name Verify</td>
	<td>'.$billfirstnameverifytypes.'</td>
	<td>'.$billfirstnameverifytypes_array[$billfirstnameverifytypes]['Unit'].'</td>
	<td>$'.$billfirstnameverifytypes_array[$billfirstnameverifytypes]['Price'].'</td>
	<td>$'.$billfirstnameverifytypes_array[$billfirstnameverifytypes]['Price'].'</td>
	</tr>
	
	<tr>
	<td>&nbsp;</td>
	</tr>
	
	<tr>
	<td colspan=2>Live Messages Delivered</td>
	<td>'.$num_human.'</td>
	<td>'.$priceperlivemessage.'</td>
	<td>$'.$live_subtotal.'</td>
	</tr>
	
	<tr>
	<td colspan=2>Answer Machine Messages Delivered</td>
	<td>'.$num_machine.'</td>
	<td>'.$priceperammessage.'</td>
	<td>$'.$am_subtotal.'</td>
	</tr>
	
	<tr>
	<td>&nbsp;</td>
	</tr>
	
	<tr>
	<td colspan=2>TOTAL (Applicable Taxes not Included)</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>$'.$total_price.'</td>
	</tr>


	</tbody>
	</table>
	</body>
	</html>
	';

	$mpdf->WriteHTML($html);
	$fname = "reports/".$invoice_type."_invoice_".$projectname."_".date("M_d").".pdf";
	$mpdf->Output($fname,'F');

	return $fname;
}
?>