<?php

require_once('./slotFunctions.php');
require_once('./objectClass.php');

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$objFile = fopen($gamePath.'/objects.dat', 'rb');

$thisBusiness = loadObject($pGameId, $objFile, 400);
if ($thisBusiness->get('ownedObjects') > 0) {
	$ownedObjects = new itemSlot($thisBusiness->get('ownedObjects'), $slotFile, 40);
	
	for ($i=1; $i<sizeof($ownedObjects->slotData); $i++) {
		$thisObject = loadObject($ownedObjects->slotData[$i], $objFile, 400);
		echo 'Object '.$ownedObjects->slotData[$i].'<br>';
	}
} else {
	echo 'You do not own anything yet - start buying!';
}

fclose($slotFile);
fclose($objFile);

?>