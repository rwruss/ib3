<?php

require_once('./slotFunctions.php');
require_once('./unitClass.php');
require_once('./sysMessage.php');

$warFile = fopen($gamePath.'/wars.war', 'r+b');
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');


//Load war info
fseek($warFile, $postVals[1]*100);
$warDat = unpack('i*', fread($warFile, 100));
print_r($postVals);

// verify that player can make offer and get place
if ($warDat[5] == $pGameID) {
	$warPosition = 0;
	$opponent = $warDat[6];
}
elseif ($warDat[6] == $pGameID) {
	$warPosition = 1;
	$opponent = $warDat[5];
}
else exit("6211- Not able to take this action");

// Verify that the player has enouh resources in they treasury
$thisPlayer = loadPlayer($pGameID, $unitFile, 400);
$playerCity = loadUnit($thisPlayer->get('homeCity'), $unitFile, 400);
$rscList = new itemSlot($playerCity->get('carrySlot'), $slotFile, 40);
for ($i=1; $i<sizeof($rscList->slotData); $i+=2) {
	$rscStores[$rscList->slotData[$i]] = $rscList->slotData[$i+1];
}
print_r($rscStores);

$rscCheck = [];
for ($i=2; $i<sizeof($postVals); $i+=2) {
	if ($rscStores[$postVals[$i]] < $postVals[$i+1]) $rscCheck[] = $postVals[$i];
}

if (sizeof($rscCheck)>0) exit("not enough rsc");

// Record the offer and notify the other player
fseek($warFile, $postVals[1]*100+14+$warPosition*24);
fwrite($warFile, pack('i*', $postVals[2], $postVals[3], $postVals[4], $postVals[5], $postVals[6], $postVals[7]));


// Place the offered goods in "Escrow"
for ($i=2; $i<sizeof($postVals); $i+=2) {
	$playerCity->adjustRsc($postVals[$i], -$postVals[$i+1], $slotFile);
}

// Notify the target player
sendMessage($opponent, "Peace offer", "you have been offered peace", 0, 4, $unitFile, $slotFile);

fclose($warFile);
fclose($unitFile);
fclose($slotFile);

?>
