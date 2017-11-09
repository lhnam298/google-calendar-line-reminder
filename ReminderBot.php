<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__.'/const.php';

class ReminderBot {

	private $_httpClient;
	private $_sender;

	public function __construct() {
		$this->_httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(CHANNEL_ACCESS_TOKEN);
		$this->_sender = new \LINE\LINEBot($this->_httpClient, ['channelSecret' => CHANNEL_SECRET]);
	}

	public function pushMessage($userId = "", $textMessage = "") {
		if (empty($userId)) return;
		$responseMessage = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($textMessage);
		$response = $this->_sender->pushMessage($userId, $responseMessage);
		if (!$response->isSucceeded()) {
			$this->out($response->getHTTPStatus . ' ' . $response->getRawBody());
		}
	}

	public function replyMessage($replyToken = "", $textMessage = "") {
		if (empty($replyToken)) return;
		$responseMessage = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($textMessage);
		$response = $this->_sender->replyMessage($replyToken, $responseMessage);
		if (!$response->isSucceeded()) {
			$this->out($response->getHTTPStatus . ' ' . $response->getRawBody());
		}
	}
}
