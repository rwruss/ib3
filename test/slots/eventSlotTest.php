<?php

include('c:/websites/ib3/public_html/slotFunctions.php');

$testFile = fopen('testslot.slt', 'r+b');
fseek($testFile, 404*10-4);
fwrite($testFile, pack('i', 0));

$sendData = pack('i*', 1, 2, 3, 4, 5, 6);
print_r(unpack('i*', $sendData));

$testSlot = new mapEffectSlot(5, $testFile, 404);

$addTarget = sizeof($testSlot->slotData);
echo 'Add target os '.$addTarget.'<br>';
$testSlot->addItem($testFile, $sendData, $addTarget);
print_r($testSlot->slotData);

fclose($testFile);

?>
