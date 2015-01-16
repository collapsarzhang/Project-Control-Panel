<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="Stratcom BVM client interface -- FNDP">
    <meta name="author" content="Kevin Zhang">


    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" />
	<link rel="stylesheet" href="css/jquery.timepicker.css" type="text/css" />
	
	<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/jquery.timepicker.min.js"></script>

	<script src="js/jquery.form-validator.min.js"></script>
	
	<title>Stratcom BVM client interface -- NDP</title>
	<script >
	$(function () {
		$.validate({
		  form : '#addproject, #editproject, #testing'
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
			  "Yes": function () {
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


<?php include("nav.php")?>
