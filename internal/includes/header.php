<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="Stratcom Simple IVR">
    <meta name="author" content="Kevin Zhang">


    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" />
	<link rel="stylesheet" href="css/jquery.timepicker.css" type="text/css" />
	
	<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/jquery.timepicker.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.38/jquery.form-validator.min.js"></script>
	
	<title>Stratcom Simple IVR</title>
	<script >
	$(function () {
		$.validate({
		  form : '#addproject, #editproject'
		});
	}); 
	</script>

	<script>
	  $(function () {

		  $("#dialog").dialog({
			modal: true,
			bgiframe: true,
			height: 180,
			autoOpen: false
		  });

		  $(".confirmLink").click(function (e) {
			e.preventDefault();
			
			var hrefAttribute = $(this).attr("href");

			$("#dialog").dialog('option', 'buttons', {
			  "Ok, I understand": function () {
				window.location.href = hrefAttribute;
				$(this).dialog("close");
			  },
			  "Cancel the action": function () {
				$(this).dialog("close");
			  }
			});

			$("#dialog").dialog("open");

		  });


		 


		});
	</script>

</head>
<body>

<div id="dialog" title="Attention!" style="display:none">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This action is for special use only, please make sure you understand what this does before proceeding. Contact Evan if in doubt.</p>
</div>

<?php include("nav.php")?>
