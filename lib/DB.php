<?php
/**
 * PHP class for sqlite3 database queries
 *
 * @author davidus.sk
 */
class DB {
	// connection object
	private $conn = null;

	/**
	 * Create object
	 */
	public function __construct($databaseFile) {
		$this->conn = new SQLite3($databaseFile);

		if (!$this->conn) {
			die('Could not open database: ' . $this->conn->lastErrorMsg());
		}//if
	}//function
 
	/**
	 * Destroy the class
	 */
	public function __destruct() {
		$this->conn->close();
	}//function

	/**
	 * Prepare and execute query
	 *
	 * @param $sql
	 * @param $params
	 * @return object
	 */
	public function query($sql, $params = []) {
		$stmt = $this->conn->prepare($sql);

		if (!$stmt) {
			die('Prepare failed: ' . $this->conn->lastErrorMsg());
		}//if

		foreach ($params as $key => $value) {
			$stmt->bindValue($key, $value);
		}//foreach

		$result = $stmt->execute();
		
		if (!$result) {
			die('Execute failed: ' . $this->conn->lastErrorMsg());
		}//if

		return $result;
	}//function

	/**
	 * Fetch all rows from a query
	 *
	 * @param $sql
	 * @param $params
	 * @return array
	 */
	public function fetchAll($sql, $params = []) {
		$result = $this->query($sql, $params);
		return $result->fetchAll();
	}//function

	/**
	 * Fetch row from a query
	 *
	 * @param $sql
	 * @param $params
	 * @return array
	 */
	public function fetchRow($sql, $params = []) {
		$result = $this->query($sql, $params);
		return $result->fetchArray();
	}//function

	/**
	 * Get last insert ID
	 *
	 * @return int
	 */
	public function lastInsertId() {
		return $this->conn->lastInsertRowID();
	}//function

	/**
	 * Create needed schemas
	 *
	 * @return void
	 */
	public function createSchemas() {
		// create table for modules
		// modules are physical devices that control something or take input from something
		$this->conn->exec('CREATE TABLE IF NOT EXISTS modules (type INTEGER, ip TEXT, name TEXT)');

		// create table for relays
		// relays can be toggled via software or physical via switches
		$this->conn->exec('CREATE TABLE IF NOT EXISTS relays (module_id INTEGER, relay_number INTEGER, name TEXT)');
	}//function
}//class
