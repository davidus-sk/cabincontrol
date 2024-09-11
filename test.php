<?php

include('WSModbusModule.php');

$module = new WSModbusModule('192.168.1.200', 4196);

for ($i=1; $i<=8; $i++) {
  $module->setRelayState($i, true);
  sleep(1);
}

for ($i=1; $i<=8; $i++) {
  $module->setRelayState($i, false);
  sleep(1);
}

$module->setModeForAll(WSModbusModule::MODE_FLIP);

unset($module);
