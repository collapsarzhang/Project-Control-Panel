<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>
<script>
	function submitForm(projectid)
	{
		$('#projectid').val(projectid);
		$('#projectform').submit();
	}
</script>
<?php
$con = dbc_ivr();

// Number of records to show per page:
$display = 30;
$search_terms = $_POST['search_term'];
$search_terms = mysql_real_escape_string($search_terms);
$search_term_array = explode(" ", $search_terms);

foreach ($search_term_array as $item) {
	$query_term .= " AND (name LIKE '%".$item."%' OR pif_number LIKE '%".$item."%')";
}
/*
foreach ($search_term_array as $item) {
	$query_term .= " AND pif_number LIKE '%".$item."%'";
}
*/

$q = "SELECT id FROM dean_poll_projects ORDER BY id DESC LIMIT 1";		
$r = mysql_query ($q); // Run the query.
$row = mysql_fetch_assoc($r);
$search_id = $row['id'] - 200;

// Define the query:
$q = "SELECT * FROM dean_poll_projects WHERE project_type='fndp' AND id > ".$search_id.$query_term." ORDER BY id DESC LIMIT ".$display;
$r = mysql_query ($q); // Run the query.
while ($row = mysql_fetch_assoc($r)) {
	if ($row['active']==1) $row['active']='Active';
	else $row['active']='Inactive';
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
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<th align="left" style="width:30%">Name</th>
								<th align="left" style="width:10%">Date</th>
								<th align="left" style="width:10%"># of Rounds</th>
								<th align="left" style="width:10%">PIF</th>
								<th align="left" style="width:10%">Status</th>
								<th align="left" style="width:15%">Edit Porject</th>
							</tr>
							<?php
							$con = dbc_ivr();
							foreach ($all_projects as $item) {
								$q = "SELECT MAX(attempts) FROM dialout_numbers WHERE projectid=".$item['id']." AND prov!='TEST'";		
								$r = mysql_query ($q); // Run the query.
								$row = mysql_fetch_assoc($r)
							?>
							<tr>
								<td align="left"><?php echo $item['name']; ?></td>
								<td align="left"><?php echo date('Y/m/d', strtotime($item['last_update'])); ?></td>
								<td align="left"><?php echo $row['MAX(attempts)']; ?></td>
								<td align="left"><?php echo $item['pif_number']; ?></td>
								<td align="left"><?php echo $item['active']; ?></td>
								<td><a href="javascript:void(0)" onclick="submitForm(<?php echo $item['id']; ?>)">Project Panel</a></td>
							</tr>
							<?php
							} // End of WHILE loop.
							mysql_close($con);
							?>
							<form method="POST" id="projectform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
								<input type="hidden" name="page" value="edit_project">
								<input type="hidden" id="projectid" name="id" value="">
							<form>
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
						<h2>Management</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						<a href="<?php echo $_SERVER['PHP_SELF'];?>?page=add_project" class="add-button"><span>Add new Project</span></a>
						<p>&nbsp;<p>
						<a href="<?php echo $_SERVER['PHP_SELF'];?>?page=edit_config" class="add-button"><span>Edit Configurations</span></a>
						<div class="cl">&nbsp;</div>

						<?php
						$con = dbc_ivr();
						$q = "SELECT id, name FROM dean_poll_projects WHERE active=1";
						$r = mysql_query ($q);
						$num_active_project = mysql_num_rows($r);
						while ($row = mysql_fetch_assoc($r)) {
							$active_projects[] = $row;
						}
						mysql_close($con);
						?>
						
						<p><?php echo $num_active_project; ?> Active Projects:</p>
						<?php
						if ($num_active_project > 0) {
							foreach ($active_projects as $item) {
							?>
							<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=edit_project&id=<?php echo $item['id']; ?>" class="ico edit">Project Panel</a>&nbsp;&nbsp;&nbsp;<?php echo $item['name']; ?></p>
							<?php
							}
						} // End of WHILE loop.
						?>
						
						
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
                                                //$channel_percentage = mysql_fetch_assoc($r);
						mysql_close($con);

                                                //preg_match('|/(.*?)/|s', $secondary_channel['value'], $matches);
                                                //$secondary_channel_value = $matches[1];

						//preg_match('|/(.*?)/|s', $default_dialout_channel, $matches);
                                                //$primary_channel_value = $matches[1];
						?>
						
						<p>Calls Per Sec: <?php echo $calls_per_second['value']; ?></p>
						<!-- 
						<p>Prime Channel Percent: <?php echo $channel_percentage['value']; ?>%</p>
						<p>Default(Primary) Provider: <?php echo $primary_channel_value; ?></p>
						<p>Secondary Provider: <?php echo $secondary_channel_value; ?></p>
						-->

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