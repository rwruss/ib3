<?php

include("./slotFunctions.php");

// look up player information to get slot IDS
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$unitBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Read the character slot for the player
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitList = array_filter(unpack("N*", readSlotData($slotFile, $playerDat[19], 40)));

print_r($unitList);
echo '<hr><script>';

foreach ($unitList as $unitID) {
	fseek($unitFile, $unitID*$unitBlockSize);
	$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));
	echo '
			newUnitDetail('.$unitID.', "militaryContent");
			//newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
			document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
			document.getElementById("Udtl_'.$unitID.'").addEventListener("click", function() {passClick("1034,'.$unitID.'", "rtPnl")});
			setUnitAction('.$unitID.', '.($actionPoints/1000).');
			setUnitExp('.$unitID.', 0.5);';
	//print_r($unitDat);
}
echo '</script>';

fclose($slotFile);
fclose($unitFile);
?>
