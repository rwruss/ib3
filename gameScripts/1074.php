<?php
$_SESSION['selectedUnit'] = $postVals[1];
echo 'Details for character '.$postVals[1].' of type '.$postVals[10].'

<script>
resetMove();

newUnitDetail('.$postVals[1].', "rtPnl");
newMoveBox('.$postVals[1].', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
document.getElementById("Udtl_'.$postVals[1].'_name").innerHTML = "unitName";
setUnitAction('.$postVals[1].', '.($actionPoints/1000).');
setUnitExp('.$postVals[1].', 0.5);

var plot = addDiv("unitEquip", "stdFloatDiv", document.getElementById("rtPnl"));
plot.innerHTML = "Character Plots";
plot.addEventListener("click", function () {makeBox("plot", "1075,'.$postVals[1].'", 500, 500, 200, 50)});

var skills = addDiv("unitEquip", "stdFloatDiv", document.getElementById("rtPnl"));
skills.innerHTML = "Character Skills";
skills.addEventListener("click", function () {makeBox("skills", "1076,'.$postVals[1].'", 500, 500, 200, 50)});

var traits = addDiv("unitEquip", "stdFloatDiv", document.getElementById("rtPnl"));
traits.innerHTML = "Character Traits";
traits.addEventListener("click", function () {makeBox("traits", "1077,'.$postVals[1].'", 500, 500, 200, 50)});
</script>';

?>