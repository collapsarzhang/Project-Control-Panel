<?
ini_set('max_execution_time','600000');
ini_set('memory_limit','16M');
include_once("includes/defines.inc.php");


$con = dbc_ivr();

$project_id = $_GET['projectid'];
$result = mysql_query("SELECT question_id FROM dean_poll_questions where project_id=".$project_id);
//$query = "SELECT general.timestamp, general.user_id";
$i=0;
while ($row = mysql_fetch_assoc($result)) {
		$current = "current".$i;
		$last = "current".($i-1);
		$temp = "temp".$current;

		if ($i==0) {
			$query = "CREATE TEMPORARY TABLE $current AS (SELECT DISTINCT timestamp, callerid, response AS Q_".$row['question_id']." FROM dean_poll_responses WHERE user_id='' AND callerid NOT IN (SELECT phonenumber FROM dialout_numbers WHERE prov='TEST' AND projectid=".$project_id.") AND question_id=".$row['question_id'].")";
			//$query_all .= $query;
			$r = mysql_query($query);
			//echo $query;
			//echo "</br>";
		} else {
			$query = "CREATE TEMPORARY TABLE $temp AS (SELECT DISTINCT callerid, question_id, response FROM dean_poll_responses WHERE user_id='' AND question_id=".$row['question_id'].")";
			//$query_all .= $query;
			$r = mysql_query($query);
			
			//echo $query;
			//echo "</br>";

			$query = "CREATE TEMPORARY TABLE $current AS (SELECT DISTINCT $last.*, $temp.response AS Q_".$row['question_id']." FROM $last LEFT JOIN $temp ON $last.callerid=$temp.callerid)";
			//$query_all .= $query;
			$r = mysql_query($query);
			$r = mysql_query("DROP TABLE $last");
			$r = mysql_query("DROP TABLE $temp");
			//echo $query;
			//echo "</br>";
			
		}
		$i++;
}

$file='project'.$project_id;
$filename = $file."_".date("Y-m-d_H-i",time()).".csv";

header( 'Content-Type: text/csv' );
header( 'Content-Disposition: attachment;filename='.$filename);
$fp = fopen('php://output', 'w');

//$fp = fopen("reports/".$filename, 'w');

$result = mysql_query("SELECT * FROM $current ORDER BY timestamp");

// output header row (if at least one row exists)
$row = mysql_fetch_assoc($result);
if($row) {
	fputcsv($fp, array_keys($row));
	// reset pointer back to beginning
	mysql_data_seek($result, 0);
}

while($row = mysql_fetch_assoc($result)) {
	fputcsv($fp, $row);
}

fclose($fp);

?>