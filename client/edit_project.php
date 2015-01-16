<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>
<div id="dialog" title="Attention!" style="display:none">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Are you sure you want to cancel the project?</p>
</div>

<script>
	function submitForm(directpage,action,projectid)
	{
		if (action == "cancel_project") {
		
			$("#dialog").dialog({
			modal: true,
			bgiframe: true,
			height: 180,
			autoOpen: false
		  });
		  
			$("#dialog").dialog('option', 'buttons', {
				"Yes": function () {
					$('#directpage').val(directpage);
					$('#projectid').val(projectid);
					$('#projectaction').val(action);
					$('#projectform').submit();
					$(this).dialog("close");
				},
				"Cancel the action": function () {
					$(this).dialog("close");
				}
			});
			$("#dialog").dialog("open");
		} else {
			$('#directpage').val(directpage);
			$('#projectid').val(projectid);
			$('#projectaction').val(action);
			$('#projectform').submit();
		}
	}
</script>
<script>
	$(function() {
		if ($("select[name='redial_interval']").val() == 'next_day_custom') {
			$('#redial_instruction').show();
		}
	});
</script>
<!--
<script>
	$(function() {
		$("select[name='redial_interval']").change(function() {
			if ($(this).val() == 'next_day_custom') {
				$('#redial_instruction').show();
				$("input[name='redial_instruction']").val('');
			} else {
				$('#redial_instruction').hide();
				$("input[name='redial_instruction']").val('N/A');
			}
		});
	});
</script>
-->
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$id = $_POST['id'];

//echo $id;
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
if ($row['project_type'] != "fndp") {
	safe_redirect("index.php");
}

if ($row['list_last_upload'] != null) {
	$list_last_modified = "Last modified at ". date("F d, Y H:i", strtotime($row['list_last_upload']));
} else {
	$list_last_modified = "";
}

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

$project_date = date('Y/m/d', strtotime($row['project_date']));


$dialplans = array();
foreach ($glb_dialplan_context as $dial_plan => $item) {
	$dialplans[] = $dial_plan;
}

$project_state = $row['project_state'];
if ($project_state != "initial") {
	$is_initial = false;
	$diabled = "disabled";
	if ($project_state == "ready") {
		$is_ready = true;
	} else {
		$is_ready = false;
	}
	if ($project_state == "approved") {
		$is_approved = true;
	} else {
		$is_approved = false;
	}
	if ($project_state == "complete") {
		$is_complete = true;
	} else {
		$is_complete = false;
	}
	if ($project_state=='cancelled') {
		$is_cancelled = true;
	} else {
		$is_cancelled = false;
	}
} else {
	$is_initial = true;
	$diabled = "";
}



$query_number_of_records = "SELECT COUNT(*) FROM dialout_numbers WHERE prov!='TEST' AND projectid=".$id;
//echo $query_number_of_records;
$result_number_of_records = mysql_query ($query_number_of_records);
$row_number_of_records = mysql_fetch_array($result_number_of_records);
$number_of_records = $row_number_of_records[0];

