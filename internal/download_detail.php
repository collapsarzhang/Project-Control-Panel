<?php
session_start();
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}

include_once("includes/defines.inc.php");

$csv_output  = "Project ID".", ";
$csv_output .= "Last Attempt Time".", ";
$csv_output .= "Phone Number".", ";
$csv_output .= "Result".", ";
$csv_output .= "Attemps".", ";
$csv_output .= "Billsec in seconds".", ";


$csv_output .= "\n";

$con = dbc_cdr();

$values = mysql_query("SELECT COUNT(*) FROM cdr WHERE accountcode LIKE '".$_GET['projectid']."<\%>%'") OR die("error executing cdr SQL");
$rowr = mysql_fetch_row($values);

$cdr_records = array();

if ($rowr[0] > 0) {
	
	$con = dbc_cdr();
	$values = mysql_query("SELECT billsec, accountcode FROM cdr WHERE accountcode LIKE '".$_GET['projectid']."<\%>%'") OR die("error executing if SQL");
	while ($rowr = mysql_fetch_array($values)) {
		$pieces = explode("<%>", $rowr['accountcode']);
		$rowr['projectid'] = $pieces[0];
		$rowr['phonenumber'] = $pieces[1];
		$cdr_records[] = $rowr;
	}
	$con = dbc_ivr();

	foreach ($cdr_records as $cdr_record) {
		$single_billsec = ceil($cdr_record['billsec'] / 30) * 30;
		$values = mysql_query("SELECT result, attempts, lastattempt FROM dialout_numbers WHERE prov!='TEST' AND projectid=".$cdr_record['projectid']." AND phonenumber='".$cdr_record['phonenumber']."'") OR die("error executing if inside SQL");
		if (mysql_num_rows($values) > 0) {
			$rowr = mysql_fetch_array($values);
			$csv_output .= $cdr_record['projectid'].", ";
			$csv_output .= $rowr['lastattempt'].", ";
			$csv_output .= $cdr_record['phonenumber'].", ";
			$csv_output .= $rowr['result'].", ";
			$csv_output .= $rowr['attempts'].", ";
			$csv_output .= $cdr_record['billsec'].", ";
			
			$csv_output .= "\n";
		}
	}
	$values = mysql_query("SELECT projectid, phonenumber, result, attempts, lastattempt FROM dialout_numbers WHERE prov!='TEST' AND projectid=".$_GET['projectid']." AND (result is null OR result='')") OR die("error executing else SQL");
	while ($rowr = mysql_fetch_array($values)) {
		$csv_output .= $rowr['projectid'].", ";
		$csv_output .= $rowr['lastattempt'].", ";
		$csv_output .= $rowr['phonenumber'].", ";
		$csv_output .= "NIS OR NVM".", ";
		$csv_output .= $rowr['attempts'].", ";
		
		$csv_output .= "\n";
	}
} else {
	$con = dbc_ivr();

	$values = mysql_query("SELECT projectid, phonenumber, result, attempts, lastattempt FROM dialout_numbers WHERE prov!='TEST' AND projectid=".$_GET['projectid']." AND NOT (result is null OR result='')") OR die("error executing else SQL");
	while ($rowr = mysql_fetch_array($values)) {
		$csv_output .= $rowr['projectid'].", ";
		$csv_output .= $rowr['lastattempt'].", ";
		$csv_output .= $rowr['phonenumber'].", ";
		$csv_output .= $rowr['result'].", ";
		$csv_output .= $rowr['attempts'].", ";

		$csv_output .= "\n";
	}

	$values = mysql_query("SELECT projectid, phonenumber, result, attempts, lastattempt FROM dialout_numbers WHERE prov!='TEST' AND projectid=".$_GET['projectid']." AND (result is null OR result='')") OR die("error executing else SQL");
	while ($rowr = mysql_fetch_array($values)) {
		$csv_output .= $rowr['projectid'].", ";
		$csv_output .= $rowr['lastattempt'].", ";
		$csv_output .= $rowr['phonenumber'].", ";
		$csv_output .= "NIS OR NVM".", ";
		$csv_output .= $rowr['attempts'].", ";

		$csv_output .= "\n";
	}
}

mysql_close($con);
$file = $_GET['projectid']."_detail_report";
$filename = $file."_".date("Y-m-d",time());


header("Content-type: application/vnd.ms-excel");
header("Content-disposition: filename=".$filename.".csv");
print $csv_output;
exit;

    

?>