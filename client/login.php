<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<title>Strategic Communications -- Client BVM interface</title>
<link rel="stylesheet" type="text/css" href="login_css/style.css" />
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script>
	  $(function () {

		  $("#dialog").dialog({
			modal: true,
			bgiframe: true,
			width: 800,
			height: 700,
			autoOpen: false
		  });

		  $("#confirm").click(function (e) {
			e.preventDefault();
			$("#dialog").dialog('option', 'buttons', {
			  "ACCEPT": function () {
				$("#login_form").submit();
				$(this).dialog("close");
			  },
			  "DECLINE": function () {
				$(this).dialog("close");
			  }
			});

			$("#dialog").dialog("open");

		  });


		 


		});
	</script>
</head>
<body>
<div id="dialog" title="Stratcom BVM System - Terms and Conditions of Use" style="display:none">
<p>
All calls placed through Stratcom's BVM system are regulated by the CRTC's do-not-call, telemarketing and automated dialing-announcing device rules (ADAD rules).
</br>
The full CRTC do-not-call, telemarketing and automated dialer rules can be viewed online here:
</br>
https://www.lnnte-dncl.gc.ca/ind/ntr-nrt-eng
</br>
Below is a summary of the main requirements for all calls place through Stratcom's BVM system.
</br>
</br>
<b>Permissable calling times</b>
</br>
Stratcom BVM calling hours are restricted to weekdays (Monday to Friday) from 9:00 am to 9:30 pm and 10:00 am to 6:00 pm on weekends (Saturday and Sunday) except where provincial laws apply. The hours refer to those of the person receiving the call.
</br>
BVM calls cannot be made to British Columbia on statutory holidays.
</br>
</br>
<b>No Solicitation</b>
</br>
Calls made through Stratcom's BVM system cannot be used for solicitation of any sort - including asking for donations or volunteers, either implicitly or explicitly, unless the caller has received prior express consent from the called party to be contacted for this reason.
</br>
</br>
<b>Required Content</b>
</br>
All calls made through Stratcom's BVM system must begin with a clear message identifying the person and/or organization on whose behalf the call is made. This "identification message" shall include:
</br>
1.	The name of the person and/or organization making the call
</br>
2.	An explanation of the purpose of the call
</br>
3.	A phone number where the originator of the call can be reached.
</br>
4.	An email address or mailing address where the originator of the call can be reached.
</br>
Phone numbers provided must be toll free, or local to the recipient of the call.
</br>
If a mailing address is provided it must include province and postal code.
</br>
If a Stratcom BVM is longer than 60 seconds, the "identification message" must also be repeated at the end of the BVM.
</br>
</br>
<b>Call Display Number</b>
</br>
Calls made through Stratcom's BVM system must display the phone number where the originator of the call can be reached.
</br>
Phone numbers provided and on call display must be active and monitored for at least 60 days after sending a call through Stratcom's BVM system.
</br>
</br>
<b>Stratcom Disclaimer:</b>
</br>
Strategic Communications is providing this information as a customer service, it should not be construed as legal advice or a legal opinion on any specific situation. Clients should contact their legal advisor if they have questions about the legal requirements applicable to its situation.
</br>
</br>
<b>Client Waiver:</b>
</br>
I have read the above information. I agree to comply with all Canadian laws and rules governing automated voice calls. I acknowledge that compliance with applicable laws and regulations is my responsibility.
</br>
</br>
<b>Indemnification:</b>
</br>
We shall indemnify and save harmless Stratcom from any damages, losses, costs and expenses (including legal costs) that Stratcom may incur, suffer or become liable for as a result of or in connection with any claim asserted against Stratcom to the extent such claim is based upon the provision of the Services or Optional Services, or both, other than the negligence or deliberate act of Stratcom in the provision of the Services or breach of this agreement by Stratcom.
</p>

</div>
<div class="container">
	<section id="content">
		<img src="https://dl.dropboxusercontent.com/u/57103960/StratcomLogo_WT_eng.png" alt="stratcom" width="272" height="86" /><br>
		<form name="input" id="login_form" action="index.php" method="post">
			<div>
				<input type="text" placeholder="Username" required="" id="username" name="user_id"/>
			</div>
			<div>
				<input type="password" placeholder="Password" required="" id="password" name="pass"/>
			</div>
			<div>
				<input type="submit" id="confirm" value="Log in" />
			</div>
		</form><!-- form -->
	</section><!-- content -->
</div><!-- container -->
</body>
</html>