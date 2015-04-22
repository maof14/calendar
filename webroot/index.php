<?php

include __DIR__."../../src/config.php";

$title = "Kalender by maof14";
$calendar = new CCalendar();

?>

<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?=$title?></title>
		<link rel="stylesheet" href="css/style.css" />
		</head>
	<body>
		<div id="container">
		<h1><a href="/calendar/webroot">Calendar</a></h1>
		<h2><?=$calendar->getMonthName()?> <?=$calendar->getYear()?></h2>
		<p><?=$calendar->getNavigation()?>
		<?=$calendar->getCalendarTable()?>
		</div>
	</body>
</html>