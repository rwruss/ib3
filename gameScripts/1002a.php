<?php

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Character interface for owner of the char
echo 'Details for character named '.$charDat[19].' '.$charDat[20].'<br>';

// Show current task
echo 'Current task is '.$charDat[11].'<br>';

// Show current locaiton
echo 'Current location is '.$charDat[1].', '.$charDat[2].' in location '.$charDat[12].'<br>';

// Show Positions Held (Slot 13)
$positionList = array_filter(unpack("i*", readSlotData($slotFile, $charDat[13], 40)));

// Show titles held (Slot 14)
$titleList = array_filter(unpack("i*", readSlotData($slotFile, $charDat[14], 40)));
if (sizeof($titleList)>0) {
	foreach ($titleList as $titleID) {
		echo 'Title: '.$titleID.'<br>';
	}
} else {
	echo 'No titles<br>';
}

// Show traits (Slot 15)
$traitList = array_filter(unpack("i*", readSlotData($slotFile, $charDat[15], 40)));

if (sizeof($traitList) >0) {
	// Read contents of trait file and split up descriptions
	$traitDesc = file_get_contents('../games/common/traits.desc');
	$traitItems = explode('<-->', $traitDesc);
	echo '<ul> Traits ('.sizeof($traitList).')';
	foreach($traitList as $traitID) {
		$traitAffects = explode(',', $traitItems[$traitID*2+1]);
		echo '<li>'.$traitItems[$traitID*2];
		echo '<ul>';
		foreach ($traitAffects as $desc) {
			echo '<li>'.$desc;
		}
		echo '</ul>';
	}
	echo '</ul>';
} else {
	echo 'No traits ('.sizeof($traitList).')<br>';
}

// Race, Culture, Public Religion and Private Religion (Slots 16, 33, 34)

// Vassal List (Slot 28)
if ($charDat[28] > 0) {
	$LVList = unpack("i*", readSlotData($slotFile, $charDat[28], 40));
	echo 'Lord: '.$LVList[1].' with a title of '.$LVList[2].'<br>';
	$vassalList = array_filter(array_slice($LVList, 2));
	$numVassals = sizeof($vassalList);
	if ($numVassals > 0) {
		for ($i=0; $i<$numVassals; $i++) {
			if ($vassalList[$i] < 0) {
				echo 'Title: '.$vassalList[$i].'<br>';
			} else echo 'Vassal: '.$vassalList[$i].'<br>';
		}
	}
} else {
	echo 'No lord or vassals<br>';
}


fclose($slotFile);

// Go to character
echo '<span onclick="mapGoto('.$charDat[1].', '.$charDat[2].')">Goto this Character</span><br>';

// Read orders file
$orderDat = explode('<-->', file_get_contents('../games/common/charOrders.desc'));
$numOrders = sizeof($orderDat)/2;
print_r($orderDat);


echo 'Issue orders to the character ('.$numOrders.'):<br>
<select id="newTask"  onchange="getDescription(\'taskDescBox\', 1036, \'newTask\')">';

for ($i=1; $i<$numOrders; $i++) {
	echo '<option value='.$orderDat[$i*2].'>'.$orderDat[$i*2+1].'</option>';
}
echo '</select>
<div id="taskDescBox"></div>
<div onclick="sendValue(\'newTask\', 1035)";>Give Order</div>';


?>