<?php

include('WSModbusModule.php');

$module = new WSModbusModule('192.168.1.200', 4196);

for ($i=0; $i<100; $i++) {
  $module->getRelayStates();
  echo $module->relayStatesToJson() . "\n";
  sleep(1);
}//for

unset($module);
