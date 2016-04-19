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
	$rscList = new itemSlot($cityDat[11], $slotFile, 40);
	for ($i=1; $i<sizeof($rscList->slotData); $i+=2) {
		if ($rscList->slotData[$i] == $postVals[1]) {
			echo 'Resource '.$rscList->slotData[$i].' Qty is '.$rscList->slotData[$i+1].'<br>'

			if ($cityData[29] > 0) {
				echo 'Options for adding to city store<br>

				<script>
				addDiv("jobOptions", "cButtons", document.getElementById("rscDtlContent"));

				var opt1 = optionButton("", "jobOptions", "10%");
				opt1.addEventListener("click", function() {scrMod("1065,'.$postVals[1].',1")});

				var opt2 = optionButton("", "jobOptions", "25%");
				opt2.addEventListener("click", function() {scrMod("1065,'.$postVals[1].',2")});

				var opt3 = optionButton("", "jobOptions", "50%");
				opt3.addEventListener("click", function() {scrMod("1065,'.$postVals[1].',3")});

				var opt4 = optionButton("", "jobOptions", "100%");
				opt4.addEventListener("click", function() {scrMod("1065,'.$postVals[1].',4")});
				</script>

				';
			}
		}
	}
}

fclose($slitFile);

?>
