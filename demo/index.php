<?php

require_once('../SG_iCal.php');

function dump_t($x) {
	echo "<pre>".print_r($x,true)."</pre>";
}
$ICS = "exdate.ics";
//echo dump_t(file_get_contents($ICS));

$ical = new SG_iCalReader($ICS);
$query = new SG_iCal_Query();

$evts = $ical->getEvents();
//$evts = $query->Between($ical,strtotime('20100901'),strtotime('20101131'));


$data = array();
foreach($evts as $id => $ev) {
	$jsEvt = array(
		"id" => ($id+1),
		"title" => $ev->getProperty('summary'),
		"start" => $ev->getStart(),
		"end"   => $ev->getEnd()-1,
		"allDay" => $ev->isWholeDay()
	);

	if (isset($ev->recurrence)) {
		$count = 0;
		$start = $ev->getStart();
		$freq = $ev->getFrequency();
		if ($freq->firstOccurrence() == $start)
			$data[] = $jsEvt;
		while (($next = $freq->nextOccurrence($start)) > 0 ) {
			if (!$next or $count >= 1000) break;
			$count++;
			$start = $next;
			$jsEvt["start"] = $start;
			$jsEvt["end"] = $start + $ev->getDuration()-1;

			$data[] = $jsEvt;
		}
	} else
		$data[] = $jsEvt;

}
//echo(date('Ymd\n',$data[0][start]));
//echo(date('Ymd\n',$data[1][start]));
//dump_t($data);

$events = "events:".json_encode($data).',';

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Fullcalendar iCal Loader</title>
<link rel="stylesheet" type="text/css" href="fullcalendar.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
<script type="text/javascript" src="fullcalendar.js"></script>
<script type="text/javascript">

	$(document).ready(function() {

		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},

			year: 2010,
			month: 9-1,

			// US Holidays
			//events: $.fullCalendar.gcalFeed('http://www.google.com/calendar/feeds/usa__en%40holiday.calendar.google.com/public/basic'),

			<?=$events ?>

			eventClick: function(event) {
				// opens events in a popup window
				window.open(event.url, 'gcalevent', 'width=700,height=600');
				return false;
			},

			loading: function(bool) {
				if (bool) {
					$('#loading').show();
				}else{
					$('#loading').hide();
				}
			}

		});

	});

</script>
<style type='text/css'>
	body div {
		text-align: center;
	}
	body {
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
	}
	div#loading {
		position: absolute;
		top: 5px;
		right: 5px;
	}
	div#calendar {
		width: 900px;
		margin: 0 auto;
	}
</style>
</head>
<body>
<div id="loading" style="display:none;">loading...</div>
<div id="calendar"></div>
</body>
</html>