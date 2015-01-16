<?php
if (!isset($_SESSION['user_ivr'])) {
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

if (isset($_FILES["human_wav"])) {
	$temp = explode(".", $_FILES["human_wav"]["name"]);
	$extension = end($temp);
	if (in_array($extension, $allowedExts))
	{
		if ($_FILES["human_wav"]["error"] > 0) {
		}
		else {
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
		if ($_FILES["am_wav"]["error"] > 0) {
		}
		else {
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
		if ($_FILES["ending_wav"]["error"] > 0) {
		}
		else {
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

safe_redirect($_SERVER['PHP_SELF']."?page=edit_project&id=".$id);
?>

