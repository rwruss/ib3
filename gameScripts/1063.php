<?php

include("./slotFunctions.php");

// Verify that player is authorized to view the resources at this city
$cityID = $_SESSION['selectedItem'];
echo 'Show projects for city '.$cityID.'<br>';
// Verify that the person giving the order has the proper credintials
$unitFile = fopen($gamePath.'/unitDat.dat' ,'rb');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

//print_r($cityDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);
echo 'Approved level '.$approved.'<br>';

if ($approved) {
	echo '<script>';
	$rscList = new blockSlot($cityDat[11], $slotFile, 40);
	for ($i=1; $i<sizeof($rscList->slotData); $i+=2) {
		echo 'thisRsc = resourceBox("'.$rscList->slotData[$i].'", "'.$rscList->slotData[$i+1].'", "rscSummaryContent");
		thisRsc.addEventListener("click", makeBox("rscDtl", "1064,'.$rscList->slotData[$i].'", 500, 500, 200, 50))';
		
	}
	echo '</script>';
}
fclose($slitFile);

?>