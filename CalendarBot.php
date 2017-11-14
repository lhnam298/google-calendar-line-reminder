<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/const.php';
require_once __DIR__ . '/DBConnection.php';

class CalendarBot {
	private $_client;
	private $_conn;
	private $_service;

	public function __construct($uri = "", $state = "") {
		date_default_timezone_set('Asia/Tokyo');
		$this->_conn = new DBConnection();
		$this->_client = new Google_Client();
		$this->_client->setApplicationName(APPLICATION_NAME);
		$this->_client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
		$this->_client->setAuthConfigFile(CLIENT_SECRET_PATH);
		$this->_client->setAccessType('offline');
		$this->_client->setApprovalPrompt('force');
		if (!empty($uri)) $this->_client->setRedirectUri($uri);
		if (!empty($state)) $this->_client->setState($state);
	}

	public function getAuthUrl() {
		return $this->_client->createAuthUrl();
	}

	public function authenticate($code = "", $luid = "") {
		$this->_client->authenticate($code);
		$access_token = json_encode($this->_client->getAccessToken());
		$query = "INSERT INTO users (luid, access_token) VALUES ('$luid', '$access_token') ON DUPLICATE KEY UPDATE access_token='$access_token'";
		$this->_conn->execQuery($query);
	}

	public function setAccessToken($luid = "", $access_token = "") {
		$this->_client->setAccessToken(json_decode($access_token, true));
		if ($this->_client->isAccessTokenExpired()) {
			$this->_client->fetchAccessTokenWithRefreshToken($this->_client->getRefreshToken());
			$access_token = json_encode($this->_client->getAccessToken());
			$query = sprintf("UPDATE users SET access_token = '%s' WHERE luid = '%s'", $access_token, $luid);
			$this->_conn->execQuery($query);
		}
	}

	public function getComingEvent() {
		$this->_service = new Google_Service_Calendar($this->_client);
		$calendarId = 'primary';
		$currentTime = time();
		$optParams = array(
			'maxResults' => 10,
			'orderBy' => 'startTime',
			'singleEvents' => TRUE,
			'timeMin' => date('c', $currentTime),
			'timeMax' => date('c', $currentTime + REMINDER_BEFORE_TIME)
		);
		$results = $this->_service->events->listEvents($calendarId, $optParams);

		$response = array();
		if (count($results->getItems()) == 0) {
			print "No upcoming events found.\n";
		} else {
  			print "Upcoming events:\n";
			foreach ($results->getItems() as $event) {
				$start = (isset($event->start->dateTime) && !empty($event->start->dateTime)) ? $event->start->dateTime : $event->start->date;
				if (empty($start)) continue;
				if (strtotime($start) > $currentTime + REMINDER_BEFORE_TIME || strtotime($start) <= $currentTime) continue;
				//printf("%s (%s)\n", $event->getSummary(), $start);
				$time = date('H:i', strtotime($start));
				$summary = $event->getSummary();
				$location = $event->getLocation();
				$response[] = $time . ' ' . $summary . PHP_EOL . $location;
			}
		}
		return $response;
	}

}
