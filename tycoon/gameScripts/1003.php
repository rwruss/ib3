<?php

require_once('./slotFunctions.php');
require_once('./objectClass.php');

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$objFile = fopen($gamePath.'/objects.dat', 'rb');

$thisObj = loadObject($postVals[1], $objFile, 400);

// confirm that the player owns this object
if ($thisObj->get('owner') == $pGameID) {
	include('./objects/obj_'.$thisObj->get('oType').'.php');
} else {
	echo 'You do not own this object';
}

fclose($objFile);
fclose($slotFile);

?>