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
//print_r($warDat);
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

  // Read current offerings and demands

  echo 'rscList = new resourceList(playerRsc);
    testUnitList = new uList(playerUnits);
    offerMultiList = new multiList([rscList, testUnitList]);

	optionBox1 = offerMultiList.SLsingleButton(thisDiv'.readOffer($warDat[$playerSide*9+1], $warDat[$playerSide*9+2], $warDat[$playerSide*9+3]).');
	optionBox2 = offerMultiList.SLsingleButton(thisDiv'.readOffer($warDat[$playerSide*9+4], $warDat[$playerSide*9+5], $warDat[$playerSide*9+6]).');
	optionBox3 = offerMultiList.SLsingleButton(thisDiv'.readOffer($warDat[$playerSide*9+7], $warDat[$playerSide*9+8], $warDat[$playerSide*9+9]).');

	sendButton = newButton(thisDiv, function () {
      console.log(SLreadSelection(optionBox1) + "," + SLreadSelection(optionBox2));
      scrMod("1126,'.$postVals[1].',"+SLreadSelection(optionBox1) + "," + SLreadSelection(optionBox2) + "," + SLreadSelection(optionBox3));
    });

	sendButton = newButton(thisDiv, function () {scrMod("1131,'.$postVals[1].'")});
	sendButton.innerHTML = "Accept These Terms";
	';
  break;

  case 3:
  if ($playerSide == 1) {
	  echo 'textBlob("", thisDiv, "You are now able to enforce your conditions on the enemy.  This goal of this war is to '.$warDat[3].' on the target of '.$warDat[2].'.  You may add additional demands below.");

	  rscList = new resourceList([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20], {max:10000});
	charTypesList = new uList(defaultChars, {prefix:3});
	unitTypesList = new uList(defaultUnits, {prefix:4});
	demandMultiList = new multiList([rscList, charTypesList, unitTypesList]);

	demandBox1 = demandMultiList.SLsingleButton(thisDiv'.readOffer($warDat[$playerSide*9+19], $warDat[$playerSide*9+20], $warDat[$playerSide*9+21]).');
  demandBox1.innerHTML = "demand 1";
	demandBox2 = demandMultiList.SLsingleButton(thisDiv'.readOffer($warDat[$playerSide*9+22], $warDat[$playerSide*9+23], $warDat[$playerSide*9+24]).');
  demandBox2.innerHTML = "demand 1";
	demandBox3 = demandMultiList.SLsingleButton(thisDiv'.readOffer($warDat[$playerSide*9+25], $warDat[$playerSide*9+26], $warDat[$playerSide*9+27]).');
  demandBox3.innerHTML = "demand 1";

	sendButton = newButton(thisDiv, function () {scrMod("1132,'.$postVals[1].'")});
	sendButton.innerHTML = "Accept These Terms";';

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

function readOffer($type, $index, $amount) {
	switch($type) {
		case 0:
			return '';
		break;

		case 1: // offer a resource
			return ',{setVal:'.$index.', setQty:'.$amount.', list:rscList}';
		break;

		case 2: // offer a specific unit or character
			return ',{setVal:'.$index.', list:testUnitList}';
		break;

		case 3:  // Offer a character tpye
			return ',{setVal:'.$index.', list:charTypesList}';
		break;

		case 4: // offer a unit type
			return ',{setVal:'.$index.', list:unitTypesList}';
		break;
	}
}

?>
