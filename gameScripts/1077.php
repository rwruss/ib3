<?php

include("./slotFunctions.php");

// Load the character's traits
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$charDat = unpack('i*', fread($unitFile, $unitBlockSize));

$traitList = new itemSlot($charDat[15], $slotFile, 40);

// Create the character trait window
echo '<script>
useDeskTop.newPane("char'.$postVals[1].'traits");
thisDiv = useDeskTop.getPane("char'.$postVals[1].'traits");';

// Output the information to the window
$showTraits = array_filter($traitList->slotData);
$numTraits = sizeof($showTraits);
if ($numTraits > 0) {
	$traitDat = explode('<->', file_get_contents($scnPath.'/traits.desc'));
	for ($i=1; $i<sizeof($showTraits); $i++) {
		$thisTrait = explode('<-->', $showTraits[$i]);
		echo 'textBlob("", thisDiv, "'.$thisTrait[0].'")';
	}

} else {
}

echo '</script>';

fclose($slotFile);
fclose($unitFile);
?>
