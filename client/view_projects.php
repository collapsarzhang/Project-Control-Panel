<script type="text/javascript">
	var auto_refresh = setInterval(
		function ()
		{
			$('#load_tweets').load('includes/record_count.php').fadeIn("slow");
			$('#load_active_project').load('includes/active_project_status.php').fadeIn("slow");
		}, 2000); // refresh every 10000 milliseconds
</script>
<script>
	function submitForm(projectid, action)
	{
		$('#projectid').val(projectid);
		$('#action').val(action);
		$('#projectform').submit();
	}
</script>

<script type="text/javascript" src="js/jquery-latest.js"></script> 
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
<script>
	$(function() 
		{ 
			$("#myTable").tablesorter(); 
		} 
	);
</script>
<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

$con = dbc_ivr();

// Number of records to show per page:
$display = 16;
// Determine how many pages there are...
if (isset($_GET['pages']) && is_numeric($_GET['pages'])) { // Already been determined.
	$pages = $_GET['pages'];
} else { // Need to determine.
	// Count the number of records:
	$q = "SELECT COUNT(id) FROM dean_poll_projects WHERE project_type='fndp'";
	$r = mysql_query($q);
	$row = mysql_fetch_assoc($r);
	if ($row['COUNT(id)'] > 100) {
		$records = 100;
	} else {
		$records = $row['COUNT(id)'];
	}
	
	// Calculate the number of pages...
	if ($records > $display) { // More than 1 page.
		$pages = ceil ($records/$display);
	} else {
		$pages = 1;
	}
} // End of p IF.

// Determine where in the database to start returning results...
if (isset($_GET['start_record']) && is_numeric($_GET['start_record'])) {
	$start_record = $_GET['start_record'];
} else {
	$start_record = 0;
}


$current_page = ($start_record/$display) + 1;
// Define the query:
$q = "SELECT * FROM dean_poll_projects WHERE project_type='fndp' ORDER BY id DESC LIMIT $start_record, $display";		
$r = mysql_query ($q); // Run the query.
while ($row = mysql_fetch_assoc($r)) {
	if ($row['active']==1 AND $row['project_state']=='initial') $row['active']='Testing';
	else if ($row['active']==0 AND $row['project_state']=='initial') $row['active']='Setting Up';
	else if ($row['active']==0 AND $row['project_state']=='cancelled') $row['active']='Cancelled';
	else if ($row['active']==1 AND $row['project_state']=='approved' AND $row['fndp_active']==0) $row['active']='Stratcom Testing';
	else if ($row['active']==1 AND $row['project_state']=='approved' AND $row['fndp_active']==1) $row['active']='Dialing';
	else if ($row['active']==0 AND $row['project_state']=='approved') $row['active']='Approved';
	else if ($row['project_state']=='ready') $row['active']='Waiting for Approval';
	else if ($row['project_state']=='complete') $row['active']='Finished';
	else $row['active']='N/A';
	$all_projects[] = $row;
}
mysql_close($con);
?>

