<?php

date_default_timezone_set('America/Chicago');
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));

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
orders.addEventListener("click", function () {makeBox("orders", "1059,'.$postVals[1].'", 500, 500, 200, 50)});

var upgrades = addDiv("unitUpgrades", "stdFloatDiv", document.getElementById("rtPnl"));
upgrades.innerHTML = "Upgrade";
upgrades.addEventListener("click", function () {makeBox("upgrades", "1069,'.$postVals[1].'", 500, 500, 200, 50)});
</script>';

/*
Full and true unit information for this unit. <br>
Last Update Time: '.date('d/m/y h:i:s', $unitDat[27]).'<br>'.
time().' - '.$unitDat[27].' = '.(floor((time()-$unitDat[27])/1)).'
Now: '.date('d/m/y h:i:s', time()).'
Move Options:
<table>
  <tr><td onclick="move(7)">7</td><td onclick="move(8)">8</td><td onclick="move(9)">9</td></tr>
  <tr><td onclick="move(4)">4</td><td>x</td><td onclick="move(6)">6</td></tr>
  <tr><td onclick="move(1)">1</td><td onclick="move(2)">2</td><td onclick="move(3)">3</td></tr>
</table>
<span onclick="move(10)">Back Up</span>
<span onclick="move(11)">Clear</span>
<span onclick="orderMove()">Send Order</span>

Object Type 6';
*/
?>
