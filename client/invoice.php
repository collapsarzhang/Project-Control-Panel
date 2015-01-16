<?php
date_default_timezone_set('America/Vancouver');
$vancouver_date = date('Y-m-d').' 00:00:00';

date_default_timezone_set('UTC');
$current_utc_time = time();


$stratcom_report_staff = array(
	'kevin.zhang@stratcom.ca'
);



define ("DB_HOST", "localhost");
define ("DB_USER", "ivr_gui");
define ("DB_PASSWORD", "snow1in1the1summer");
define ("DB_IVR", "IVR_data"); //IVR_data_test
define ("DB_CDR", "asteriskcdrdb");

$con = dbc_ivr();

$id = 753;
$invoice_type = 'stratcom';
mkdir('/var/www/html/fndp/archive/'.$id.'/');
$invoice_name = getInvoice($id, $invoice_type);



require '/var/www/html/simpleivr/phpmailer/PHPMailerAutoload.php';
$mail = new PHPMailer;
$mail->From = 'tm.tech@stratcom.ca';
$mail->FromName = 'TM Tech';
foreach ($stratcom_report_staff as $item) {
	$mail->addAddress($item);
}
$mail->WordWrap = 50;
$mail->addAttachment($invoice_name);

$mail->isHTML(true);
$mail->Subject = "Project ".$id." Report";
$mail->Body = "Please see attachment";
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
   exit;
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
	} else {
		$standard_setup_fee = 5;
	}
	
	if ($invoice_type == 'stratcom') {
		$data_pull_standard_fee = 0;
		$data_pull_rush_fee = 0;
	} else {
		$data_pull_standard_fee = 25;
		$data_pull_rush_fee = 45;
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
		$priceperlivemessage = round((0.055/60)*$livemessagedurationwithivr, 4);
	} else {
		$priceperlivemessage = 0.055;
	}
	$live_subtotal = $priceperlivemessage;
	$priceperlivemessage = "$".$priceperlivemessage;

	if ($ammessageduration > 60) {
		$priceperammessage = round((0.055/60)*$ammessageduration, 4);
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
	
	$total_price = round($total_price, 2);

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
	<tr><td><img src="/var/www/html/fndp/images/logo.jpg"></td></tr>
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
	$fname = "/var/www/html/fndp/archive/".$id."/toClient_".$projectname."_".date("M_d").".pdf";
	$mpdf->Output($fname,'F');

	return $fname;
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

function getDetailOutput($id) {
	$con = dbc_ivr();
	$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
	$r = mysql_query ($q);
	$row = mysql_fetch_assoc($r);
	
	$project_name = $row["name"];
	$extra_field_titles = $row["extra_field_titles"];

	$csv_output  = "Last Attempt Time".", ";
	$csv_output .= "Phone Number".", ";
	$csv_output .= "Result".", ";
	$csv_output .= "Attemps".", ";
	$csv_output .= "Billtime in seconds".", ";
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
				$csv_output .= $cdr_record['billsec'].", ";
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

	$output_name = '/var/www/html/fndp/archive/'.$id.'/'.$project_name.'_Detail_Report.csv';

	file_put_contents($output_name, $csv_output);
	
	return $output_name;
}

?>