<!-- Container -->
<div id="container">
	<div class="shell">
		
		<!-- Main -->
		<div id="main">
			<div class="cl">&nbsp;</div>
			
			<!-- Content -->
			<div id="content">
				
				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">All Projects</h2>
						<div class="right">
							
							<form name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
							<input type="hidden" name="page" value="search_result">
							<input type="text" class="field small-field" name="search_term" />
							<input type="submit" class="button" value="search" />
							</form>
						</div>
					</div>
					<!-- End Box Head -->	

					<!-- Table -->
					<div class="table">
						<table id="myTable" class="tablesorter" width="100%" border="0" cellspacing="0" cellpadding="0">
							<thead>
							<tr>
								<th align="left" style="width:30%">Name</th>
								<th align="left" style="width:10%">Date</th>
								<th align="left" style="width:5%">Rounds</th>
								<th align="left" style="width:10%">PIF</th>
								<th align="left" style="width:10%">Status</th>
								<th align="left" style="width:10%">Edit Porject</th>
								<th align="left" style="width:10%">Clone Porject</th>
							</tr> 
							</thead>
							<tbody>
							<?php
							$con = dbc_ivr();
							foreach ($all_projects as $item) {
								$q = "SELECT MAX(attempts) FROM dialout_numbers WHERE projectid=".$item['id']." AND prov!='TEST'";		
								$r = mysql_query ($q); // Run the query.
								$row = mysql_fetch_assoc($r)
							?>
							<tr>
								<td align="left"><?php echo $item['name']; ?></td>
								<td align="left"><?php echo date('Y/m/d', strtotime($item['project_date'])); ?></td>
								<td align="left"><?php echo $row['MAX(attempts)']; ?></td>
								<td align="left"><?php echo $item['pif_number']; ?></td>
								<td align="left"><?php echo $item['active']; ?></td>
								<td><a href="javascript:void(0)" onclick="submitForm(<?php echo $item['id']; ?>, 'edit_project')">Project Panel</a></td>
								<td><a href="javascript:void(0)" onclick="submitForm(<?php echo $item['id']; ?>, 'clone_project')">Clone This</a></td>
							</tr>
							<?php
							} // End of WHILE loop.
							mysql_close($con);
							?>
							</tbody>
							<form method="POST" id="projectform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
								<input type="hidden" id="action" name="page" value="">
								<input type="hidden" id="projectid" name="id" value="">
							<form>
						</table>

						<!-- Pagging -->
						<div class="pagging">
							<div class="left">Page <?php echo $current_page;?> of <?php echo $pages;?></div>
							<div class="right">
							<?php
							// Set Previous Page Link

							if ($current_page != 1) {
								$next_start = $start_record-$display;
							?>
								<a href="<?php echo $_SERVER['PHP_SELF'];?>?start_record=<?php echo $next_start;?>&pages=<?php echo $pages;?>&page=home">Previous</a>
							
							<?php
							// Set First Page Link

								$next_start = 0;
							?>
								<a href="<?php echo $_SERVER['PHP_SELF'];?>?start_record=<?php echo $next_start;?>&pages=<?php echo $pages;?>&page=home">1</a>
							<?php
							}
							?>
							
							<?php
							// Set Previous 5 Pages Link
							if ($current_page > 1) {
								for ($i=5;$i>=1;$i--) {
									$next_page = $current_page-$i;
									if ($next_page > 1) {
										$next_start = $display*($next_page-1);
							?>
										<a href="<?php echo $_SERVER['PHP_SELF'];?>?start_record=<?php echo $next_start;?>&pages=<?php echo $pages;?>&page=home"><?php echo $next_page;?></a>
							<?php
									}
								}
							}
							?>

							<?php
							// Set Current Page Link
							?>
							<a><?php echo $current_page;?></a>


							<?php
							// Set Next 5 Pages Link
							if ($current_page < $pages) {
								for ($i=1;$i<=5;$i++) {
									$next_page = $current_page+$i;
									if ($next_page < $pages) {
										$next_start = $display*($next_page-1);
							?>
										<a href="<?php echo $_SERVER['PHP_SELF'];?>?start_record=<?php echo $next_start;?>&pages=<?php echo $pages;?>&page=home"><?php echo $next_page;?></a>
							<?php
									}
								}
							}
							?>

							<?php
							// Set Last Page Link

							if ($current_page != $pages) {
								$next_start = $display*($pages-1);
							?>
								<a href="<?php echo $_SERVER['PHP_SELF'];?>?start_record=<?php echo $next_start;?>&pages=<?php echo $pages;?>&page=home"><?php echo $pages;?></a>
							
							<?php
							// Set Next Page Link
								$next_start = $start_record+$display;
							?>
								<a href="<?php echo $_SERVER['PHP_SELF'];?>?start_record=<?php echo $next_start;?>&pages=<?php echo $pages;?>&page=home">Next</a>
							<?php
							}
							?>
								
							</div>
						</div>
						<!-- End Pagging -->
						
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
						<h2>Project Manager</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						<div><a href="<?php echo $_SERVER['PHP_SELF'];?>?page=add_project" class="add-button"><span>Add new Project</span></a></div>
						<div class="cl">&nbsp;</div>

						
						
						
					</div>
				</div>
				<!-- End Box -->

				<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>Current Configuration</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						<?php
						$con = dbc_ivr();
						$q = "SELECT value FROM config WHERE id=3";		
						$r = mysql_query ($q); // Run the query.
						$calls_per_second = mysql_fetch_assoc($r);
						$q = "SELECT value FROM config WHERE id=14";		
						$r = mysql_query ($q); // Run the query.
						$secondary_channel = mysql_fetch_assoc($r);
						$q = "SELECT value FROM config WHERE id=15";		
						$r = mysql_query ($q); // Run the query.
						$channel_percentage = mysql_fetch_assoc($r);
						mysql_close($con);

						preg_match('|/(.*?)/|s', $secondary_channel['value'], $matches);
						$secondary_channel_value = $matches[1];

						preg_match('|/(.*?)/|s', $default_dialout_channel, $matches);
						$primary_channel_value = $matches[1];
						?>
						
						<p>Calls Per Sec: <?php echo $calls_per_second['value']; ?></p>
						<div class="cl">&nbsp;</div>
					</div>
				</div>
				<!-- End Box -->

				<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>Real Time Status</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						
						<div id="load_tweets"></div>
						<div id="load_active_project"></div>
						<div class="cl">&nbsp;</div>
					</div>
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