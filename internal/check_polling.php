<?php
session_start();
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
require "includes/defines.inc.php";
$con = dbc_ivr();

$r = mysql_query("SELECT * FROM dean_poll_questions WHERE project_id=".$_GET['id']." ORDER BY question_id ASC") OR die("error excuting query");

echo "<table border='1'>";
echo "<tr>";
echo "<td>Question ID</td>";
echo "<td>Question Type</td>";
echo "<td>Valid Options</td>";
echo "<td>Destination Question</td>";
echo "<td>Is the first question</td>";
echo "</tr>";
while ($row = mysql_fetch_array($r)) {
	$first = $row["first"]==1?'yes':'no';
	echo "<tr>";
	echo "<td>".$row["question_id"]."</td>";
	echo "<td>".$row["type"]."</td>";
	echo "<td>".$row["data"]."</td>";
	echo "<td>".$row["next"]."</td>";
	echo "<td>".$first."</td>";
	echo "</tr>";
}
echo "</table>";
mysql_close($con);








?>