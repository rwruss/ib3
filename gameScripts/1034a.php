<?php

date_default_timezone_set('America/Chicago');
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));
print_r($unitDat);
// Get resource slot
if ($unitDat[30] > 0) {
  include('./slotFunctions.php');
  $slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
  $rscSlot = new itemSlot($unitDat[30], $slotFile, 40);
  print_r($rscSlot->slotData);
} else {
  echo 'No rsc slot started';
}

echo '<script>
resetMove();

newUnitDetail('.$postVals[1].', "rtPnl");
newMoveBox('.$postVals[1].', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
document.getElementById("Udtl_'.$postVals[1].'_name").innerHTML = "unitName";
setUnitAction('.$postVals[1].', '.($actionPoints/1000).');
setUnitExp('.$postVals[1].', 0.5);
var equip = addDiv("unitEquip", "stdFloatDiv", document.getElementById("rtPnl"));
equip.innerHTML = "Equip this unit";
equip.addEventListener("click", function () {makeBox("equip", "1053,'.$postVals[1].'", 500, 500, 200, 50)} );

var orders = addDiv("unitOrders", "stdFloatDiv", document.getElementById("rtPnl"));
orders.innerHTML = "Unit Orders";
orders.addEventListener("click", function () {scrMod("1059,'.$postVals[1].'")});

var upgrades = addDiv("unitUpgrades", "stdFloatDiv", document.getElementById("rtPnl"));
upgrades.innerHTML = "Upgrade";
upgrades.addEventListener("click", function () {makeBox("upgrades", "1069,'.$postVals[1].'", 500, 500, 200, 50)});
</script>';

?>
