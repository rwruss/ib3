<?php

include('c:/websites/ib3/public_html/slotFunctions.php');

$testFile = fopen('testslot.slt', 'r+b');
fseek($testFile, 200);
echo 'Test:<br>';
print_r(unpack('N*', fread($testFile, 40)));
echo '<p>';

fseek($testFile, 404*10-4);
fwrite($testFile, pack('i', 0));

$sendData = pack('i*', 1, 2000);
print_r(unpack('i*', $sendData));

$testSlot = new blockSlot(5, $testFile, 40);
echo '<p>Loaded data<br>';
print_r($testSlot->slotData);
echo '<p>Check for spaces';

$addTarget = sizeof($testSlot->slotData);

for ($i=1; $i<sizeof($testSlot->slotData); $i+=2) {
  if ($testSlot->slotData[$i] == 0) {
    $addTarget = $i;
    break;
  }
}

echo 'Add target is '.$addTarget.'<p>';
$testSlot->addItem($testFile, $sendData, $addTarget);
print_r($testSlot->slotData);

fclose($testFile);

?>
