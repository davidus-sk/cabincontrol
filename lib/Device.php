<?php
/**
 * PHP class for managing devices and relays
 *
 * @author davidus.sk
 */
class Device {
  // database connection object
  private $db = null;

  /**
   * Create object
   */
  public function __construct() {
    $this->db = new DB($databaseFile);
  }//function

  /**
   * Destroy the class
   */
  public function __destruct() {
    unset($this->db);
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
    $this->db->query('INSERT INTO modules (type, ip, name) VALUES (?, ?, ?)', [$type, $ip, $name]);

    // add relays for the module
    $this->addRelays($this->db->lastInsertId());
  }//function

  /**
   * Get all modules
   *
   * @return array
   */
  public function getModules() {
    return $this->db->fetchAll('SELECT * FROM modules');
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
      $this->db->query('INSERT INTO relays (module_id, relay_number, name) VALUES (?, ?, ?)', [$moduleId, $i, "Relay {$i}"]);
    }//for
  }//function

  /**
   * Get all relays for a module
   *
   * @param $moduleId
   * @return array
   */
  public function getRelays($moduleId) {
    return $this->db->fetchAll('SELECT * FROM relays WHERE module_id = ?', [$moduleId]);
  }//function

  /**
   * Update relay's name
   *
   * @param $relayId
   * @param $name
   * @return void
   */
  public function updateRelay($relayId, $name) {
    $this->db->query('UPDATE relays SET name = ? WHERE rowid = ?', [$name, $relayId]);
  }//function
}//class