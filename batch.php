<?php
require_once __DIR__.'/CalendarBot.php';
require_once __DIR__.'/ReminderBot.php';

$reminder_bot = new ReminderBot();
$conn = new DBConnection();

$query = "SELECT luid, access_token FROM users WHERE del_flg = 0";
$results = $conn->execQuery($query);

foreach ($results as $result) {
	$calendar_bot = new CalendarBot();
	$calendar_bot->setAccessToken($result['luid'], $result['access_token']);
	$events = $calendar_bot->getComingEvent();
	foreach ($events as $event) {
		$reminder_bot->pushMessage($result['luid'], $event);
	}
	unset($calendar_bot);
}
