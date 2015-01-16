<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$con = dbc_ivr();

$id = $_POST['id'];
$number_column = 1;
$province_column = 2;
$temp_column = 3;
$active = false;
if (date("I")) {
	$time_zone = "Summer";
} else {
	$time_zone = "Winter";
}

//obtain DNC list
unset($dnc_numbers);
unset($kill_numbers);
$kill_numbers = 0;
if (($handle = fopen("dnc/DNC.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$dnc_numbers[]=formatPhone($data[0]);
	}
	fclose($handle);
}




//fetch timezone lookup table from db and store in array
if (!$result = mysql_query("SELECT phonenumber FROM dnc_list")){ // DB1
	echo '<h4 class="alert_error">DB1 Error</h4>';
}
while($row = mysql_fetch_assoc($result)){           
	$dnc_numbers[]=$row['phonenumber'];
}
// close a connection
mysql_close($con);

$raw_data=array('num'=>array(), 'temp'=>array(),'province'=>array());
unset($raw_data);
unset($isdone);
unset($gonext);

$col_num =  $number_column;
$col_province =  $province_column;  
$col_temp =  $temp_column;   
$g_projectid = $id;

if (!isset($_FILES['userfile']) OR ($_FILES['userfile']['size'] == 0)){
}
else{
	$fileName=$_FILES['userfile']['name'];
	$tmpName =$_FILES['userfile']['tmp_name'];
	$fileSize=$_FILES['userfile']['size'];
	$fileType=$_FILES['userfile']['type'];
	if (($handle = fopen($tmpName, 'r'))!==FALSE){ // IF 1
		$content = fread($handle,fileSize ($tmpName));
		$raw_data_by_row = explode("\r",$content); 
		$raw_data_count = count($raw_data_by_row);
		$check_point = 50;
		if ($raw_data_count <$check_point){
			$check_point=$raw_data_count;
		}
		if ($raw_data_by_row==FALSE){
			echo '<h4 class="alert_error">Error in extract each row from data file</h4>';
		}
		unset($skipdone);
		$stop_at_raw=0;
		$first_n_loop=0;

		foreach ($raw_data_by_row as $row_str){
			if(trim($row_str)!=NULL){
				$row =explode(",",$row_str);
				if ($row_col_num = formatPhone($row[$col_num-1])) {
					if (!in_array($row_col_num, $dnc_numbers)) {
						$raw_data['num'][]=$row_col_num;
						$raw_data['province'][]="";
						$raw_data['temp'][]="";
						$test_rows = my_trim($row_col_num);
						$first_n_loop++;
						// validate the first 50 data or whichever less
						if ($test_rows[0]==FALSE && ($first_n_loop<$check_point)){
							//echo $test_rows[1]."<br />";
							$stop_at_raw++;
						}
					} else {
						$kill_numbers++;
					}
				}
			}
		}
	}// IF 1 END

	$isdone = 1;
	fclose($handle);

	if ($stop_at_raw>($check_point/4)){
		$gonext=0; 
	}
	else{
		$gonext=1;
	}
}

if ($isdone==1 && $gonext==0){
	echo '<h4 class="alert_error">Please check the data or make sure that the right columns numbers were chosen</h4>';
}
if ($isdone==1 && $gonext ==1){
	//---------fetch time zone lookup table from database-
	$con = dbc_ivr();
	//fetch timezone lookup table from db and store in array
	if (!$result = mysql_query("SELECT * FROM TimeZones")){ // DB1
		echo '<h4 class="alert_error">DB1 Error</h4>';
	}
	$utc_table['areacode']=array();
	$utc_table['offset']=array();
	while($row = mysql_fetch_assoc($result)){           
		$utc_table['areacode'][]=$row['AreaCode'];
		if($time_zone == 'Winter'){
			$utc_table['offset'][]  =$row['UTCWinter'];
		}
		else{
			$utc_table['offset'][]  =$row['UTCSummer'];
		}
	}
	// close a connection
	mysql_close($con);

	$validnum=array('num'=> array(), 'offset'=> array(), 'projectid'=>array(), 'province'=>array(), 'temp'=>array());
	$invalidnum=array('num_o'=>array(), 'num_t'=>array(), 'comments'=>array());

	//***********trim phone number and return false if does not matach NANP*********
	$fg_1 = 0;
	foreach ($raw_data['num'] as $raw_num){
		//$raw_data['num'] = trim ($raw_num); //trim white space
		$trimed_num = my_trim($raw_num);

		if ($trimed_num[0]==FALSE){
			$invalidnum['num_o'][]=$raw_num;
			$invalidnum['num_t'][]=$trimed_num[1]; 
			$invalidnum['comments'][]="Failed in NANP verification";
		}
		else{
			$areacode = substr($trimed_num[1],0,3);
			unset($index);
			$index = array_search($areacode, $utc_table['areacode']);
			if ($index != FALSE){
				//echo $index."<br />";
				$validnum['num'][]=$trimed_num[1];
				$validnum['offset'][]=$utc_table['offset'][$index];
				//echo $utc_table['offset'][$index];
				//$validnum['projectid'][]=$raw_data['projectid'][$fg_1];
				$validnum['province'][]=$raw_data['province'][$fg_1];
				$validnum['temp'][]=$raw_data['temp'][$fg_1];
			}
			else{								
				$invalidnum['num_t'][]=$trimed_num[1];
				$invalidnum['num_o'][]=$raw_num;
				$invalidnum['comments'][]="Area Code is not found in Timezone table";
			}
		}

		$fg_1++;
	}

	//************remove duplicated valid phone numbers*********
	// var_dump ($validnum);
	$validnum_clean = rm_dup($validnum,'valid');
	$num_dups_valid = count($validnum['num']) - count($validnum_clean['num']);

	//************remove duplicated invalid phone numbers*********
	$invalidnum_clean = rm_dup($invalidnum,'invalid');
	// echo $num_dups_invalid = count($invalidnum['num_t']);
	// echo count( $invalidnum_clean['num_t']);
	$num_dups_invalid = count($invalidnum['num_t']) - count( $invalidnum_clean['num_t']);
	$num_dups = $num_dups_valid + $num_dups_invalid;



	// echo count($validnum['num'])." valid phone numbers were imported and ".count($invalidnum['num'])." invalid phone numbers were found.<br />";
	// var_dump ($invalidnum);

	//**************************insert into database*********************
	$con = dbc_ivr();

	if ($active){
		$num_isactive=1;
	}
	else{
		$num_isactive=0;
	}

	//---------inserting valid numbers into database------------------------------
	$inserted_valid=0;
	$updated_valid=0;
	for ($i=0;$i<count($validnum_clean['num']);$i++){
		//$valid_projectid = $validnum_clean['projectid'][$i];
		$valid_num = $validnum_clean['num'][$i];
		$valid_offset = $validnum_clean['offset'][$i];
		$valid_province = $validnum_clean['province'][$i];
		$valid_temp = $validnum_clean['temp'][$i];
		mysql_query("INSERT INTO dialout_numbers(projectid, phonenumber,timezone,active,prov,temp) VALUES ($g_projectid,$valid_num,$valid_offset,$num_isactive,'$valid_province','$valid_temp') ON DUPLICATE KEY UPDATE timezone=$valid_offset, active=$num_isactive, prov='$valid_province', temp='$valid_temp'");
		$feedback = mysql_affected_rows();
		if ($feedback==1){
			$inserted_valid++;
		}
		if ($feedback==2){
			$updated_valid++;
		}
	}


	//---------inserting invalid numbers into database-----------------------------
	$inserted_invalid=0;
	$updated_invalid=0;
	$invalid_result="invalid";

	for ($i=0;$i<count($invalidnum_clean['num_t']);$i++){
		$invalid_num = $invalidnum_clean['num_t'][$i];
		// echo $invalid_num."<br>"; 
		// set active=0 for invalid numbers
		mysql_query("INSERT INTO dialout_numbers(projectid, phonenumber,active,result) VALUES ($g_projectid,$invalid_num, 0 ,'$invalid_result') ON DUPLICATE KEY UPDATE active=0, result='$invalid_result'");
		$feedback = mysql_affected_rows();
		if ($feedback==1){
			$inserted_invalid++;
		}
		if ($feedback==2){
			$updated_invalid++;
		}
	}

	// close a connection
	mysql_close($con);
}

if (isset($_FILES['dncfile'])){
	if (($handle = fopen($_FILES['dncfile']['tmp_name'], "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$dnc_numbers_remove[]=formatPhone($data[0]);
		}
		fclose($handle);
		$dnc_remove_string = implode("','", $dnc_numbers_remove);
		$con = dbc_ivr();
		mysql_query("UPDATE dialout_numbers SET result='DNC' WHERE projectid=".$id." AND phonenumber IN ('".$dnc_remove_string."')");
		mysql_close($con);
	}
}



?>

<script>
	function submitForm(projectid)
	{
		$('#projectform').submit();
	}
</script>
<form method="POST" id="projectform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="page" value="edit_project">
	<input type="hidden" id="projectid" name="id" value=<?php echo $id; ?>>
<form>
<script>
	submitForm()
</script>