	<?php

$cityID = $_SESSION['selectedItem'];

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = fread($unitFile, $unitBlockSize);
$cityInfo = unpack('i*', $cityDat);

echo 'Resource slot is '.$cityInfo[10];

if ($cityInfo[10] == 0) {
	echo 'There are no resource producing buildings for this city.  Resources are produced at a base level by the settlement\'s population foraging.
	<div style="border:11px solid black;" onclick="setClick([1023], \'crosshair\', \'cityProdContent\');">Est. RSC Point</div>
	<div style="border:11px solid black;" onclick="makeBox(\'forageOpt\', 1024, 500, 500, 200, 50)">Start Foraging</div>';
}

?>
