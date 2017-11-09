<?php
require_once __DIR__ . '/ReminderBot.php';

$json_string = file_get_contents('php://input');
$jsonObj = json_decode($json_string);
$events = $jsonObj->{'events'}[0];
$reminder = new ReminderBot();

switch ($events->{'type'}) {
	case 'follow':
		$replyToken = $events->{'replyToken'};
		$userId = $events->{'source'}->{'userId'};
		$oauth_url = OAUTH_URL . "/?luid=" . $userId;
		$reminder->replyMessage($replyToken, $oauth_url);
		break;

	case 'unfollow':
		break;

	default:
		break;
}
