<?php
date_default_timezone_set('America/Chicago');
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));

echo '<script>
resetMove();

newUnitDetail('.$unitID.', "rtPnl");
newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
setUnitAction('.$unitID.', '.($actionPoints/1000).');
setUnitExp('.$unitID.', 0.5);
</script>Full and true unit information for this unit. <br>
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

?>
