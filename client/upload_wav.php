<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
$allowedExts = array("wav");
$folder = $_POST['id'];
$id = $_POST['id'];
$name1 = $folder."00.wav";
$name2 = $folder."99.wav";
$ending_name = $folder."98.wav";

$con = dbc_ivr();
$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
$r = mysql_query ($q);
$row = mysql_fetch_assoc($r);
if ($row['project_type'] != "fndp") {
	safe_redirect("index.php");
}

if (isset($_FILES["human_wav"])) {
	$temp = explode(".", $_FILES["human_wav"]["name"]);
	$extension = end($temp);
	if (in_array($extension, $allowedExts))
	{
		$file = $_FILES["human_wav"]["tmp_name"];
		$output = shell_exec('file '.$file);
		if (strpos($output, 'WAVE audio, Microsoft PCM, 16 bit, mono 8000 Hz') !== false) {
			$is_valid_wav = true;
		} else {
			$is_valid_wav = false;
		}
		if ($_FILES["human_wav"]["error"] > 0) {
		}
		else if($is_valid_wav)  {
			//echo "Upload: " . $_FILES["human_wav"]["name"] . "<br>";
			//echo "Type: " . $_FILES["human_wav"]["type"] . "<br>";
			//echo "Size: " . ($_FILES["human_wav"]["size"] / 1024) . " kB<br>";
			//echo "Temp file: " . $_FILES["human_wav"]["tmp_name"] . "<br>";
			
			mkdir($wav_file_path.$folder);

			if (file_exists($wav_file_path.$folder."/".$name1)) {
				//echo $name1 . " already exists. ";
			}

			move_uploaded_file($_FILES["human_wav"]["tmp_name"], $wav_file_path.$folder."/".$name1);
			//echo "Stored in: " .  $wav_file_path.$folder."/".$name1;

		}
	  }
	else {
		//echo "Invalid file";
	}
}
else {
	//echo "file not exist";
}


if (isset($_FILES["am_wav"])) {
	$temp = explode(".", $_FILES["am_wav"]["name"]);
	$extension = end($temp);
	if (in_array($extension, $allowedExts))
	{
		$file = $_FILES["am_wav"]["tmp_name"];
		$output = shell_exec('file '.$file);
		if (strpos($output, 'WAVE audio, Microsoft PCM, 16 bit, mono 8000 Hz') !== false) {
			$is_valid_wav = true;
		} else {
			$is_valid_wav = false;
		}
		if ($_FILES["am_wav"]["error"] > 0) {
		}
		else if($is_valid_wav) {
			mkdir($wav_file_path.$folder);

			if (file_exists($wav_file_path.$folder."/".$name2)) {
				//echo $name2 . " already exists. ";
			}
			move_uploaded_file($_FILES["am_wav"]["tmp_name"], $wav_file_path.$folder."/".$name2);

		}
	  }
	else {
		//echo "Invalid file";
	}
}
else {
	//echo "file not exist";
}



if (isset($_FILES["ending_wav"])) {
	$temp = explode(".", $_FILES["ending_wav"]["name"]);
	$extension = end($temp);
	if (in_array($extension, $allowedExts))
	{
		$file = $_FILES["ending_wav"]["tmp_name"];
		$output = shell_exec('file '.$file);
		if (strpos($output, 'WAVE audio, Microsoft PCM, 16 bit, mono 8000 Hz') !== false) {
			$is_valid_wav = true;
		} else {
			$is_valid_wav = false;
		}
		if ($_FILES["ending_wav"]["error"] > 0) {
		}
		else if($is_valid_wav) {
			mkdir($wav_file_path.$folder);

			if (file_exists($wav_file_path.$folder."/".$ending_name)) {
				//echo $name2 . " already exists. ";
			}
			move_uploaded_file($_FILES["ending_wav"]["tmp_name"], $wav_file_path.$folder."/".$ending_name);

		}
	  }
	else {
		//echo "Invalid file";
	}
}
else {
	//echo "file not exist";
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
