<?php
session_start();
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
require_once "includes/defines.inc.php";
$id = $_GET['projectid'];
$project_type = $_GET['projecttype'];

//it is called invoice_type in the getInvoice function
$invoice_name = getInvoice($id, $project_type);


echo "<html>\n";
echo "<head>\n";
echo "<SCRIPT LANGUAGE=\"JavaScript\"><!--\n";
echo "  function go_now () { window.location.href = '".$invoice_name."'; }\n";
echo "//--></SCRIPT>";
echo "</head>\n";
echo "<body onLoad=\"go_now()\"; >\n";
echo "<a href=\"".$invoice_name."\">click here</a> if you are not re-directed.\n";
echo "</body>\n";


exit();




?>

