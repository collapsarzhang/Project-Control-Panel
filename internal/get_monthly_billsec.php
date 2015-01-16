<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '256M');


include_once("includes/defines.inc.php");

if (isset($_GET['month'])) {
	$time = mktime(0, 0, 0, $_GET['month']);
	$month = strftime("%m", $time);
	$next_time = mktime(0, 0, 0, $_GET['month']+1);
	$next_month = strftime("%m", $next_time);
	$year = date("Y");
	$year_month = $year."-".$month;
	$next_year_month = $year."-".$next_month;
} else {
	exit;
}


$csv_output  = "";
$cdr_records = array();
$projectids = array();
/*
$projects = array();
$values = mysql_query("SELECT id, name, pif_number FROM dean_poll_projects") OR die("error executing SQL");
while ($rowr = mysql_fetch_row($values)) {
	$projects[] = $rowr;
}
*/


$con = dbc_cdr();

$csv_output .= "Project ID".", ";
$csv_output .= "Project Name".", ";
$csv_output .= "Project PIF".", ";
if (IS_MINUTE) {
	$csv_output .= "Monthly Billsec in minutes".", ";
} else {
	$csv_output .= "Monthly Billsec in seconds".", ";
}
$csv_output .= "\n";


$all_projects_total_sec = 0;

//print "SELECT billsec, accountcode FROM cdr WHERE calldate > '".$year_month."' AND calldate < '".$next_year_month."'"."<br/>";
$values = mysql_query("SELECT billsec, accountcode FROM cdr WHERE calldate > '".$year_month."' AND calldate < '".$next_year_month."' AND accountcode!=''") OR die("error executing cdr SQL");

while ($rowr = mysql_fetch_array($values)) {
	//echo $rowr['accountcode']."<br/>";
	$pieces = explode("<%>", $rowr['accountcode']);
	$rowr['projectid'] = $pieces[0];
	$rowr['phonenumber'] = $pieces[1];
	$cdr_records[] = $rowr;
	$projectids[] = $rowr['projectid'];
}

$unique_project_ids = array_unique($projectids);

$con = dbc_ivr();

foreach ($unique_project_ids as $unique_project_id) {
	$total_sec = 0;
	$values = mysql_query("SELECT name, pif_number FROM dean_poll_projects WHERE id=".$unique_project_id) OR die("error executing project SQL");
	$rowr = mysql_fetch_array($values);
	foreach ($cdr_records as $cdr_record) {
		
		if ($cdr_record['projectid'] == $unique_project_id) {
			
			$single_billsec = ceil($cdr_record['billsec'] / 30) * 30;
			$total_sec += $single_billsec;
			
		}
		
	}
	if (IS_MINUTE) {
		$total_sec = ceil($total_sec / 60);
	}
	$csv_output .= $unique_project_id.", ";
	$csv_output .= $rowr['name'].", ";
	$csv_output .= $rowr['pif_number'].", ";
	$csv_output .= $total_sec.", ";
	$csv_output .= "\n";
	$all_projects_total_sec += $total_sec;
}


/*
$number_of_projects = 0;
foreach ($projects as $project) {
	$values = mysql_query("SELECT count(*) FROM IVR_data.dialout_numbers D, asteriskcdrdb.cdr C WHERE D.projectid=".$project[0]." AND C.calldate > '".$year_month."' AND C.calldate < '".$next_year_month."' AND C.accountcode LIKE '".$project[0]."<\%>%'") OR die("error executing SQL");
	$rowr = mysql_fetch_row($values);
	if ($rowr[0] > 0) {
		$number_of_projects++;
		$total_sec = 0;
		$values = mysql_query("SELECT billsec FROM cdr WHERE calldate > '".$year_month."' AND calldate < '".$next_year_month."' AND accountcode LIKE '".$project[0]."<\%>%'") OR die("error executing SQL");
		while ($rowr = mysql_fetch_row($values)) {
			$single_billsec = ceil($rowr[0] / 30) * 30;
			$total_sec += $single_billsec;
		}
		if (IS_MINUTE) {
			$total_sec = ceil($total_sec / 60);
		}
		$csv_output .= $project[0].", ";
		$csv_output .= $project[1].", ";
		$csv_output .= $project[2].", ";
		$csv_output .= $total_sec.", ";
		$csv_output .= "\n";
		$all_projects_total_sec += $total_sec;
	}
}
*/

$number_of_projects = count($unique_project_ids);

$csv_output .= "Number of Projects this month".", ";
$csv_output .= $number_of_projects.", ";
$csv_output .= "\n";

if (IS_MINUTE) {
	$csv_output .= "Total Billsec this month in minutes".", ";
	$csv_output .= $all_projects_total_sec.", ";
	$csv_output .= "\n";
} else {
	$csv_output .= "Total Billsec this month in seconds".", ";
	$csv_output .= $all_projects_total_sec.", ";
	$csv_output .= "\n";
}


mysql_close($con);

$time = mktime(0, 0, 0, $month);
$name = strftime("%B", $time);
$filename = "Monthly_IVR_Billsec_".$year."_".$name;



header("Content-type: application/vnd.ms-excel");
header("Content-disposition: filename=".$filename.".csv");
print $csv_output;
exit;

    

?>