if ($number_of_records>0) {
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

				<div class="msg msg-error"><p><strong>Projects can only be dialed on or after the Project Date set below.</strong></p></div>
				
				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Project Name: <?php echo $row['name']; ?></h2>
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
								<td align="left" style="width:30%"><input type="text" class="datepicker field size4" name="project_date" readonly="true" <?php echo $diabled; ?>></td>
								<td align="left" style="width:20%">PIF Number</td>
								<td align="left" style="width:30%"><input type="text" data-validation="required" data-validation-error-msg=" " name="pif" class="field size4" value="<?php echo $row['pif_number']; ?>" disabled></td>
							</tr>
							<tr>
								<td align="left">Redial Interval</td>
								<td align="left"><select class="field size4" name="redial_interval" <?php echo $diabled; ?>>
									<?php
									for ($i=30; $i<=$max_redial_interval; $i=$i+30) {
										echo "<option value=".$i;
										if ($i == $row['redial_interval'])
											echo ' selected';
										echo ">".$i." Minutes</option>";
									}
									?>
									<option value="next_day_standard" <?php if ($row['redial_interval'] == 'next_day_standard') echo ' selected';?>>Next day with same start/end time</option>
									<option value="next_day_custom" <?php if ($row['redial_interval'] == 'next_day_custom') echo ' selected';?>>Next day with custom start/end time</option>
								</select></td>
								<td align="left">Rounds to Dial</td>
								<td align="left"><select class="field size4" name="redial_rounds" <?php echo $diabled; ?>>
									<?php
									for ($i=1; $i<=$max_redial_rounds; $i++) {
										echo "<option value=".$i;
										if ($i == $row['redial_rounds'])
											echo ' selected';
										echo ">".$i." Round(s)</option>";
									}
									?>
								</select></td>
							</tr>
							<tr id="redial_instruction">
								<td align="left" colspan=1>Custom Redial Instruction</td>
								<td align="left" colspan=3><input type="text" name="redial_instruction" value="<?php echo $row['redial_instruction']; ?>" class="field size1" <?php echo $diabled; ?>/></td>
							</tr>
							<tr>
								<td align="left">Start Time [Local Time | 24HR]</td>
								<td align="left"><input type="text" id="starttime" class="field size4" name="start_time" <?php echo $diabled; ?>></td>
								<td align="left">End Time [Local Time | 24HR]</td>
								<td align="left"><input type="text" id="endtime" class="field size4" name="end_time" <?php echo $diabled; ?>></td>
							</tr>
							<tr>
								<td align="left">Project Name</td>
								<td align="left"><input type="text" name="client_name" data-validation="required" data-validation-error-msg=" " class="field size4" value="<?php echo $client_name; ?>" <?php echo $diabled; ?>></td>
								<td align="left">FNDP Staff</td>
								<td align="left"><input type="text" name="consultant_name" data-validation="required" data-validation-error-msg=" " class="field size4" value="<?php echo $consultant_name; ?>" <?php echo $diabled; ?>></td>
							</tr>
							<tr>
								<td align="left">Caller ID</td>
								<td align="left"><input type="text" name="caller_id" data-validation="length" data-validation-length="10-10" data-validation-error-msg=" " class="field size4" value="<?php echo $caller_id; ?>" <?php echo $diabled; ?>></td>
								<td align="left">Caller Name</td>
								<td align="left"><input type="text" name="caller_name" data-validation="length" data-validation-length="max20" data-validation-error-msg=" " class="field size4" value="<?php echo $caller_name; ?>" <?php echo $diabled; ?>></td>
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

						</table>

					<?php
					if ($is_initial) {  // check if the project is complete. complete project cannot be changed
					?>
						<!-- Pagging -->
						<div class="pagging">
							<div class="left"><input type="submit" class="button" value="Save" /></div>
						</div>
						<!-- End Pagging -->
					<?php
					}  // check if the project is complete.
					?>
						
					</div>
					<!-- Table -->
					</form>
					
				</div>
				<!-- End Box -->
			
			
			<?php
			if ($is_initial) { // check if project is in initial state. only initial project can upload sound and import numbers
			?>




				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Upload Sound File (must be in .wav format. PCM 16 bit mono, bitrate = 128kbps, otherwise will not get uploaded)</h2>
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
								<td align="left" style="width:20%">Ending Message (closing message after first name question)</td>
								<td align="left" style="width:30%"><input type="file" name="ending_wav" class="field size4"></td>
								<?php
								}
								?>
							</tr>
								<?php
									if (is_dir($wav_file_path.$id)) {
										echo "<tr>";
										if (file_exists($wav_file_path.$id."/".$id.$live_wav_suffix)) {
											echo "<td align='left' colspan=2>";
											$live_exist_msg = "Live message uploaded (".getDuration($wav_file_path.$id."/".$id.$live_wav_suffix)." seconds), on ".date ("F d, Y H:i", filemtime($wav_file_path.$id."/".$id.$live_wav_suffix));
											echo $live_exist_msg;
											echo "</td>";
										} else {
											echo "<td align='left' colspan=2>";
											echo $live_not_exist_msg;
											echo "</td>";
										}
										if (file_exists($wav_file_path.$id."/".$id.$machine_wav_suffix)) {
											echo "<td align='left' colspan=2>";
											$machine_exist_msg = "AM message uploaded (".getDuration($wav_file_path.$id."/".$id.$machine_wav_suffix)." seconds), on ".date ("F d, Y H:i", filemtime($wav_file_path.$id."/".$id.$machine_wav_suffix));
											echo $machine_exist_msg;
											echo "</td>";
										} else {
											echo "<td align='left' colspan=2>";
											echo $machine_not_exist_msg;
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
						<h2 class="left">Testing</h2>
					</div>
					<!-- End Box Head -->	
					
					<form id="testing" name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<input type="hidden" name="page" value="send_testing">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Test Number</td>
								<td align="left" style="width:30%"><input type="text" name="test_number" data-validation="length" data-validation-length="max10" data-validation-error-msg=" " class="field size4"></td>
							</tr>
							<?php
							$loop_count = 0;
							foreach ($consultants as $username => $item) {
								if  ($loop_count == 0) echo '<tr>';
							?>
								<td align="left">Add <?php echo $item['name']; ?> to Test</td>
								<td align="left"><div class="onoffswitch">
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
							if  ($loop_count == 1) echo '<td></td><td></td><tr>';
							?>
						</table>


						<!-- Pagging -->
						<div class="pagging">
							<div class="left"><input type="submit" class="button" value="Send Testing" /></div>
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
						<h2 class="left">Import Project DNC list</h2>
					</div>
					<!-- End Box Head -->	
					
					<form name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="post">
					<input type="hidden" name="page" value="import_numbers">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Do Not Call List</td>
								<td align="left" style="width:40%"><input type="file" name="dncfile" class="field size4"></td>
								<?php
								$project_dnc_file = 'dnc/'.$id.'/DNC.csv';
								if (file_exists($project_dnc_file)) {
									$fp = file($project_dnc_file, FILE_SKIP_EMPTY_LINES);
								?>
								<td align="left" style="width:40%">Project DNC list uploaded (<?php echo count($fp); ?> records total), on <?php echo date ("F d, Y H:i", filemtime($project_dnc_file)); ?>.</td>
								<?php } else { ?>
								<td align="left" style="width:40%">Make sure the file is in CSV format, phone number in first column. This DNC list will apply to this project only.</td>
								<?php } ?>
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
								<?php if ($number_of_records>0) { ?>
								<td align="left" style="width:40%">Phone number list uploaded (<?php echo $number_of_records; ?> records total). </br><?php echo $list_last_modified; ?></td>
								<?php } else { ?>
								<td align="left" style="width:40%">Make sure the file is in CSV format, phone number in first column.</td>
								<?php } ?>
							</tr>
							<tr>
								<td align="left" style="width:100%" colspan=3>If the stats shown below do not change after importing, double check your data file, make sure you only have one sheet and one column (first column) with phone numbers.</td>
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
			}  // check if project is in initial state.
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

				<?php
				if ($is_initial OR $is_ready) {  // check if the project is complete. complete project cannot be changed
				?>
				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Project Status Control</h2>
					</div>
					<!-- End Box Head -->	
					
					<form id="editproject" name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<input type="hidden" name="page" value="edit_project_status_post">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<div class="table">


					<?php
					//if ($is_approved) {  // check if the project is approved. client can choose to complete an approved project only
					?>
					<!--
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Is Project Complete?</td>
								<td align="left" style="width:30%"><div class="onoffswitch">
									<input type="checkbox" name="project_complete" class="onoffswitch-checkbox" id="project_complete">
									<label class="onoffswitch-label" for="project_complete">
										<div class="onoffswitch-inner"></div>
										<div class="onoffswitch-switch"></div>
									</label>
								</div></td>
								<td></td>
							</tr>
						</table>
					-->
					<?php
					//}  // check if the project is approved.
					?>


						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Is Project Ready?</td>
								<td align="left" style="width:30%"><div class="onoffswitch">
									<input type="checkbox" name="project_ready" class="onoffswitch-checkbox" id="project_ready" <?php if ($is_ready) echo 'checked'; ?>>
									<label class="onoffswitch-label" for="project_ready">
										<div class="onoffswitch-inner"></div>
										<div class="onoffswitch-switch"></div>
									</label>
								</div></td>
								<td></td>
							</tr>
						</table>
					


						<!-- Pagging -->
						<div class="pagging">
							<div class="left"><input type="submit" class="button" value="Submit Change" /></div>
						</div>
						<!-- End Pagging -->

						
					</div>
					<!-- Table -->
					</form>
					
				</div>
				<!-- End Box -->
				<?php
				}  // check if the project is approved or is complete.
				?>

			</div>
			<!-- End Content -->
			
			<!-- Sidebar -->
			<div id="sidebar">
				
			<?php
			if ($is_initial) { // check if project got approved. only approved project can be activated by client
			?>
				<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>Phone List Manager</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						<div><a href="javascript:void(0)" onclick="submitForm('number_analysis','number_analysis',<?php echo $id; ?>)" class="add-button"><span>Enter Number Analysis</span></a></div>
						<!--
						<p>&nbsp;<p>
						<div><a href="javascript:void(0)" onclick="submitForm('project_action','add_back_dnc',<?php echo $id; ?>)" class="add-button"><span>Add back ALL DNC numbers</span></a></div>
						-->
						<div class="cl">&nbsp;</div>					
					</div>
				</div>
				<!-- End Box -->
			<?php
			}  // check if project got approved.
			?>

			<form method="POST" id="projectform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input type="hidden" id="directpage" name="page" value="">
				<input type="hidden" id="projectid" name="id" value="">
				<input type="hidden" id="projectaction" name="action" value="">
			<form>
			<?php
			if ($is_approved) { // check if project got approved. only approved project can be activated by client
			?>
				<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>Project Actions</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						<div><a href="javascript:void(0)" onclick="submitForm('project_action','start_dialing',<?php echo $id; ?>)" class="add-button"><span>Start/Resume Dialing</span></a></div>
						<p>&nbsp;<p>
						<div><a href="javascript:void(0)" onclick="submitForm('project_action','pause_dialing',<?php echo $id; ?>)" class="add-button"><span>Pause Dialing</span></a></div>
						<!--
						<p>&nbsp;<p>
						<div><a href="javascript:void(0)" onclick="submitForm('project_action','reset_undeliver',<?php echo $id; ?>)" class="add-button"><span>Re-run Undelivered Numbers</span></a></div>
						<p>&nbsp;<p>
						<div><a href="javascript:void(0)" onclick="submitForm('project_action','deactivate_all',<?php echo $id; ?>)" class="add-button"><span>Deactivate All Numbers</span></a></div>
						-->
						<div class="cl">&nbsp;</div>					
					</div>
				</div>
				<!-- End Box -->
			<?php
			}  // check if project got approved.
			?>
			
			<?php
			if ($is_complete) { // check if project is complete. only complete project can have report & result to download
			?>
				<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>Result Download</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						<?php
						$con = dbc_ivr();
						$q = "SELECT * FROM project_billing_info WHERE projectid=".$id;
						$r = mysql_query($q);
						$row = mysql_fetch_assoc($r);
						$projecttype = $row['billtype'];
						?>
						<div><a href="download_detail.php?projectid=<?php echo $id; ?>" class="add-button"><span>Download ALL Detail Result</span></a></div>
						<p>&nbsp;<p>
						<div><a href="download_pdf.php?projectid=<?php echo $id; ?>" class="add-button" target="_blank"><span>Download PDF Report</span></a></div>
						<p>&nbsp;<p>
						<div><a href="download_invoice.php?projecttype=stratcom&projectid=<?php echo $id; ?>" class="add-button" target="_blank"><span>Download Stratcom Invoice</span></a></div>
						<p>&nbsp;<p>
						<?php if ($projecttype != 'MP_Office') { ?>
						<div><a href="download_invoice.php?projecttype=fndp&projectid=<?php echo $id; ?>" class="add-button" target="_blank"><span>Download NDP Invoice</span></a></div>
						<p>&nbsp;<p>
						<?php } ?>
						<div><a href="download_fullzip.php?projectid=<?php echo $id; ?>" class="add-button"><span>Download Everything in Zip</span></a></div>
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
						$result = mysql_query("SELECT DISTINCT(result) FROM dialout_numbers WHERE projectid=".$id." AND result!='HUMAN' AND result!='MACHINE' AND result!='' AND result!='invalid' AND result!='NOTSURE'");
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
			<?php
			}  // check if project is complete.
			?>

				<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>Project Billing Info</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						<?php
						$con = dbc_ivr();
						$q = "SELECT * FROM project_billing_info WHERE projectid=".$id;
						$r = mysql_query($q);
						$row = mysql_fetch_assoc($r);
						echo "<p>Billing Name: ".$row['billname']."</p>";
						echo "<p>Billing Address: ".$row['billaddress']."</p>";
						echo "<p>Billing Phone: ".$row['billphone']."</p>";
						echo "<p>Billing Email: ".$row['billemail']."</p>";
						echo "<p>Project Type: ".$row['billtype']."</p>";
						?>
					</div>
				</div>
				<!-- End Box -->

								<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>Project Manager</h2>
					</div>
					<!-- End Box Head-->
					
					<div class="box-content">
						<?php
						if ($is_initial) {
							if (file_exists($wav_file_path.$id."/".$id.$machine_wav_suffix)) {
							?>
								<div><a href="javascript:void(0)" onclick="submitForm('project_action','disable_am',<?php echo $id; ?>)" class="add-button"><span>Disable Answer Machine Message</span></a></div>
								<p>&nbsp;<p>
							<?php
							} else if (file_exists($wav_file_path.$id."/".$id."AM.wav")) {
							?>
								<div><a href="javascript:void(0)" onclick="submitForm('project_action','enable_am',<?php echo $id; ?>)" class="add-button"><span>Enable Answer Machine Message</span></a></div>
								<p>&nbsp;<p>
							<?php
							}
						}
						?>

						<?php
						if (!$is_complete AND !$is_cancelled) {
						?>
							<div><a href="javascript:void(0)" onclick="submitForm('project_action','cancel_project',<?php echo $id; ?>)" class="add-button"><span>Cancel Project</span></a></div>
							<p>&nbsp;<p>
						<?php
						}
						?>
						<?php
						$q = "SELECT id, name FROM dean_poll_projects WHERE active=1 AND project_type='fndp' AND project_state='approved'";
						$r = mysql_query($q);
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
							<p>&nbsp;&nbsp;&nbsp;<?php echo $item['name']; ?></p>
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