<?php

//print_r($thisChar->attrList);
$actionPoints = min(1000, $thisChar->get("energy") + floor((time()-$thisChar->get("updateTime"))/1));

echo 'Details for character '.$postVals[1].' of type '.$thisChar->get("uType").'

<script>
resetMove();

newUnitDetail('.$postVals[1].', "rtPnl");
newMoveBox('.$postVals[1].', '.$thisChar->get("xLoc").', '.$thisChar->get("yLoc").', "rtPnl");
document.getElementById("Udtl_'.$postVals[1].'_name").innerHTML = "unitName";
setUnitAction('.$postVals[1].', '.($actionPoints/1000).');
setUnitExp('.$postVals[1].', 0.5);

var plot = addDiv("", "stdFloatDiv", document.getElementById("rtPnl"));
plot.innerHTML = "Plot against char";
plot.addEventListener("click", function () {makeBox("plot", "1075,'.$postVals[1].'", 500, 500, 200, 50)});

var plot = addDiv("", "stdFloatDiv", document.getElementById("rtPnl"));
plot.innerHTML = "Invite to Plot";
plot.addEventListener("click", function () {makeBox("plotInvite", "1080,'.$postVals[1].'", 500, 500, 200, 50)});

var skills = addDiv("unitEquip", "stdFloatDiv", document.getElementById("rtPnl"));
skills.innerHTML = "Character Skills";
skills.addEventListener("click", function () {makeBox("skills", "1076,'.$postVals[1].'", 500, 500, 200, 50)});

var skills = addDiv("", "stdFloatDiv", document.getElementById("rtPnl"));
skills.innerHTML = "Char Plots";
skills.addEventListener("click", function () {makeBox("plotList", "1083,'.$postVals[1].'", 500, 500, 200, 50)});

var traits = addDiv("unitEquip", "stdFloatDiv", document.getElementById("rtPnl"));
traits.innerHTML = "Character Traits";
traits.addEventListener("click", function () {makeBox("traits", "1077,'.$postVals[1].'", 500, 500, 200, 50)});
</script>';
fclose($unitFile);

?>