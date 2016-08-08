	<?php
include("./slotFunctions.php");
$cityID = $_SESSION['selectedItem'];

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = fread($unitFile, $unitBlockSize);
$cityInfo = unpack('i*', $cityDat);



if ($cityInfo[10] == 0) {
	echo 'There are no resource producing buildings for this city.  Resources are produced at a base level by the settlement\'s population foraging.';
} else {
	$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
	$farmList = new itemSlot($cityInfo[10], $slotFile, 40);
	fclose($slotFile);

	echo '<script>
	var thisDiv = useDeskTop.getPane("cityProd");
	console.log("add to " + thisDiv);';

	for ($i=1; $i<=sizeof($farmList->slotData); $i++) {
		if ($farmList->slotData[$i] > 0) {
			fseek($unitFile, $farmList->slotData[$i]*$defaultBlockSize);
			$farmDat = unpack('i*', fread($unitFile, 400));
			echo 'unitList.newUnit({unitType:"building", unitID:'.$farmList->slotData[$i].', unitName:"'.$farmList->slotData[$i].' - '.$farmDat[10].'", actionPoints:0});
				var objContain = addDiv("", "selectContain", thisDiv);
  			unitList.renderSum('.$farmList->slotData[$i].', objContain);
  			var newButton = optionButton("", objContain, "Gather Here");
  			newButton.objectID = "'.$farmList->slotData[$i].',1";
  			newButton.addEventListener("click", function () {scrMod("1103,"+this.objectID)});
			';
			/*
			echo '
			unitList.newUnit({unitType:"building", unitID:'.$farmList->slotData[$i].', unitName:"'.$farmList->slotData[$i].' - '.$farmDat[10].'", actionPoints:0});
			var objContain = addDiv("", "selectContain", thisDiv);
  			unitList.renderSum('.$farmList->slotData[$i].', objContain);
  			var newButton = optionButton("", objContain, "25%");
  			newButton.objectID = "'.$farmList->slotData[$i].',1";
  			newButton.addEventListener("click", function () {scrMod("1102,"+this.objectID)});
  			var newButton = optionButton("", objContain, "50%");
  			newButton.objectID = "'.$farmList->slotData[$i].',2";
  			newButton.addEventListener("click", function () {scrMod("1102,"+this.objectID)});
  			var newButton = optionButton("", objContain, "100%");
  			newButton.objectID = "'.$farmList->slotData[$i].',3";
  			newButton.addEventListener("click", function () {scrMod("1102,"+this.objectID)});';*/
		}
	}
echo '</script>';
}
echo '<div style="border:11px solid black;" onclick="setClick([1023], \'crosshair\', \'cityProdContent\');">Est. RSC Point</div>
<div style="border:11px solid black;" onclick="makeBox(\'forageOpt\', 1024, 500, 500, 200, 50)">Start Foraging</div>'

?>
