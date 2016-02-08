<?php
include("./slotFunctions.php");
$cityID = $_SESSION['selectedItem'];

// Get city data
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $cityID*400);
$cityDat = unpack('i*', fread($unitFile, 400));

// Verify data is for a city
if ($cityDat[12] != 1) //exit('Type error');

// Verify that player has credentials to view this info
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);
//print_r($credList);
if ($approved != false) {
	$credLevel = $credList[$approved-1]*(-1);
} else {
	$credLevel = 0;
}

echo 'Credintial Level:'.$credLevel.'<p>';

// Get list of characters for this town 
//// // Get list of characters for this town


// Get list of characters for this town 
//// // Get list of characters for this town
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$townLeaders = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[13], 40)));
print_r($townLeaders);
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
$rankList[10] = array();
for ($i=1; $i<=$numLeaders; $i++) {
	if ($townLeaders[$i] < 0) $trgRank = -$townLeaders[$i];
	else $rankList[$trgRank][] = $townLeaders[$i];
}
//print_r($rankList);

echo 'Characters present in the city ('.$cityID.').  Resource slot is '.$cityDat[10];

for ($rank=10; $rank>=0; $rank--) {
	echo 'Rank '.$rank.'<br>';
	foreach ($rankList[$rank] as $charID) {
		// get chararcter info
		fseek($unitFile, $charID*400);
		$charDat = unpack('i*', fread($unitFile, 400));
		echo '<div class="charSummary">
			<div class="charSummary cSAvatar"><img src="./common/av_'.$charDat[22].'.png"></div>
			<div class="charSummary cSDtlButton" onclick="makeBox(\'charDtl\', \'1002,'.$charID.'\', 500, 500, 200, 50)">Char '.$charID.' Detail</div>
			<div id="rankBox_'.$charID.'" class="charSummary cSRankButton" onclick="makeBox(\'charDtl\', \'1032,'.$charID.'\', 500, 500, 200, 50)">Rank: '.$rank.'</div>
			<div class="charSummary cSName">Name</div>
		</div>';
	}
	echo '<hr>';
}

?>