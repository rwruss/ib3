<?php

echo '<script>//drawLoc = ['.$unitDat[1].', '.$unitDat[2].'];
newUnitDetail('.$postVals[1].', "rtPnl");
newMoveBox('.$postVals[1].', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
document.getElementById("Udtl_'.$postVals[1].'_name").innerHTML = "unitName";</script>Full and true unit information for this unit. <br>
Move Options:
<table>
  <tr><td onclick="move(7)">7</td><td onclick="move(8)">8</td><td onclick="move(9)">9</td></tr>
  <tr><td onclick="move(4)">4</td><td>x</td><td onclick="move(6)">6</td></tr>
  <tr><td onclick="move(1)">1</td><td onclick="move(2)">2</td><td onclick="move(3)">3</td></tr>
</table>
<span onclick="move(10)">Back Up</span>
<span onclick="move(11)">Clear</span>
<span onclick="orderMove()">Send Order</span>';



?>