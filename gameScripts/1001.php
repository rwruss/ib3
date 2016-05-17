<?php

include("./slotFunctions.php");

// look up player information to get slot IDS
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Read the character slot for the player
//echo 'Check slot '.$playerDat[19].'<br>';
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitList = array_filter(unpack("i*", readSlotData($slotFile, $playerDat[19], 40)));

//print_r($unitList);
echo '<script>
var thisDiv = document.getElementById("charListContent");';

foreach ($unitList as $unitID) {
	fseek($unitFile, $unitID*$defaultBlockSize);
	$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));
	$actionPoints = 150;
	echo '

		unitList.newUnit({unitType:"character", unitID:'.$unitID.', unitName:"char name", actionPoints:1000, strength:75});
		unitList.renderSum('.$unitID.', thisDiv);';
	/*
	echo '
			newUnitDetail('.$unitID.', "charListContent");
			//newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
			document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "Char '.$unitID.'";
			document.getElementById("Udtl_'.$unitID.'").addEventListener("click", function() {passClick("1074,'.$unitID.'", "rtPnl")});
			setUnitAction('.$unitID.', '.($actionPoints/1000).');
			setUnitExp('.$unitID.', 0.5);';
	//print_r($unitDat);*/
}
echo '</script>';

fclose($slotFile);
fclose($unitFile);
?>
