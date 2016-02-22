<?php
include('./taskFunctions.php');
include("./slotFunctions.php");
echo 'Start construction of building '.$postVals[1].' at city '.$postVals[2].' from player '.$pGameID.'<br>';

// Verify that player has a character that can control this place

//// // Load city Dat
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[2]*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

//// // Get list of characters for this town
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$townLeaders = unpack("i*", readSlotData($slotFile, $cityDat[13], 40));
echo 'Town leader slot: '.$cityDat[13].'<br>';
$numLeaders = sizeof($townLeaders);
$rankList[0] = array();
$rankList[1] = array();
$rankList[2] = array();
$rankList[3] = array();
$rankList[4] = array();
$rankList[5] = array();
$rankList[6] = array();
$rankList[7] = array();
$rankList[8] = array();
$rankList[9] = array();
print_r($townLeaders);
for ($i=1; $i<=$numLeaders; $i++) {
	if ($townLeaders[$i] < 0) $trgRank = -$townLeaders[$i];
	else $rankList[$trgRank][] = $townLeaders[$i];
}
echo '<hr>';
print_r($rankList);
$approved = false;
for ($rank=4; $rank<10; $rank++) {
	if (sizeof($rankList[$rank]) > 0) {
		foreach ($rankList[$rank] as $charID) {
			fseek($unitFile, $charID*$defaultBlockSize);
			$charDat = unpack('i*', fread($unitFile, $unitBlockSize));
			if ($charDat[6]==$pGameID) {
				// The character is approved to give this order
				$approved = true;
				break 2;
			} else {
				// The character is not approved to give this order
			}
		}
	}
}


$approved = false;
if ($approved) {
	// load and show current build options
	$buildTree = file_get_contents($gamePath.'/buildings.desc');
	$bldgList = explode('<-->', $buildTree);
	$listSize = sizeof($bldgList)/7;

	// Read material requirements for the building
	$rscReq = explode(",", $bldgList[$postVals[1]*7+4]); //<< - insert offset for resource requirements
	$numRsc = sizeof($rscReq)/2;
	$rscDat = pack('i*', 1, time(), $bldgList[$postVals[1]*7+2]); //<< - insert offset for building points requirements
	for ($i=0; $i< $numRsc; $i++) {
		$rscDat .= pack('i*', $rscReq[$i*2], $rscReq[$i*2+1]);
	}

	// approved - start the task
	$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
	$taskIndex = fopen($gamePath.'/tasks.tix', 'r+b');
	$newTask = createTask($taskFile, $taskIndex, 0, $rscDat, $gamePath, $slotFile);

	// Record the task number in the list of buildings in progress for the city
	fclose($taskFiel);
	fclose($taskIndex);
} else {
	echo 'Not approved';
}

fclose($slotFile);
fclose($unitFile);

?>
