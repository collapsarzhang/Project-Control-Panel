<?php
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>
<script>
	function submitForm(action,directpage,projectinfo,projectid)
	{
		$('#projectform').attr('action',action);
		$('#directpage').val(directpage);
		$('#projectinfo').val(projectinfo);
		$('#projectid').val(projectid);
		$('#projectform').submit();
	}
</script>
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$id = $_GET['id'];

//------------------ for project stats --------------------
$con = dbc_ivr();
$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
$r = mysql_query($q);
$row = mysql_fetch_assoc($r);
$project_name = $row['name'];

//$id = 666; // for testing

$active_area_code = array();
$q = "SELECT DISTINCT(SUBSTRING(phonenumber,1,3)) FROM dialout_numbers WHERE attempts=0 AND (result is NULL OR result='') AND prov!='TEST' AND projectid=".$id;
$r = mysql_query($q);
while ($row = mysql_fetch_array($r)) {
	$active_area_code[] = $row[0];
}
//print_r($active_area_code);

$active_timezone = array();
$q = "SELECT DISTINCT(timezone) FROM dialout_numbers WHERE attempts=0 AND (result is NULL OR result='') AND prov!='TEST' AND projectid=".$id;
$r = mysql_query($q);
while ($row = mysql_fetch_array($r)) {
	$active_timezone[] = $row[0];
}

$removed_area_code = array();
$q = "SELECT DISTINCT(SUBSTRING(phonenumber,1,3)) FROM dialout_numbers WHERE attempts=0 AND result='removed' AND prov!='TEST' AND projectid=".$id;
$r = mysql_query($q);
while ($row = mysql_fetch_array($r)) {
	$removed_area_code[] = $row[0];
}

$removed_timezone = array();
$q = "SELECT DISTINCT(timezone) FROM dialout_numbers WHERE attempts=0 AND result='removed' AND prov!='TEST' AND projectid=".$id;
$r = mysql_query($q);
while ($row = mysql_fetch_array($r)) {
	$removed_timezone[] = $row[0];
}
?>

