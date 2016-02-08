<?php

include("./slotFunctions.php");
$cityID = $_SESSION['selectedItem'];

// Get city data
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $cityID*400);
$cityDat = unpack('i*', fread($unitFile, 400));

// Verify data is for a city

if ($cityDat[4] != 1) exit('Type error');
// Verify that player has credentials to view this info
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);


if ($approved != false) {
	$credLevel = $credList[$approved-1]*(-1);
} else {
	$credLevel = 0;
}

if ($cityDat[20] == $pGameID) $credLevel = 10;

// Determine the character's current rank in the city
fseek($unitFile, $postVals[1]*400);
$charDat = unpack('i*', fread($unitFile, 400));

// Get characters current position
print_r($charDat);
$positionList = unpack("i*", readSlotData($slotFile, $charDat[13], 40));
$posIndex = array_search($cityID*(-1), $positionList);

print_r($positionList);

$charPosition = 0;
if ($posIndex) $charPosition = $positionList[$posIndex+1];

$promotionList = array('Chief', 'SWBear', 'SHBear', 'Warrior', 'Councilor');
//echo '<script>alert("test");</script>';

echo '<script>
window["saveRank"] = function() {
	
	setVal = document.getElementById("rankSelect").value;
	//alert("saveRank " + setVal + " for char '.$postVals[1].'");
	scrMod("1033,'.$postVals[1].',"+setVal);
}
</script>
Promotion options for character #'.$postVals[1].', which has a current position of '.$charPosition.'<br>
Character details: - credintial '.$credLevel.'<br>';

echo '<hr><select id="rankSelect">';

for ($i=$credLevel; $i>0; $i--) {
	echo '<option value='.$i.'>Change to '.$promotionList[$i].' ('.$i.')</option>';
}
echo '</select>
<span onclick="saveRank()">Save Change</span>';

?>