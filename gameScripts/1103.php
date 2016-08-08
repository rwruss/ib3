<?php

include("./slotFunctions.php");
include('./unitClass.php');
//echo 'Gather from a point with a unit';
echo '<script>
useDeskTop.newPane("rscWork");
var thisDiv = useDeskTop.getPane("rscWork");
console.log("add to " + thisDiv);';


$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Get specifics for gather point
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$farmDat = unpack('i*', fread($unitFile, 400));

// Get list of units nearby
$playerObj = new player($pGameID, $unitFile, 400);
$unitList = new itemSlot($playerObj->get("unitSlot"), $slotFile, 40);
//print_r($unitList->slotData);
for ($i=1; $i<=sizeof($unitList->slotData); $i++) {
  if ($unitList->slotData[$i] > 0) {
    fseek($unitFile, $unitList->slotData[$i]*$defaultBlockSize);
    $unitDat = unpack('i*', fread($unitFile, 400));

    echo 'unitList.newUnit({unitType:"warband", unitID:'.$unitList->slotData[$i].', unitName:"'.$unitList->slotData[$i].'", actionPoints:0});
    var objContain = addDiv("", "selectContain", thisDiv);
    unitList.renderSum('.$unitList->slotData[$i].', objContain);
    var newButton = optionButton("", objContain, "Gather Here");
    newButton.objectID = "'.$postVals[1].','.$unitList->slotData[$i].',1";
    newButton.addEventListener("click", function () {scrMod("1104,"+this.objectID)});';
  }
}
echo '</script>';
//$unitList = array_filter(unpack("i*", readSlotData($slotFile, $playerObj->get("unitSlot"), 40)));
fclose($unitFile);
fclose($slotFile);

?>
