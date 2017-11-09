<?php
require_once __DIR__.'/const.php';

class DBConnection {
	private $_conn;

	public function __construct() {
		$this->createDatabaseConnection();	
	}

	public function createDatabaseConnection() {
		$this->_conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		if ($this->_conn->connect_error) {
			die("Connection failed: " . $this->_conn->connect_error);
		}
	}

	public function execQuery($query = "") {
		if (empty($query)) return;
		$result = @$this->_conn->query($query);

		if (!$result) {
			die("Query failed: " . $this->_conn->error);
		}

		$response = array();
		while ($row = @mysqli_fetch_assoc($result)) {
			$response[] = $row;
		}
		return $response;
	}

	public function closeDatabaseConnection() {
		mysqli_close($this->_conn);
	}
}
