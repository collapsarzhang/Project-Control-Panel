<?php
session_start();
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$id = $_GET['projectid'];
require ("pdf_report.php"); 




echo "<html>\n";
echo "<head>\n";
echo "<SCRIPT LANGUAGE=\"JavaScript\"><!--\n";
echo "  function go_now () { window.location.href = '".$fname."'; }\n";
echo "//--></SCRIPT>";
echo "</head>\n";
echo "<body onLoad=\"go_now()\"; >\n";
echo "<a href=\"".$fname."\">click here</a> if you are not re-directed.\n";
echo "</body>\n";


exit();




?>

