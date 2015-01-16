<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
$dnc_numbers = "";

if (isset($_POST['page']) && $_POST['page']=='add_dnc_post') {
	if (($handle = fopen("dnc/DNC.csv", "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$dnc_numbers .= formatPhone($data[0]).", "."\n";
		}
		fclose($handle);
	}
	$dnc_numbers .= formatPhone($_POST['dnc_number']).", "."\n";
	$message = "Successfully Added DNC Number";
}

if (isset($_POST['page']) && $_POST['page']=='remove_dnc_post') {
	if (($handle = fopen("dnc/DNC.csv", "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if (formatPhone($data[0]) != formatPhone($_POST['dnc_number'])) {
				$dnc_numbers .= formatPhone($data[0]).", "."\n";
			}
		}
		fclose($handle);
	}
	$message = "Successfully Removed DNC Number";
}

$file = 'dnc/DNC.csv';
file_put_contents($file, $dnc_numbers);

$message = '<div class="msg msg-ok"><p><strong>'.$message.'</strong></p></div>';

safe_redirect("?page=upload_dnc&message=$message");

?>