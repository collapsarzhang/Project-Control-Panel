<?php
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

$con = dbc_ivr();
$q = "SELECT value FROM config WHERE id=3";		
$r = mysql_query ($q); // Run the query.
$calls_per_second = mysql_fetch_assoc($r);
$q = "SELECT value FROM config WHERE id=15";		
$r = mysql_query ($q); // Run the query.
$channel_percentage = mysql_fetch_assoc($r);
mysql_close($con);

$current_calls_per_sec = $calls_per_second['value'];
$current_prime_channel_percentage = $channel_percentage['value'];

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
						<h2 class="left">Configurations</h2>
					</div>
					<!-- End Box Head -->	
					
					<form name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<input type="hidden" name="page" value="edit_config_post">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:30%">Calls Per Sec</td>
								<td align="left" style="width:70%"><select class="field size4" name="calls_per_second">
									<?php
									for ($i=1;$i<=$max_calls_per_sec;$i++)
									{
										echo "<option value=".$i;
										if ($i==$current_calls_per_sec) {
											echo " selected";
										}
										echo ">".$i."</option>";
									}
									?>
							</select></td>
							</tr>
							<tr>
								<td align="left" colspan=2>This parameter decides how many calls are initialized per second. Keep it at 2-3 unless it's a large BVM in very short time (in which case you will need to set the below parameter to get load balancing to our secondary provider).</td>
							</tr>
							<tr>
								<td align="left" style="width:30%">Prime Channel %</td>
								<td align="left" style="width:70%"><select class="field size4" name="channel_percentage">
									<?php
									for ($i=0;$i<=$max_prime_channel_percentage;$i+=10)
									{
										echo "<option value=".$i;
										if ($i==$current_prime_channel_percentage) {
											echo " selected";
										}
										echo ">".$i."%</option>";
									}
									?>
							</select></td>
							</tr>
							<tr>
								<td align="left" colspan=2>Keep this parameter at 100% for most projects. 100% means all traffic will go through our primary provider. The lower this parameter is, the less traffic will go through primary provider, the more traffic will go through secondary provider. For example, if this is set to 80%, then 80% of the traffic will go through our primary provider, and the rest 20% traffic will go through secondary provider.</td>
							</tr>
						</table>

						<!-- Pagging -->
						<div class="pagging">
							<div class="left"><input type="submit" class="button" value="Save" /></div>
						</div>
						<!-- End Pagging -->
						
					</div>
					<!-- Table -->
					</form>
					
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
							<p><a href="#" class="ico edit">Edit</a>&nbsp;&nbsp;&nbsp;<?php echo $item['name']; ?></p>
							<?php
							}
						} // End of WHILE loop.
						?>
						
						
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
