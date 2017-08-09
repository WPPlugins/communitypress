<?php
if ($_POST['jes_send_type'] == 'iPhone' )
	{
		header("Content-Type: text/Calendar");
		header("Content-Disposition: inline; filename=event-".$_POST['jes-send-eventslug'].".ics");
		echo "BEGIN:VCALENDAR\n";
		echo "VERSION:2.0\n";
		echo "PRODID:-//Apple Inc.//iCal 4.0.2//EN\n";
	}
		else
	{
		if ($_POST['jes_send_type'] == 'Outlook' )
			{
				header("Content-Type: text/Calendar");	
				header("Content-Disposition: inline; filename=event-".$_POST['jes-send-eventslug'].".vcs");
				echo "BEGIN:VCALENDAR\n";
				echo "VERSION:2.0\n";
				echo "PRODID:-//Miscrosoft Corporation//NONSGML Microsoft//EN\n";
		}
		else
		{
			header( 'Status: 403 Forbidden' );
			header( 'HTTP/1.1 403 Forbidden' );
			exit();
		}
	}
echo "BEGIN:VEVENT\n";
$j_sd = $_POST['jes-send-unixsd'];
$j_ed = $_POST['jes-send-unixed'];
echo "UID:".date('Ymd',$j_sd).'T'.date('His',$j_sd)."-".rand()."\n"; // required by Outlok
echo "DTSTAMP:".date('Ymd').'T'.date('His')."\n"; // required by Outlook
echo "DTSTART:".date('Ymd',$j_sd).'T'.date('His',$j_sd)."\n";
echo "DTEND:".date('Ymd',$j_ed).'T'.date('His',$j_ed)."\n";
echo "LOCATION:".$_POST['jes-send-placed']."\n";
echo "SUMMARY:".$_POST['jes-send-eventname']."\n";
echo "DESCRIPTION: ".$_POST['jes-send-eventdesc'].", url: ".$_POST['jes-send-url']."\n";
echo "URL:".$_POST['jes-send-url']."\n";
echo "END:VEVENT\n";
echo "END:VCALENDAR\n";
?>