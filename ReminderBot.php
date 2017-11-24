<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/const.php';
require_once __DIR__ . '/DBConnection.php';

class ReminderBot {
	private $_conn;
	private $_httpClient;
	private $_sender;

	public function __construct() {
		$this->_conn = new DBConnection();
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

	public function deleteUser($userId) {
		$query = sprintf("UPDATE users SET del_flg = 1 WHERE luid = '%s'", $userId);
		$this->_conn->execQuery($query);
	}
}
