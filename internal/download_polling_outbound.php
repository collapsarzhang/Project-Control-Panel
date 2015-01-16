<?
ini_set('max_execution_time','600000');
ini_set('memory_limit','16M');
include_once("includes/defines.inc.php");


$con = dbc_ivr();

$project_id = $_GET['projectid'];
$dummy_id_start = $project_id*100+60;
$firstname_question_id = $project_id*100+95;
$result = mysql_query("SELECT question_id FROM dean_poll_questions WHERE project_id=".$project_id." AND (question_id<".$dummy_id_start.") ORDER BY question_id ASC");
//$query = "SELECT general.timestamp, general.user_id";
$i=0;
while ($row = mysql_fetch_assoc($result)) {
		$current = "current".$i;
		$last = "current".($i-1);
		$temp = "temp".$current;

		if ($i==0) {
			$query = "CREATE TEMPORARY TABLE $current AS (SELECT timestamp, user_id, response AS Q_".$row['question_id']." FROM dean_poll_responses WHERE user_id!='' AND user_id NOT IN (SELECT phonenumber FROM dialout_numbers WHERE prov='TEST' AND projectid=".$project_id.") AND question_id=".$row['question_id'].")";
			//$query_all .= $query;
			$r = mysql_query($query);
			//echo $query;
			//echo "</br>";
		} else {
			$query = "CREATE TEMPORARY TABLE $temp AS (SELECT user_id, question_id, response FROM dean_poll_responses WHERE user_id!='' AND question_id=".$row['question_id'].")";
			//$query_all .= $query;
			$r = mysql_query($query);
			
			//echo $query;
			//echo "</br>";

			$query = "CREATE TEMPORARY TABLE $current AS (SELECT $last.*, $temp.response AS Q_".$row['question_id']." FROM $last LEFT JOIN $temp ON $last.user_id=$temp.user_id)";
			//$query_all .= $query;
			$r = mysql_query($query);
			$r = mysql_query("DROP TABLE $last");
			$r = mysql_query("DROP TABLE $temp");
			//echo $query;
			//echo "</br>";
			
		}
		$i++;
}

$result = mysql_query("SELECT COUNT(*) FROM dean_poll_responses WHERE project_id=".$project_id." AND question_id=".$firstname_question_id);
$row = mysql_fetch_array($result);
if ($row[0]>0) {
	$current = "current".$i;
	$last = "current".($i-1);
	$temp = "temp".$current;
	$query = "CREATE TEMPORARY TABLE $temp AS (SELECT user_id, question_id, response FROM dean_poll_responses WHERE user_id!='' AND question_id=".$firstname_question_id.")";
	$r = mysql_query($query);

	$query = "CREATE TEMPORARY TABLE $current AS (SELECT $last.*, $temp.response AS Firstname FROM $last LEFT JOIN $temp ON $last.user_id=$temp.user_id)";
	$r = mysql_query($query);
	$r = mysql_query("DROP TABLE $last");
	$r = mysql_query("DROP TABLE $temp");
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


/*
$result = mysql_query("SHOW COLUMNS FROM $current");
$i = 0;
$csv_output = "";

  if (mysql_num_rows($result) > 0)
    {
      while ($row = mysql_fetch_assoc($result))
        {
          $csv_output .= $row['Field'].", ";
          $i++;
        }
    }
    
    $csv_output .= "\n";
    $values = mysql_query("SELECT * FROM $current ORDER BY timestamp");
    while ($rowr = mysql_fetch_row($values))
      {
	for ($j=0;$j<$i;$j++)
	  {

	    $csv_output .= $rowr[$j].", ";
	  }
	$csv_output .= "\n";
      }
    mysql_close($con);
    
    header("Content-type: application/vnd.ms-excel");
    header("Content-disposition: csv" . date("Y-m-d") . ".csv");
    header( "Content-disposition: filename=".$filename.".csv");

    print $csv_output;
    exit;

function query_to_csv($db_conn, $query, $filename, $attachment = false, $headers = true) {
	
	if($attachment) {
		// send response headers to the browser
		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment;filename='.$filename);
		$fp = fopen('php://output', 'w');
	} else {
		$fp = fopen($filename, 'w');
	}
	
	$result = mysql_query($query, $db_conn) or die( mysql_error( $db_conn ) );
	
	if($headers) {
		// output header row (if at least one row exists)
		$row = mysql_fetch_assoc($result);
		if($row) {
			fputcsv($fp, array_keys($row));
			// reset pointer back to beginning
			mysql_data_seek($result, 0);
		}
	}
	
	while($row = mysql_fetch_assoc($result)) {
		fputcsv($fp, $row);
	}
	
	fclose($fp);
}
*/
?>