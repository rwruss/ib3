<?php

/*
Menu for options of placing a unit up for merc sale
*/

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

echo '<script>
useDeskTop.newPane("military");
thisDiv = useDeskTop.getPane("military");
thisDiv.innerHTML = "";
var sumContain = addDiv("", "stdFloatDiv", thisDiv);
unitList.renderSum('.$postVals[1].', sumContain);';

// Confirm that player is the owner of the unitClass
$thisUnit = loadUnit($postVals[1], $unitFile, 400);
if ($thisUnit->get('owner') != $pGameID) exit('You dont own this unit');

// Confirm that unit is saleable
if (!$thisUnit->mercApproved) exit('not saaaaaleable');

// Show resource pricing options
echo 'var pricing = addDiv("", "stdFloatDiv", thisDiv);
pricing.innerHTML = "sale me for a price";
var priceOptions = [1, "Resource 1", 2, "Resource 2",3,"Resource 3",4,"Resource 4",5,"Resource 5"];

var formList = [];
var rOpt = selectList(pricing, priceOptions);
var rSlide = slideValBar(pricing, "1", 0, 1000);
formList.push(rOpt, rSlide.slide);

rOpt = selectList(pricing, priceOptions);
rSlide = slideValBar(pricing, "2", 0, 1000);
formList.push(rOpt, rSlide.slide);

rOpt = selectList(pricing, priceOptions);
rSlide = slideValBar(pricing, "3", 0, 1000);
formList.push(rOpt, rSlide.slide);

rOpt = selectList(pricing, priceOptions);
rSlide = slideValBar(pricing, "4", 0, 1000);
formList.push(rOpt, rSlide.slide);

rOpt = selectList(pricing, priceOptions);
rSlide = slideValBar(pricing, "5", 0, 1000);
formList.push(rOpt, rSlide.slide);
';

// Show duration options
echo 'var durList = [1, "1 Day", 2, "3 Day", 3, "5 Day", 4, "7 Day", 5, "14 Day"];
var duration = addDiv("", "stdFloatDiv", thisDiv);
duration.innerHTML = "sale me for a amount of time";
rOpt = selectList(duration, durList);
formList.push(rOpt);

var sendButton = addDiv("", "button", thisDiv);
sendButton.innerHTML = "Offer Unit";
sendButton.addEventListener("click", function () {scrMod("1119,'.$postVals[1].'"+collect(formList))})';

fclose($unitFile);
fclose($slotFile);

?>