<!-- Container -->
<div id="container">
	<div class="shell">
		<div class="msg msg-ok">
			<p><strong>Phone Number Analysis for <?php echo $project_name; ?> [Please note that you can only remove or add back numbers that have not been sent out yet!]</strong></p>
		</div>
		
		<!-- Main -->
		<div id="main">
			<div class="cl">&nbsp;</div>
			
			<!-- Content -->
			<div id="content">
				
				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Removing Unwanted Numbers</h2>
					</div>
					<!-- End Box Head -->	
					
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<?php
							$loop_count = 0;
							foreach ($active_area_code as $item) {
								$q = "SELECT COUNT(*) FROM dialout_numbers WHERE attempts=0 AND (result is NULL OR result='') AND prov!='TEST' AND SUBSTRING(phonenumber,1,3)=".$item." AND projectid=".$id;
								$r = mysql_query($q);
								$row = mysql_fetch_array($r);
								if  ($loop_count == 0) echo '<tr>';
							?>
								<td align="left" style="width:12%">Areacode <?php echo $item; ?></td>
								<td align="left" style="width:12%">Count = <?php echo $row[0]; ?></td>
								<td align="left" style="width:13%"><a href="javascript:void(0)" onclick="submitForm('areacode_download_active.php','areacode_download_active',<?php echo $item; ?>,<?php echo $id; ?>)">Download List</a></td>
								<td align="left" style="width:13%"><a href="javascript:void(0)" onclick="submitForm('index.php','areacode_remove',<?php echo $item; ?>,<?php echo $id; ?>)">Remove</a></td>
								
							<?php
								$loop_count++;
								if  ($loop_count == 2) {
									$loop_count = 0;
									echo '</tr>';
								}
							}
							if  ($loop_count == 1) echo '<td></td><td></td><tr>';
							?>

							<?php
							$loop_count = 0;
							foreach ($active_timezone as $item) {
								$q = "SELECT COUNT(*) FROM dialout_numbers WHERE attempts=0 AND (result is NULL OR result='') AND prov!='TEST' AND timezone=".$item." AND projectid=".$id;
								$r = mysql_query($q);
								$row = mysql_fetch_array($r);
								if  ($loop_count == 0) echo '<tr>';
							?>
								<td align="left">Timezone <?php echo $item; ?></td>
								<td align="left">Count = <?php echo $row[0]; ?></td>
								<td align="left"><a href="javascript:void(0)" onclick="submitForm('timezone_download_active.php','timezone_download_active',<?php echo $item; ?>,<?php echo $id; ?>)">Download List</a></td>
								<td align="left"><a href="javascript:void(0)" onclick="submitForm('index.php','timezone_remove',<?php echo $item; ?>,<?php echo $id; ?>)">Remove</a></td>
								
							<?php
								$loop_count++;
								if  ($loop_count == 2) {
									$loop_count = 0;
									echo '</tr>';
								}
							}
							if  ($loop_count == 1) echo '<td></td><td></td><tr>';
							?>
						</table>
						
					</div>
					<!-- Table -->
					
				</div>
				<!-- End Box -->

				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Add Back Wanted Numbers</h2>
					</div>
					<!-- End Box Head -->	
					
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<?php
							$loop_count = 0;
							foreach ($removed_area_code as $item) {
								$q = "SELECT COUNT(*) FROM dialout_numbers WHERE attempts=0 AND result='removed' AND prov!='TEST' AND SUBSTRING(phonenumber,1,3)=".$item." AND projectid=".$id;
								$r = mysql_query($q);
								$row = mysql_fetch_array($r);
								if  ($loop_count == 0) echo '<tr>';
							?>
								<td align="left" style="width:12%">Areacode <?php echo $item; ?></td>
								<td align="left" style="width:12%">Count = <?php echo $row[0]; ?></td>
								<td align="left" style="width:13%"><a href="javascript:void(0)" onclick="submitForm('areacode_download_removed.php','areacode_download_removed',<?php echo $item; ?>,<?php echo $id; ?>)">Download List</a></td>
								<td align="left" style="width:13%"><a href="javascript:void(0)" onclick="submitForm('index.php','areacode_addback',<?php echo $item; ?>,<?php echo $id; ?>)">Add Back</a></td>
								
							<?php
								$loop_count++;
								if  ($loop_count == 2) {
									$loop_count = 0;
									echo '</tr>';
								}
							}
							if  ($loop_count == 1) echo '<td></td><td></td><tr>';
							?>

							<?php
							$loop_count = 0;
							foreach ($removed_timezone as $item) {
								$q = "SELECT COUNT(*) FROM dialout_numbers WHERE attempts=0 AND result='removed' AND prov!='TEST' AND timezone=".$item." AND projectid=".$id;
								$r = mysql_query($q);
								$row = mysql_fetch_array($r);
								if  ($loop_count == 0) echo '<tr>';
							?>
								<td align="left">Timezone <?php echo $item; ?></td>
								<td align="left">Count = <?php echo $row[0]; ?></td>
								<td align="left"><a href="javascript:void(0)" onclick="submitForm('timezone_download_removed.php','timezone_download_removed',<?php echo $item; ?>,<?php echo $id; ?>)">Download List</a></td>
								<td align="left"><a href="javascript:void(0)" onclick="submitForm('index.php','timezone_addback',<?php echo $item; ?>,<?php echo $id; ?>)">Add Back</a></td>
								
							<?php
								$loop_count++;
								if  ($loop_count == 2) {
									$loop_count = 0;
									echo '</tr>';
								}
							}
							if  ($loop_count == 1) echo '<td></td><td></td><tr>';
							?>
						</table>
						
					</div>
					<!-- Table -->
					
				</div>
				<!-- End Box -->

			</div>
			<!-- End Content -->
			
			<!-- Sidebar -->
			<div id="sidebar">
				

				<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>Project Actions</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						<div><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=edit_project&id=<?php echo $id; ?>" class="add-button"><span>Return to Project Panel</span></a></div>
						<div class="cl">&nbsp;</div>					
					</div>
					<form method="POST" id="projectform" action="">
						<input type="hidden" id="directpage" name="page" value="">
						<input type="hidden" id="projectid" name="id" value="">
						<input type="hidden" id="projectinfo" name="action" value="">
					<form>
				</div>
				<!-- End Box -->

			</div>
			<!-- End Sidebar -->
			<div class="cl">&nbsp;</div>			
		</div>
		<!-- Main -->
	</div>
</div>
<!-- End Container -->