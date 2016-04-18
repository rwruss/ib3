<?php
date_default_timezone_set('America/Chicago');
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));

$_SESSION['selectedUnit'] = $unitID;

echo '<script>
resetMove();

newUnitDetail('.$unitID.', "rtPnl");
newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
setUnitAction('.$unitID.', '.($actionPoints/1000).');
setUnitExp('.$unitID.', 0.5);


var equip = addDiv("unitEquip", "stdFloatDiv", document.getElementById("rtPnl"));
equip.innerHTML = "Equip this unit";
equip.addEventListener("click", function () {makeBox("equip", "1053,'.$unitID.'", 500, 500, 200, 50)} );

var orders = addDiv("unitOrders", "stdFloatDiv", document.getElementById("rtPnl"));
orders.innerHTML = "Unit Orders";
orders.addEventListener("click", function () {makeBox("orders", "1059,'.$unitID.'", 500, 500, 200, 50)})
</script>';

?>
