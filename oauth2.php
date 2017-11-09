<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/CalendarBot.php';
require_once __DIR__.'/const.php';

$calendar_bot = new CalendarBot(OAUTH_URL, $_GET['luid']);

if (! isset($_GET['code'])) {
	$auth_url = $calendar_bot->getAuthUrl();
	header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
	$calendar_bot->authenticate($_GET['code'], $_GET['state']);
	$redirect_uri = OAUTH_SUCCESS_URL; 
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
