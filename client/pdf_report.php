<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

include_once("includes/defines.inc.php");

include_once ("mpdf/mpdf.php");

$con = dbc_ivr();

$result = mysql_query("SELECT * FROM dean_poll_projects WHERE id=".$id);
$row = mysql_fetch_array($result);
$projectname = $row['name'];
$pif = $row['pif_number'];
$dialplanraw = $row['dialplan_context'];
if (isset($glb_dialplan_context[$dialplanraw])) {
	$dialplanname = $glb_dialplan_context[$dialplanraw]['description'];
} else {
	$dialplanname = "Custom IVR";
}

if ($dialplanname == "Custom IVR") {
	$livemessageduration = "Custom IVR";
	$ammessageduration = "Custom IVR";
	$livemessagedurationwithivr = "Custom IVR";
} else {
	$livemessagepath = $wav_file_path.$id."/".$id.$live_wav_suffix;
	$ammessagepath = $wav_file_path.$id."/".$id.$machine_wav_suffix;
	if (isset($livemessagepath)) {
		$livemessageduration = getDuration($livemessagepath);
	} else {
		$livemessageduration = "N/A";
	}
	if (isset($ammessagepath)) {
		$ammessageduration = getDuration($ammessagepath);
	} else {
		$ammessageduration = "N/A";
	}
	if ($livemessageduration == "N/A") {
		$livemessagedurationwithivr = "N/A";
	} else {
		$livemessagedurationwithivr = $livemessageduration + $glb_dialplan_context[$dialplanraw]['options']*5;
	}
}

if ($livemessagedurationwithivr > 60) {
	$priceperlivemessage = (0.055/60)*$livemessagedurationwithivr;
} else {
	$priceperlivemessage = 0.055;
}
$priceperlivemessage = "$".$priceperlivemessage;

if ($ammessageduration > 60) {
	$priceperammessage = (0.055/60)*$ammessageduration;
} else {
	$priceperammessage = 0.055;
}
$priceperammessage = "$".$priceperammessage;

if ($livemessageduration != "Custom IVR" AND $livemessageduration != "N/A") {
	$livemessageduration = $livemessageduration." seconds";
}
if ($ammessageduration != "Custom IVR" AND $ammessageduration != "N/A") {
	$ammessageduration = $ammessageduration." seconds";
}
if ($livemessagedurationwithivr != "Custom IVR" AND $livemessagedurationwithivr != "N/A") {
	$livemessagedurationwithivr = $livemessagedurationwithivr." seconds";
}

$callerstring = $row['callerid'];
$matches = array();
preg_match('/"(.*?)"/s', $callerstring, $matches);
if (isset($matches[1])) {
	$callername = $matches[1];
} else {
	$callername = "";
}
preg_match('/<(.*?)>/s', $callerstring, $matches);
if (isset($matches[1])) {
	$callerid = $matches[1];
} else {
	$callerid = $callerstring;
}

$q = "SELECT * FROM project_billing_info WHERE projectid=".$id;
$r = mysql_query($q);
$row = mysql_fetch_array($r);
$billname = $row['billname'];
$billaddress = $row['billaddress'];
$billphone = $row['billphone'];
$billemail = $row['billemail'];
$projecttype = $row['billtype'];


$result = mysql_query("SELECT COUNT(*) FROM dnc_list WHERE projectid=".$id);
$row = mysql_fetch_array($result);
$DNC_added = $row[0];

$result = mysql_query("SELECT COUNT(*) FROM dialout_numbers WHERE prov!='TEST' AND result!='invalid' AND result!='removed' AND projectid=".$id);
$row = mysql_fetch_array($result);
$total_num = $row[0];

$result = mysql_query("SELECT COUNT(*) FROM dialout_numbers WHERE prov!='TEST' AND (result='HUMAN' OR result like'PRESS%') AND projectid=".$id);
$row = mysql_fetch_array($result);
$num_human = $row[0];

$result = mysql_query("SELECT COUNT(*) FROM dialout_numbers WHERE prov!='TEST' AND (result='MACHINE' OR result='NOTSURE') AND projectid=".$id);
$row = mysql_fetch_array($result);
$num_machine = $row[0];

$result = mysql_query("SELECT COUNT(*) FROM dialout_numbers WHERE prov!='TEST' AND (result is NULL OR result='') AND projectid=".$id);
$row = mysql_fetch_array($result);
$num_noreach = $row[0];

$result=mysql_query("SELECT MAX(lastattempt) FROM dialout_numbers WHERE prov!='TEST' AND projectid=".$id);
$row = mysql_fetch_array($result);
$projectend = substr($row[0],0,10);

$result=mysql_query("SELECT MIN(lastattempt) FROM dialout_numbers WHERE prov!='TEST' AND attempts>0 AND projectid=".$id);
$row = mysql_fetch_array($result);
$projectstart = substr($row[0],0,10);

mysql_close($con);

$num_delivered = $num_human + $num_machine;
if ($num_delivered > 0) {
	$connectpercentage = number_format(($num_delivered/$total_num)*100, 0)."%";
	$amindelivered = number_format(($num_machine/$num_delivered)*100, 1)."%";
	$humanindelivered = number_format(($num_human/$num_delivered)*100, 1)."%";
} else {
	$connectpercentage = "0%";
	$amindelivered = "0%";
	$humanindelivered = "0%";
}

