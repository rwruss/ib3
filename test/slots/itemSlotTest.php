<?php

include('c:/websites/ib3/public_html/slotFunctions.php');

$testFile = fopen('itemSlot.slt', 'r+b');
fseek($testFile, 40*10-4);
fwrite($testFile, pack('i', 0));



$testSlot = new itemSlot(5, $testFile, 40);
print_r($testSlot->slotData);
echo '<p>';
//$testSlot->addItem(7, $testFile);

$deleteLoc = array_search(7, $testSlot->slotData);
if ($deleteLoc) $testSlot->deleteItem($deleteLoc, $testFile);

/*
$addTarget = sizeof($testSlot->slotData);
echo 'Add target os '.$addTarget.'<br>';
$testSlot->addItem($testFile, $sendData, $addTarget);
print_r($testSlot->slotData);
*/
$checkSlot = new itemSlot(5, $testFile, 40);
print_r($checkSlot->slotData);
fclose($testFile);

?>
