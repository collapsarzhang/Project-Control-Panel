<?php
require "defines.inc.php";
$con = dbc_ivr();
$q = "SELECT id, name FROM dean_poll_projects WHERE active=1";
$r = mysql_query ($q);
$num_active_project = mysql_num_rows($r);
while ($row = mysql_fetch_assoc($r)) {
	$active_projects[] = $row;
}

?>

<p><?php echo $num_active_project; ?> Active Projects:</p>
<p>[number in brackets = # of active phones]</p>
<?php
if ($num_active_project > 0) {
	foreach ($active_projects as $item) {
		$q = "SELECT id FROM dialout_numbers WHERE projectid=".$item['id']." AND active=1";
		$r = mysql_query ($q);
		$num_active_phones = mysql_num_rows($r);
	?>
	<p><a href="index.php?page=edit_project&id=<?php echo $item['id']; ?>" class="ico edit">Project Panel</a>&nbsp;&nbsp;&nbsp;<?php echo $item['name']." (".$num_active_phones.")"; ?></p>
	<?php
	}
} // End of WHILE loop.

mysql_close($con);
?>