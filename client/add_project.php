<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<script>
	$(function() {
		$('#starttime').timepicker({'disableTextInput': true, 'timeFormat': 'H:i', 'minTime': '9:00am', 'maxTime': '9:00pm'}).timepicker('setTime', '10:00');
		$('#endtime').timepicker({'disableTextInput': true, 'timeFormat': 'H:i', 'minTime': '9:00am', 'maxTime': '9:00pm'}).timepicker('setTime', '20:00');
	});
</script>
<script>
	$(function() {
		$(".datepicker").datepicker({dateFormat: 'yy/mm/dd'}).datepicker("setDate", '0');
	});
</script>

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
						<h2 class="left">Adding Project</h2>
					</div>
					<!-- End Box Head -->	
					
					<form id="addproject" name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<input type="hidden" name="page" value="add_project_post">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Project Date</td>
								<td align="left" style="width:30%"><input type="text" class="datepicker field size4" name="project_date" readonly="true" /></td>
								<td align="left">Dial Plan</td>
								<td align="left"><select class="field size4" name="dial_plan">
									<?php
									foreach ($glb_dialplan_context as $dial_plan => $item)
									{
										echo "<option value=".$dial_plan;
										echo ">".$item['description']."</option>";
									}
									?>
								</select></td>
							</tr>
							<tr>
								<td align="left">Redial Interval</td>
								<td align="left"><select class="field size4" name="redial_interval">
									<?php
									for ($i=30; $i<=$max_redial_interval; $i=$i+30) {
										echo "<option value=".$i;
										echo ">".$i." Minutes</option>";
									}
									?>
									<option value="next_day_standard">Next day with same start/end time</option>
									<option value="next_day_custom">Next day with custom start/end time</option>
								</select></td>
								<td align="left">Rounds to Dial</td>
								<td align="left"><select class="field size4" name="redial_rounds">
									<?php
									for ($i=1; $i<=$max_redial_rounds; $i++) {
										echo "<option value=".$i;
										echo ">".$i." Round(s)</option>";
									}
									?>
								</select></td>
							</tr>
							<tr id="redial_instruction" style="display:none;">
								<td align="left" colspan=1>Custom Redial Instruction</td>
								<td align="left" colspan=3><input type="text" name="redial_instruction" value="N/A" data-validation="required" data-validation-error-msg=" " class="field size1" /></td>
							</tr>
							<tr>
								<td align="left">Start Time [Local Time | 24HR]</td>
								<td align="left"><input type="text" id="starttime" class="field size4" name="start_time" /></td>
								<td align="left">End Time [Local Time | 24HR]</td>
								<td align="left"><input type="text" id="endtime" class="field size4" name="end_time" /></td>
							</tr>
							<tr>
								<td align="left">Project Name</td>
								<td align="left"><input type="text" name="client_name" data-validation="required" data-validation-error-msg=" " class="field size4" /></td>
								<td align="left">FNDP Staff</td>
								<td align="left"><input type="text" name="consultant_name" data-validation="required" data-validation-error-msg=" " class="field size4" /></td>
							</tr>
							<tr>
								<td align="left">Caller ID</td>
								<td align="left"><input type="text" name="caller_id" data-validation="length" data-validation-length="10-10" data-validation-error-msg=" " class="field size4" /></td>
								<td align="left">Caller Name</td>
								<td align="left"><input type="text" name="caller_name" data-validation="length" data-validation-length="max20" data-validation-error-msg=" " class="field size4" /></td>
							</tr>
							<tr>
								<td align="left">Project Type</td>
								<td align="left"><select class="field size4" name="bill_type">
									<?php
									foreach ($bill_types as $item)
									{
										echo "<option value=".$item;
										echo ">".$item."</option>";
									}
									?>
								</select></td>
								<td align="left">Setup</td>
								<td align="left"><select class="field size4" name="bill_setup_types">
									<?php
									foreach ($bill_setup_types as $item)
									{
										echo "<option value=".$item;
										echo ">".$item."</option>";
									}
									?>
								</select></td>
							</tr>
							<tr>
								<td align="left">List Order / Data Pull</td>
								<td align="left"><select class="field size4" name="bill_data_return_types">
									<?php
									foreach ($bill_data_return_types as $item)
									{
										echo "<option value=".$item;
										echo ">".$item."</option>";
									}
									?>
								</select></td>
								<td align="left">First Name Verification</td>
								<td align="left"><select class="field size4" name="bill_firstname_verify_types">
									<?php
									foreach ($bill_firstname_verify_types as $item)
									{
										echo "<option value=".$item;
										echo ">".$item."</option>";
									}
									?>
								</select></td>
							</tr>
							<tr>
								<td align="left">Inbound # for IVR</td>
								<td align="left"><select class="field size4" name="bill_inbound_number_types">
									<?php
									foreach ($bill_inbound_number_types as $item)
									{
										echo "<option value=".$item;
										echo ">".$item."</option>";
									}
									?>
								</select></td>
								<td align="left">Invoice Language</td>
								<td align="left"><select class="field size4" name="bill_language_types">
									<?php
									foreach ($bill_language_types as $item)
									{
										echo "<option value=".$item;
										echo ">".$item."</option>";
									}
									?>
								</select></td>
							</tr>
							<tr>
								<td align="left">Billing Name</td>
								<td align="left"><input type="text" name="bill_name" data-validation="required" data-validation-error-msg=" " class="field size4" /></td>
								<td align="left">Billing Phone</td>
								<td align="left"><input type="text" name="bill_phone" data-validation="required" data-validation-error-msg=" " class="field size4" /></td>
							</tr>
							<tr>
								<td align="left" colspan=1>Billing Email</td>
								<td align="left" colspan=3><input type="text" name="bill_email" data-validation="required" data-validation-error-msg=" " class="field size1" /></td>
							</tr>
							<tr>
								<td align="left" colspan=1>Billing Address</td>
								<td align="left" colspan=3><input type="text" name="bill_address" data-validation="required" data-validation-error-msg=" " class="field size1" /></td>
							</tr>
						</table>

						<!-- Pagging -->
						<div class="pagging">
							<div class="left"><input type="submit" class="button" value="Add Project" /></div>
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
						$q = "SELECT id, name FROM dean_poll_projects WHERE active=1 AND project_type='fndp'";
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
