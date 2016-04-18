<?php

// 1065 - transfer resources from a sub-city to a group city

include("./slotFunctions.php");
include("./cityClass.php");

$cityID = $_SESSION['selectedItem'];
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

$fromCity = new city($cityID, $unitFile);
$fromCity->slotFile($slotFile);

$toCity = new city($fromCity->cityData[29], $unitFile);
$toCity->slotFile($slotFile);

$transferOpt = [0, .10, .25, .50, 1.0];

$transferAmt = $fromCity->rscAmt($postVals[1])*$transferOpt[$postVals[2]];
if ($transferAmt > 0) {
	$fromCity->addRsc($postVals[1], -$transferAmt);
	$toCity->addRsc($postVals[1], $transferAmt);
}

echo '<script>messageBox("Transfered '.$ransferAmt.' resources.", document.getElementByType("Body"))</script>';

fclose($slotFile);
fclose($unitFile);


?>