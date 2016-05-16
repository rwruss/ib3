<?php

include('./slotFunctions.php');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');


// Get list of known plots
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$pDat = unpack('i*', fread($unitFile, 200));

// Show list that char can be invited to
echo 'Read slot '.$pDat[20];
$plotList = new itemSlot($pDat[20], $slotFile, 40);
print_r($plotList->slotData);
$useList = array_filter($plotList->slotData);
print_r($useList);
echo '<script>
trg = document.getElementById("plotInviteContent");';
for ($i=1; $i<=sizeof($useList); $i++) {

	echo 'unitList.newUnit({unitType:"plot", unitID:'.$useList[$i].', actionPoints:500, target:20000});
		unitList.renderSum('.$useList[$i].', trg);';
	/*
	echo 'var thisPlot = plotSummary({desc : "What\'d you do, man?", id:'.$useList[$i].'}, trg);
	confirmButton("Invite to Plot?", "1082,'.$useList[$i].'", thisPlot.childNodes[3], "invite - '.$i.'");';*/
}
echo '</script>';

fclose($unitFile);
fclose($slotFile);

?>
