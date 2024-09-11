<?php

class WSModbusModule {
  // module's IP address
  private $ip;

  // module's port to connect to
  private $port;

  // TCP/IP socket
  private $socket;

  // state of relays
  private $relayStates = 0;

  // state of inputs
  private $inputStates = 0;

  public function __construct($ip, $port) {
    $this->ip = $ip;
    $this->port = $port;

    $this->createSocketConnection();
  }//func

  public function __destruct() {
    $this->closeSocketConnection();
  }//func

  private function createSocketConnection() {
    $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    if (!$this->socket) {
      throw new Exception("Could not create socket: " . socket_strerror(socket_last_error()));
    }//if

    if (!socket_connect($this->socket, $this->ip, $this->port)) {
      throw new Exception("Could not connect to server: " . socket_strerror(socket_last_error()));
    }//if
  }//func

  private function closeSocketConnection()
  {
    if ($this->socket) {
      socket_close($this->socket);
    }//if
  }//func

  private function getRelayStates()
  {
    @socket_write($this->socket, "\x01\x01\x00\x00\x00\x08\x3D\xCC\n");
    $data = @socket_read($socket, 2048);

    if ($data && strlen($data) == 6) {
      $this->relayStates = ord($data[3]);
    }//if
  }//func

  public function getRelayState($relayNumber)
  {
    $this->getRelayStates();

    return ($this->relayStates >> ($relayNumber - 1)) & 1;
  }//func

  public function setRelayState($relayNumber, $state)
  {
    @socket_write($this->socket, "\x01\x05\x00" . char($relayNumber - 1) . " FF 00\n");
    
    $this->getRelayStates();
  }
}//class
