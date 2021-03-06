<?php

include('./slotFunctions.php');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');

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
foreach ($useList as $plotID) {
	fseek($taskFile, $plotID*200);
	$plotDat = unpack('i*', fread($taskFile, 200));
	echo 'unitList.newUnit({unitType:"plot", unitID:'.$plotID.', unitName:"Plot #'.$plotID.'", actionPoints:'.$plotDat[6].', target:0, tResist:10});
		var thisSum = unitList.renderSum('.$plotID.', trg);
		confirmButton("Invite to Plot?", "1082,'.$plotID.'", thisSum.childNodes[3], "invite - '.$plotID.'");
		';
	/*
	echo 'var thisPlot = plotSummary({desc : "What\'d you do, man?", id:'.$useList[$i].'}, trg);
	confirmButton("Invite to Plot?", "1082,'.$useList[$i].'", thisPlot.childNodes[3], "invite - '.$i.'");';*/
}
echo '</script>';

fclose($unitFile);
fclose($slotFile);
fclose($taskFile);

?>
