<?php

require_once('./slotFunctions.php');
require_once('./unitClass.php');

$warFile = fopen($gamePath.'/wars.war', 'rb');
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

echo '<script>
useDeskTop.newPane("warOpts");
thisDiv = useDeskTop.getPane("warOpts");';

// Verify that the viewing player is an owner of the war
fseek($warFile, $postVals[1]*$defaultBlockSize);
$warDat = unpack('i*', fread($warFile, $warBlockSize));

$sideSwitch = 1;
$playerSide = 1;
$oppside = 2;
if ($warDat[6] == $pGameID) {
  // player is the defender
  $sideSwitch = -1;
  $playerSide = 2;
  $oppside = 1;
}

// Show details for the war and the war status

// Show the conditions of ending the war and options as requested
switch($postVals[2]) {
  case 1:
    echo 'textBlob("", thisDiv, "You are choosing to surrender - you will have the following terms enforced upon you. PROCEED?");
    ';
  break;

  case 2:
  // Get list of resources the player owns
  $thisPlayer = loadPlayer($pGameID, $unitFile, 400);
  $playerCity = loadUnit($thisPlayer->get('homeCity'), $unitFile, 400);
  //echo 'Loaded city ('.$thisPlayer->get('homeCity').') type:'.get_class($playerCity);
  $resources = new itemSlot($playerCity->get('carrySlot'), $slotFile, 40);
  //print_r($resources->slotData);
  //unitList.renderSum(armyItems[i+1], "armyList_"+armyItems[i]);
  if ($warDat[$playerSide*6+9] + $warDat[$playerSide*6+11] + $warDat[$playerSide*6+13] > 0) {
    echo 'textBlob("", thisDiv, "You have offered the following items for a truce:<br>	R'.$warDat[$playerSide*6+8].'->'.$warDat[$playerSide*6+9].', R'.$warDat[$playerSide*6+10].'->'.$warDat[$playerSide*6+11].', R'.$warDat[$playerSide*6+12].'->'.$warDat[$playerSide*6+13].'	<p>If you would like, propose new terms below.");';
  }

  if ($warDat[$oppside*6+9] + $warDat[$oppside*6+11] + $warDat[$oppside*6+13] > 0) {
    echo 'textBlob("", thisDiv, "Your enemy has offered the following items for a truce:<br>
	R'.$warDat[$oppside*6+8].'->'.$warDat[$oppside*6+9].', R'.$warDat[$oppside*6+10].'->'.$warDat[$oppside*6+11].', R'.$warDat[$oppside*6+12].'->'.$warDat[$oppside*6+13].'
	<p>If you would like, propose new terms below.");';
  }

  echo 'rscList = new resourceList([1, 2, 3, 4, 5]);
    testUnitList = new uList(playerUnits);
    multiList = new resourceList([rscList, testUnitList]);
    optionBox1 = addDiv("", "selectContain", thisDiv);
    optionBox1.innerHTML = "1";
    optionBox1.addEventListener("click", function () {multiList.SLsingleSelect(this)});

    optionBox2 = addDiv("", "selectContain", thisDiv);
    optionBox2.innerHTML = "2";
    optionBox2.addEventListener("click", function () {multiList.SLsingleSelect(this)});

    let optionBox3 = addDiv("", "selectContain", thisDiv);
    optionBox3.innerHTML = "3";
    optionBox3.addEventListener("click", function () {multiList.SLsingleSelect(this)});

    sendButton = newButton(thisDiv, function () {
      console.log(SLreadSelection(optionBox1) + "," + SLreadSelection(optionBox2));
      scrMod("1126,'.$postVals[1].',"+SLreadSelection(optionBox1) + "," + SLreadSelection(optionBox2) + "," + SLreadSelection(optionBox3));
    });
    ';
  break;

  case 3:
  if ($playerSide == 1) {
	  echo 'textBlob("", thisDiv, "This goal of this war is to '.$warDat[3].' on the target of '.$warDat[2].'.  You may add additional demands below.");

	let optionBox1 = slideBox(thisDiv, 0);
	optionBox1.unitSpace.innerHTML = "rsc";
	  optionBox1.unitSpace.addEventListener("click", function () {rscList.SLsingleSelect(this, function() {setSlideQty(optionBox1, playerRsc[optionBox1.unitSpace.selected[0]])})});

    let optionBox2 = slideBox(thisDiv, 10000);
    optionBox2.unitSpace.innerHTML = "rsc";
	  optionBox2.unitSpace.addEventListener("click", function () {rscList.SLsingleSelect(this, function() {setSlideQty(optionBox2, playerRsc[optionBox2.unitSpace.selected[0]])})});

    let optionBox3 = slideBox(thisDiv, 10000);
    optionBox3.unitSpace.innerHTML = "rsc";
	  optionBox3.unitSpace.addEventListener("click", function () {rscList.SLsingleSelect(this, function() {setSlideQty(optionBox3, playerRsc[optionBox3.unitSpace.selected[0]])})});

    sendButton = newButton(thisDiv, function () {
      //scrMod("1126,'.$postVals[1].'," + [optionBox1.unitSpace.selected[0], optionBox1.slider.slide.value, optionBox2.unitSpace.selected[0], optionBox2.slider.slide.value, optionBox3.unitSpace.selected[0], optionBox3.slider.slide.value])//
      //console.log(optionBox1.unitSpace.selected[0] + "," + optionBox1.slider.slide.value);
      SLreadSelection(optionBox1);
      SLreadSelection(optionBox2);
    });';

  }
  /*
  if ($warDat[7]*$sideSwitch > 100) {
    echo 'textBlob("", thisDiv, "Can do")';
  } else echo 'textBlob("", thisDiv, "Cant do")';
	*/
  break;
}

// Submit button

fclose($warFile);
fclose($unitFile);
fclose($slotFile);

?>
