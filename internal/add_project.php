<?php
if (!isset($_SESSION['user_ivr'])) {
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
						<h2 class="left">Adding Porject</h2>
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
								<td align="left" style="width:20%">PIF Number</td>
								<td align="left" style="width:30%"><input type="text" data-validation="required" data-validation-error-msg=" " name="pif" class="field size4" /></td>
							</tr>
							<tr>
								<td align="left">Start Time [Local Time]</td>
								<td align="left"><input type="text" id="starttime" class="field size4" name="start_time" /></td>
								<td align="left">End Time [Local Time]</td>
								<td align="left"><input type="text" id="endtime" class="field size4" name="end_time" /></td>
							</tr>
							<tr>
								<td align="left">Client Name</td>
								<td align="left"><input type="text" name="client_name" data-validation="required" data-validation-error-msg=" " class="field size4" /></td>
								<td align="left">Consultant Name</td>
								<td align="left"><input type="text" name="consultant_name" data-validation="required" data-validation-error-msg=" " class="field size4" /></td>
							</tr>
							<tr>
								<td align="left">Caller ID</td>
								<td align="left"><input type="text" name="caller_id" data-validation="length" data-validation-length="10-10" data-validation-error-msg=" " class="field size4" /></td>
								<td align="left">Caller Name</td>
								<td align="left"><input type="text" name="caller_name" data-validation="length" data-validation-length="max20" data-validation-error-msg=" " class="field size4" /></td>
							</tr>
							<tr>
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

<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {maxChars:255,isRequired:true});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "phone_number", {format:"phone_custom", pattern:"0000", isRequired:true});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "email", {isRequired:true});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "phone_number", {format:"phone_custom", pattern:"0000000000", isRequired:false});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "integer", {isRequired:false, maxValue:2147483647, allowNegative:false});
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "time", {isRequired:false, format:"HH:mm"});
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "time", {isRequired:false, format:"HH:mm"});
var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8", "none", {maxChars:255, isRequired:false});
var sprytextfield9 = new Spry.Widget.ValidationTextField("sprytextfield9", "none", {maxChars:255, isRequired:false});
var sprytextfield10 = new Spry.Widget.ValidationTextField("sprytextfield10", "none", {maxChars:255, isRequired:false});
var sprytextfield11 = new Spry.Widget.ValidationTextField("sprytextfield11", "none", {maxChars:255, isRequired:false});
</script>