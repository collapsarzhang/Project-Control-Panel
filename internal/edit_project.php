<?php
if (!isset($_SESSION['user_ivr'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$id = $_GET['id'];
//------------------      for project stats --------------------
$con = dbc_ivr();

$result=mysql_query("select count(id) from dialout_numbers where prov!='TEST' AND projectid=$id and active=1");
$row = mysql_fetch_assoc($result);
foreach ($row as $key)
$active_num=$key;

$result=mysql_query("select count(id) from dialout_numbers where prov!='TEST' AND (result is NULL OR (result!='invalid' AND result!='removed' AND result!='DNC')) AND projectid=".$id);
$row = mysql_fetch_assoc($result);
foreach ($row as $key)
$total_num=$key;

$result=mysql_query("select count(id) from dialout_numbers where prov!='TEST' AND projectid=$id and (result='HUMAN' or result like'PRESS%') ");
$row = mysql_fetch_assoc($result);
foreach ($row as $key)
$num_human=$key;

$result=mysql_query("select count(id) from dialout_numbers where prov!='TEST' AND projectid=$id and (result='MACHINE' or result='NOTSURE')");
$row = mysql_fetch_assoc($result);
foreach ($row as $key)
$num_machine=$key;

$result=mysql_query("select count(id) from dialout_numbers where prov!='TEST' AND projectid=$id and result='invalid'");
$row = mysql_fetch_assoc($result);
foreach ($row as $key)
$total_invalid_num=$key;

$result=mysql_query("select count(id) from dialout_numbers where prov!='TEST' AND projectid=$id and result='removed'");
$row = mysql_fetch_assoc($result);
foreach ($row as $key)
$total_removed_num=$key;

$result=mysql_query("select count(id) from dialout_numbers where prov!='TEST' AND projectid=$id and result='DNC'");
$row = mysql_fetch_assoc($result);
foreach ($row as $key)
$total_dnc_num=$key;

$result=mysql_query("select count(id) from dialout_numbers where prov!='TEST' AND projectid=$id and result='NOTSURE'");
$row = mysql_fetch_assoc($result);
foreach ($row as $key)
$num_notsure=$key;

$result=mysql_query("select count(id) from dialout_numbers where prov!='TEST' AND projectid=$id AND (result is NULL OR result='') AND attempts>0 AND active=0");
$row = mysql_fetch_assoc($result);
foreach ($row as $key)
$num_noreach=$key;

if ($total_num == 0) {
	$contact_rate = "0%";
} else {
	$contact_rate = number_format(($num_notsure+$num_machine+$num_human)/$total_num*100,0) . "%";
}

//-----------------      end of project stats ------------------------------







$q = "SELECT * FROM dean_poll_projects WHERE id=".$id;
$r = mysql_query ($q);
$row = mysql_fetch_assoc($r);
mysql_close($con);

$current_project_type = $glb_dialplan_context[$row['dialplan_context']]['type'];

$pieces = explode("; ", $row['Notes']);
$consultant_name = $pieces[0];
if (isset($pieces[1])) {
	$client_name = $pieces[1];
} else {
	$client_name = "";
}

$caller_string = $row['callerid'];
$matches = array();
preg_match('/"(.*?)"/s', $caller_string, $matches);
if (isset($matches[1])) {
	$caller_name = $matches[1];
} else {
	$caller_name = "";
}
preg_match('/<(.*?)>/s', $caller_string, $matches);
if (isset($matches[1])) {
	$caller_id = $matches[1];
} else {
	$caller_id = $caller_string;
}

// for import numbers
if (date("I")) {
	$daylight = "Summer";
} else {
	$daylight = "Winter";
}

$project_date = substr_replace($row['valid_users'], '/', 2, 0);
$project_date = date('y/').$project_date;



$dialplans = array();
foreach ($glb_dialplan_context as $dial_plan => $item) {
	$dialplans[] = $dial_plan;
}


if (($row['project_type'] == 'fndp') AND ($row['project_state'] == 'ready')) {
	$is_ready = true;
} else {
	$is_ready = false;
}
if (($row['project_type'] == 'fndp') AND ($row['project_state'] == 'approved')) {
	$is_approved = true;
} else {
	$is_approved = false;
}

if ($row['project_state'] == 'complete') {
	$diabled = "disabled";
	$is_complete = true;
} else {
	$is_complete = false;
	$diabled = "";
}
?>

<script>
	$(function() {
		var time_start = "<?php echo $row['time_start']; ?>";
		var time_end = "<?php echo $row['time_end']; ?>";
		$('#starttime').timepicker({'disableTextInput': true, 'timeFormat': 'H:i', 'minTime': '9:00am', 'maxTime': '9:00pm'}).timepicker('setTime', time_start);
		$('#endtime').timepicker({'disableTextInput': true, 'timeFormat': 'H:i', 'minTime': '9:00am', 'maxTime': '9:00pm'}).timepicker('setTime', time_end);
	});
</script>
<script>
	$(function() {
		var project_date = "<?php echo $project_date; ?>";
		$(".datepicker").datepicker({dateFormat: 'yy/mm/dd'}).datepicker("setDate", project_date);
	});
</script>

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
						<h2 class="left">Project Name: <?php echo $row['name']; ?></h2>
						<div class="right">
							<label>Project ID: <?php echo $row['id']; ?></label>
						</div>
					</div>
					<!-- End Box Head -->	
					
					<form id="editproject" name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<input type="hidden" name="page" value="edit_project_post">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Project Date</td>
								<td align="left" style="width:30%"><input type="text" class="datepicker field size4" name="project_date" readonly="true" <?php echo $diabled; ?>/></td>
								<td align="left" style="width:20%">PIF Number</td>
								<td align="left" style="width:30%"><input type="text" data-validation="required" data-validation-error-msg=" " name="pif" class="field size4" value="<?php echo $row['pif_number']; ?>" <?php echo $diabled; ?>/></td>
							</tr>
							<tr>
								<td align="left">Start Time [Local Time]</td>
								<td align="left"><input type="text" id="starttime" class="field size4" name="start_time" <?php echo $diabled; ?>/></td>
								<td align="left">End Time [Local Time]</td>
								<td align="left"><input type="text" id="endtime" class="field size4" name="end_time" <?php echo $diabled; ?>/></td>
							</tr>
							<tr>
								<td align="left">Client Name</td>
								<td align="left"><input type="text" name="client_name" data-validation="required" data-validation-error-msg=" " class="field size4" value="<?php echo $client_name; ?>" <?php echo $diabled; ?>/></td>
								<td align="left">Consultant Name</td>
								<td align="left"><input type="text" name="consultant_name" data-validation="required" data-validation-error-msg=" " class="field size4" value="<?php echo $consultant_name; ?>" <?php echo $diabled; ?>/></td>
							</tr>
							<tr>
								<td align="left">Caller ID</td>
								<td align="left"><input type="text" name="caller_id" data-validation="length" data-validation-length="10-10" data-validation-error-msg=" " class="field size4" value="<?php echo $caller_id; ?>" <?php echo $diabled; ?>/></td>
								<td align="left">Caller Name</td>
								<td align="left"><input type="text" name="caller_name" data-validation="length" data-validation-length="max20" data-validation-error-msg=" " class="field size4" value="<?php echo $caller_name; ?>" <?php echo $diabled; ?>/></td>
							</tr>
							<tr>
						<?php
						if (!in_array($row['dialplan_context'], $dialplans)) {
						?>
							<input type="hidden" name="dial_plan" value=<?php echo $row['dialplan_context']; ?>>
								<td align="left">Dial Plan</td>
								<td align="left">N/A (contact Tech if in doubt)</td>
						<?php
						} else {
						?>
							
								<td align="left">Dial Plan</td>
								<td align="left"><select class="field size4" name="dial_plan" <?php echo $diabled; ?>>
									<?php
									foreach ($glb_dialplan_context as $dial_plan => $item)
									{
										echo "<option value=".$dial_plan;
										if ($dial_plan == $row['dialplan_context'])
											echo ' selected';
										echo ">".$item['description']."</option>";
									}
									?>
								</select></td>
						<?php
						}
						?>
							<?php
							if (!$is_complete) {
							?>
								<td align="left">Activate Project</td>
								<td align="left">
									<div class="onoffswitch">
										<input type="checkbox" name="activate_project" class="onoffswitch-checkbox" id="activate_project" <?php if ($row['active']==1) echo 'checked'; ?>>
										<label class="onoffswitch-label" for="activate_project">
											<div class="onoffswitch-inner"></div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
								</td>
							<?php
							}
							?>
							</tr>
						</table>

					<?php
					if (!$is_complete) {
					?>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<?php
							$loop_count = 0;
							foreach ($consultants as $username => $item) {
								if  ($loop_count == 0) echo '<tr>';
							?>
								<td align="left" style="width:20%">Add <?php echo $item['name']; ?> to Test</td>
								<td align="left" style="width:30%"><div class="onoffswitch">
									<input type="checkbox" name="<?php echo $item['recogniz']; ?>" class="onoffswitch-checkbox" id="<?php echo $item['recogniz']; ?>">
									<label class="onoffswitch-label" for="<?php echo $item['recogniz']; ?>">
										<div class="onoffswitch-inner"></div>
										<div class="onoffswitch-switch"></div>
									</label>
								</div></td>
							<?php
								$loop_count++;
								if  ($loop_count == 2) {
									$loop_count = 0;
									echo '</tr>';
								}
							}
							?>
						</table>
					<?php
					}
					?>
					
					<?php
					if ($is_ready OR $is_approved) { // check if the project is ready or approved. only ready/approved project with type fndp can be approved or un-approved.
					?>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Is Project Approved?</td>
								<td align="left" style="width:30%"><div class="onoffswitch">
									<input type="checkbox" name="project_approved" class="onoffswitch-checkbox" id="project_approved" <?php if ($is_approved) echo 'checked'; ?>>
									<label class="onoffswitch-label" for="project_approved">
										<div class="onoffswitch-inner"></div>
										<div class="onoffswitch-switch"></div>
									</label>
								</div></td>
								<td></td>
							</tr>
						</table>
					<?php
					} // check if the project is ready or approved.
					?>

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
				
		<?php
		if (!$is_complete) {
		?>

			<?php
			if ($current_project_type == "Polling") {
			?>
				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Upload Polling Questions</h2>
					</div>
					<!-- End Box Head -->	
					
					<form name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="post">
					<input type="hidden" name="page" value="upload_pollings">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Polling Script</td>
								<td align="left" style="width:40%"><input type="file" name="polling_file" class="field size4"></td>
								<td align="left" style="width:40%"><a target="_blank" href="check_polling.php?id=<?php echo $id; ?>">Check Current Polling Questions [Tech use Only]</a></td>
							</tr>
							<tr>
								<td align="left" style="width:100%" colspan=3>Please follow the polling template structure to prepare your polling script. [don't forget to save the file as csv format] <a href="includes/Polling_Script_Demo_Explaination.xlsx">Click to download polling template</a></td>
							</tr>
						</table>


						<!-- Pagging -->
						<div class="pagging">
							<div class="left"><input type="submit" class="button" value="Upload" /></div>
						</div>
						<!-- End Pagging -->
						
					</div>
					<!-- Table -->
					</form>
					
				</div>
				<!-- End Box -->

			<?php
			}
			?>

				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Upload Sound File</h2>
					</div>
					<!-- End Box Head -->	
					
					<form name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="post">
					<input type="hidden" name="page" value="upload_wav">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<?php
								if ($current_project_type != "Polling") {
								?>
								<td align="left" style="width:20%">Live Pickup Audio</td>
								<td align="left" style="width:30%"><input type="file" name="human_wav" class="field size4"></td>
								<?php
								}
								?>
								<td align="left" style="width:20%">Answer Machine Audio</td>
								<td align="left" style="width:30%"><input type="file" name="am_wav" class="field size4"></td>
								<?php
								if ($current_project_type == "Polling") {
								?>
								<td align="left" style="width:20%">Ending Message (closing message before hanging up)</td>
								<td align="left" style="width:30%"><input type="file" name="ending_wav" class="field size4"></td>
								<?php
								}
								?>
							</tr>
								<?php
									if (is_dir($wav_file_path.$id)) {
										echo "<tr>";
										if ($current_project_type != "Polling") {
											if (file_exists($wav_file_path.$id."/".$id."00.wav")) {
												echo "<td align='left' colspan=2>";
												echo $live_exist_msg;
												echo "</td>";
											} else {
												echo "<td align='left' colspan=2>";
												echo $live_not_exist_msg;
												echo "</td>";
											}
										}
										if (file_exists($wav_file_path.$id."/".$id."99.wav")) {
											echo "<td align='left' colspan=2>";
											echo $machine_exist_msg;
											echo "</td>";
										} else {
											echo "<td align='left' colspan=2>";
											echo $machine_not_exist_msg;
											echo "</td>";
										}
										if (file_exists($wav_file_path.$id."/".$id."98.wav")) {
											echo "<td align='left' colspan=2>";
											echo "Ending message exists";
											echo "</td>";
										}
										echo "</tr>";
									} else {
										echo "<tr><td align='left' colspan=4>".$dir_not_exist_msg."</td></tr>";
									}
								?>
						</table>


						<!-- Pagging -->
						<div class="pagging">
							<div class="left"><input type="submit" class="button" value="Upload" /></div>
						</div>
						<!-- End Pagging -->
						
					</div>
					<!-- Table -->
					</form>
					
				</div>
				<!-- End Box -->

				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Import Phone Numbers</h2>
					</div>
					<!-- End Box Head -->	
					
					<form name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="post">
					<input type="hidden" name="page" value="import_numbers">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Phone Number List</td>
								<td align="left" style="width:40%"><input type="file" name="userfile" class="field size4"></td>
								<td align="left" style="width:40%">Make sure the file is in CSV format, phone number in first column. Currently it's <?php echo $daylight; ?> time.</td>
							</tr>
							<tr>
								<td align="left" style="width:20%">Do Not Call List</td>
								<td align="left" style="width:40%"><input type="file" name="dncfile" class="field size4"></td>
								<td align="left" style="width:40%">Make sure the file is in CSV format, phone number in first column.</td>
							</tr>
							<?php if ($current_project_type == "Polling") { ?>
							<tr>
								<td align="left" style="width:20%">Is there a first name question?</td>
								<td align="left" style="width:40%"><div class="onoffswitch">
									<input type="checkbox" name="is_first_name" class="onoffswitch-checkbox" id="is_first_name">
									<label class="onoffswitch-label" for="is_first_name">
										<div class="onoffswitch-inner"></div>
										<div class="onoffswitch-switch"></div>
									</label>
								</div></td>
								<td align="left" style="width:40%">if there is a first name question, the firstnames should be seperated by semicolon and be in the second column of the csv file</td>
							</tr>
							<?php } ?>
							<tr>
								<td align="left" style="width:100%" colspan=3>If the stats shown below do not change after importing, double check your data file, make sure you only have one sheet and one column (first column) with phone numbers (no delimiter allowed).</td>
							</tr>
						</table>


						<!-- Pagging -->
						<div class="pagging">
							<div class="left"><input type="submit" class="button" value="Import" /></div>
						</div>
						<!-- End Pagging -->
						
					</div>
					<!-- Table -->
					</form>
					
				</div>
				<!-- End Box -->
			<?php
			}
			?>
			
				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Project Statistics</h2>
					</div>
					<!-- End Box Head -->	

					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Total Valid Numbers</td>
								<td align="left" style="width:13%"><?php echo $total_num; ?></td>
								<td align="left" style="width:20%">Active Numbers</td>
								<td align="left" style="width:13%"><?php echo $active_num; ?></td>
								<td align="left" style="width:20%">Current Contact Rate</td>
								<td align="left" style="width:13%"><?php echo $contact_rate; ?></td>
							</tr>
							<tr>
								<td align="left">Human</td>
								<td align="left"><?php echo $num_human; ?></td>
								<td align="left">Machine</td>
								<td align="left"><?php echo $num_machine; ?></td>
								<td align="left">Undeliverable</td>
								<td align="left"><?php echo $num_noreach; ?></td>
							</tr>
							<tr>
								<td align="left">Total Invalid Numbers</td>
								<td align="left"><?php echo $total_invalid_num; ?></td>
								<td align="left">Total Removed Numbers</td>
								<td align="left"><?php echo $total_removed_num; ?></td>
								<td align="left">Total DNC Numbers</td>
								<td align="left"><?php echo $total_dnc_num; ?></td>
							</tr>
						</table>	
					</div>
					<!-- Table -->
					
				</div>
				<!-- End Box -->

			</div>
			<!-- End Content -->
			
			<!-- Sidebar -->
			<div id="sidebar">
			
			<?php
			if (!$is_complete) {
			?>

				<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>List Operation</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						<div><a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=number_analysis&id=<?php echo $id; ?>" class="add-button"><span>Enter Number Analysis</span></a></div>
						<div class="cl">&nbsp;</div>					
					</div>
				</div>
				<!-- End Box -->
				
				<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>Project Actions</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
					<?php
					if (file_exists($wav_file_path.$id."/".$id."99.wav")) {
					?>
						<div><a href="<?php echo $_SERVER['PHP_SELF'];?>?page=project_action&action=disable_am_message&id=<?php echo $id; ?>" class="add-button confirmLink"><span>Disable AM Message</span></a></div>
						<p>&nbsp;<p>
					<?php
					} else if (file_exists($wav_file_path.$id."/".$id."AM.wav")) {
					?>
						<div><a href="<?php echo $_SERVER['PHP_SELF'];?>?page=project_action&action=enable_am_message&id=<?php echo $id; ?>" class="add-button confirmLink"><span>Enable AM Message</span></a></div>
						<p>&nbsp;<p>
					<?php
					}
					?>
						<div><a href="<?php echo $_SERVER['PHP_SELF'];?>?page=project_action&action=activate_number&id=<?php echo $id; ?>" class="add-button"><span>Activate Numbers</span></a></div>
						<p>&nbsp;<p>
						<div><a href="<?php echo $_SERVER['PHP_SELF'];?>?page=project_action&action=reset_undeliver&id=<?php echo $id; ?>" class="add-button"><span>Re-activate Undeliverable Numbers</span></a></div>
						<p>&nbsp;<p>
						
						<div><a href="<?php echo $_SERVER['PHP_SELF'];?>?page=project_action&action=reset_am&id=<?php echo $id; ?>" class="add-button confirmLink"><span>Re-activate Answer Machines</span></a></div>
						<p>&nbsp;<p>
						
						<div><a href="<?php echo $_SERVER['PHP_SELF'];?>?page=project_action&action=deactivate&id=<?php echo $id; ?>" class="add-button"><span>Deactivate All Numbers</span></a></div>
						<p>&nbsp;<p>
						
						<div><a href="<?php echo $_SERVER['PHP_SELF'];?>?page=project_action&action=finish_project&id=<?php echo $id; ?>" class="add-button confirmLink"><span>Mark project as finished</span></a></div>
						
						<div class="cl">&nbsp;</div>

					</div>
				</div>
				<!-- End Box -->
				
			<?php
			}
			?>

				<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>Result Download / Project Finish</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						
						<div><a href="download_detail.php?projectid=<?php echo $id; ?>" class="add-button"><span>Download ALL Detail Result</span></a></div>
						<p>&nbsp;<p>
						<div><a href="download_pdf.php?projectid=<?php echo $id; ?>" class="add-button" target="_blank"><span>Download PDF Report</span></a></div>
						<?php
						if ($current_project_type == "Polling") {
						?>
						<p>&nbsp;<p>
						<div><a href="download_polling_outbound.php?projectid=<?php echo $id; ?>" class="add-button" target="_blank"><span>Download Polling Outbound Result</span></a></div>
						<p>&nbsp;<p>
						<div><a href="download_polling_inbound.php?projectid=<?php echo $id; ?>" class="add-button" target="_blank"><span>Download Polling Inbound Result</span></a></div>
						<?php
						}
						?>
						<?php
						$con = dbc_ivr();
						$result = mysql_query("SELECT DISTINCT(result) FROM dialout_numbers WHERE projectid=".$id." AND prov!='TEST' AND result!='HUMAN' AND result!='MACHINE' AND result!='' AND result!='invalid' AND result!='NOTSURE'");
						while ($row = mysql_fetch_assoc($result)) {
						?>
						<p>&nbsp;<p>
						<div><a href="download_unique.php?type=<?php echo $row['result']; ?>&projectid=<?php echo $id; ?>" class="add-button" target="_blank"><span>Download <?php echo $row['result']; ?> Detail Result</span></a></div>
						<?php
						}
						mysql_close($con);
						?>
						<div class="cl">&nbsp;</div>					
					</div>
				</div>
				<!-- End Box -->

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
							<p><a href="index.php?page=edit_project&id=<?php echo $item['id']; ?>" class="ico edit">Project Panel</a>&nbsp;&nbsp;&nbsp;<?php echo $item['name']; ?></p>
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