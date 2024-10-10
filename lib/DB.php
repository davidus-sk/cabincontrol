<?php

class DB {

  // connection object
  private $conn = null;

  public function __construct($databaseFile) {
    $this->conn = new SQLite3($databaseFile);
    
    if (!$this->conn) {
      die('Could not open database: ' . $this->conn->lastErrorMsg());
    }//if
  }//function

  public function __destruct() {
    $this->conn->close();
  }//function

    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
      
        if (!$stmt) {
            die('Prepare failed: ' . $this->conn->lastErrorMsg());
        }

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $result = $stmt->execute();
        if (!$result) {
            die('Execute failed: ' . $this->conn->lastErrorMsg());
        }

        return $result;
    }

    public function fetchAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetchAll();
    }

    public function fetchRow($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetchArray();
    }

  public function lastInsertId() {
    return $this->conn->lastInsertRowID();
  }//function

  public function createSchemas() {
    // create table for modules
    // modules are physical devices that control something or take input from something
    $this->conn->exec('CREATE TABLE IF NOT EXISTS modules (type INTEGER, ip TEXT, name TEXT)');

    // create table for relays
    // relays can be toggled via software or physical via switches
    $this->conn->exec('CREATE TABLE IF NOT EXISTS relays (module_id INTEGER, relay_number INTEGER, name TEXT)');
  }//function

  /**
   * Add module to DB
   *
   * @param $type
   * @param $ip
   * @param $name
   * @return void
   */
  public function addModule($type, $ip, $name) {
    $this->query('INSERT INTO modules (type, ip, name) VALUES (?, ?, ?)', [$type, $ip, $name]);

    // add relays for the module
    $this->addRelays($this->lastInsertId());
  }//function

  /**
   * Get all modules
   *
   * @return array
   */
  public function getModules() {
    return $this->fetchAll('SELECT * FROM modules');
  }//function

  /**
   * Add relays for the module
   * They are added automatically when module is added
   *
   * @param $moduleId
   * @param $count
   * @return void
   */
  public function addRelays($moduleId, $count = 8) {
    // create relays for a module
    for ($i = 1; $i <= $count; $i++) {
      $this->query('INSERT INTO relays (module_id, relay_number, name) VALUES (?, ?, ?)', [$moduleId, $i, "Relay {$i}"]);
    }//for
  }//function

  /**
   * Get all relays for a module
   *
   * @param $moduleId
   * @return array
   */
  public function getRelays($moduleId) {
    return $this->fetchAll('SELECT * FROM relays WHERE module_id = ?', [$moduleId]);
  }//function

  /**
   * Update relay's name
   *
   * @param $relayId
   * @param $name
   * @return void
   */
  public function updateRelay($relayId, $name) {
    $this->query('UPDATE relays SET name = ? WHERE rowid = ?', [$name, $relayId]);
  }//function
}//class
