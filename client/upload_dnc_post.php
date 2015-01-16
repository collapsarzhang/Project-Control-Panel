<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
$allowedExts = array("csv");
$temp = explode(".", $_FILES["dncfile"]["name"]);
$extension = end($temp);
$dnc_numbers = "";

if (isset($_POST['incremental']) && $_POST['incremental']) {
	if (($handle = fopen("dnc/DNC.csv", "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$dnc_numbers .= formatPhone($data[0]).", "."\n";
		}
		fclose($handle);
	}
}

if (in_array($extension, $allowedExts)) {
	if ($_FILES["dncfile"]["error"] > 0) {
		$result = "Return Code: " . $_FILES["dncfile"]["error"];
  } else {
	if (($handle = fopen($_FILES['dncfile']['tmp_name'], "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$dnc_numbers .= formatPhone($data[0]).", "."\n";
		}
		$file = 'dnc/DNC.csv';
		file_put_contents($file, $dnc_numbers);
	}
	$result = "Successfully Uploaded DNC list";
  }
} else {
  $result = "Your chosen file is invalid";
}

$message = '<div class="msg msg-ok"><p><strong>'.$result.'</strong></p></div>';

safe_redirect("?page=upload_dnc&message=$message");
?>