$aminall = number_format(($num_machine/$total_num)*100, 1)."%";
$humaninall = number_format(($num_human/$total_num)*100, 1)."%";
$undeliveredinall = number_format(($num_noreach/$total_num)*100, 1)."%";

$reportdate = date('Y-m-d');

$mpdf=new mPDF('UTF-8','Letter','','',20,15,48,25,10,10); 
$mpdf->SetTitle("STRATCOM NDP BVM Report");
$mpdf->SetAuthor("STRATCOM");
$mpdf->SetDisplayMode('fullpage');

$html = '
<html>
<head>
<style>
body {font-family: sans-serif;
	font-size: 10pt;
}
p {    margin: 0pt;
}
td { vertical-align: top; }
.items td {
	border-left: 0.1mm solid #000000;
	border-right: 0.1mm solid #000000;
}
table thead td { background-color: #EEEEEE;
	text-align: center;
	border: 0.1mm solid #000000;
}
.items td.blanktotal {
	background-color: #FFFFFF;
	border: 0mm none #000000;
	border-top: 0.1mm solid #000000;
	border-right: 0.1mm solid #000000;
}
.items td.totals {
	text-align: right;
	border: 0.1mm solid #000000;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%"><tr>
<td><img src="images/logo.jpg"></td>
</tr></table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
<div>Toronto: 1179 King Street West Suite 202 Toronto ON M6K 3C5 PH 416.537.6100 FX 416.588.3490</div>
<div>Vancouver: 1770 West 7th Ave. Suite 305 Vancouver BC V6J 4Y6 PH 604.681.3030 FX 604.681.2025</div>
<div>Ottawa: 100 Sparks Street 8th Floor Ottawa ON K1P 5B7 PH 613.916.6215 FX 613.238.9997</div>
</div>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->


<table width="100%" style="font-size: 11pt; cellpadding="8">
<tr>
<td align="left" width="25%">PIF #:</td>
<td align="left" width="25%">'.$pif.'</td>
<td align="left" width="25%">Report Date:</td>
<td align="left" width="25%">'.$reportdate.'</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>Project Name:</td>
<td colspan=3>'.$projectname.'</td>
</tr>
<tr>
<td>Dial Plan:</td>
<td colspan=3>'.$dialplanname.'</td>
</tr>
<tr>
<td>Project Type:</td>
<td>'.$projecttype.'</td>
<td>List Size:</td>
<td>'.$total_num.'</td>
</tr>
<tr>
<td>Caller ID Name:</td>
<td>'.$callername.'</td>
<td>Project Start Date:</td>
<td>'.$projectstart.'</td>
</tr>
<tr>
<td>Caller ID Phone #:</td>
<td>'.$callerid.'</td>
<td>Project End Date:</td>
<td>'.$projectend.'</td>
</tr>

<tr>
<td>&nbsp;</td>
</tr>

<tr>
<td>Billing Name:</td>
<td colspan=3>'.$billname.'</td>
</tr>
<tr>
<td>Billing Address:</td>
<td colspan=3>'.$billaddress.'</td>
</tr>
<tr>
<td>Billing Phone:</td>
<td colspan=3>'.$billphone.'</td>
</tr>
<tr>
<td>Billing Email:</td>
<td colspan=3>'.$billemail.'</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan=2>Live Message Duration:</td>
<td colspan=2>'.$livemessageduration.'</td>
</tr>
<tr>
<td colspan=2>Live Message Duration (including Dial Plan IVR):</td>
<td colspan=2>'.$livemessagedurationwithivr.'</td>
</tr>
<tr>
<td colspan=2>Answer Machine Message Duration:</td>
<td colspan=2>'.$ammessageduration.'</td>
</tr>
<tr>
<td colspan=2>Cost per Live Message Delivered:</td>
<td colspan=2>'.$priceperlivemessage.'</td>
</tr>
<tr>
<td colspan=2>Cost per Answer Machine Message Delivered:</td>
<td colspan=2>'.$priceperammessage.'</td>
</tr>

<tr>
<td>&nbsp;</td>
</tr>

<tr>
<td style="font: bold 10pt Helvetica, Arial;">Contact Summary</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>Contact Rate:</td>
<td colspan=3>'.$connectpercentage.'</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>Count</td>
<td>Group %</td>
<td>Overall %</td>
</tr>
<tr>
<td>Live Message</td>
<td>'.$num_human.'</td>
<td>'.$humanindelivered.'</td>
<td>'.$humaninall.'</td>
</tr>
<tr>
<td>AM Message</td>
<td>'.$num_machine.'</td>
<td>'.$amindelivered.'</td>
<td>'.$aminall.'</td>
</tr>
<tr>
<td>Undeliverable</td>
<td>'.$num_noreach.'</td>
<td>&nbsp;</td>
<td>'.$undeliveredinall.'</td>
</tr>
<tr>
<td>TOTAL</td>
<td>'.$total_num.'</td>
<td>&nbsp;</td>
<td>100%</td>
</tr>


</tbody>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$fname = "reports/toClient_".$projectname."_".date("M_d").".pdf";
$mpdf->Output($fname,'F');


?>