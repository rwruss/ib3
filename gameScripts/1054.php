<?php

// PV: Equipment slot #

// Get unit data and look up equipment in the given slot
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

$itemDesc = explode('<->', file_get_contents($gamePath.'/equip.desc'));
//print_r($itemDesc);
echo '
<div class="taskHeader" id="eq_header"></div>
<div class="centeredmenu" id="eq_tabs"><ul id="eq_tabs_ul"></ul></div>
<div class="taskOptions" id="eq_options"></div>
<script>
		newTabMenu("eq");
		newTab("eq", 1, "Currently Equiped");
		newTab("eq", 2, "Available Items");
		tabSelect("eq", 1);
		';
// Show item that is currently equiped.
if ($unitDat[17+$postVals[1]] > 0) {
	// Show current item
	$itemDtl = explode('<-->', $itemDesc[$unitDat[17+$postVals[1]]]);
	echo 'var eq'.$postVals[1].'_dtl = textBlob("eq'.$unitDat[17+$postVals[1]].'", "eq_tab1", "'.$itemDtl[1].'");
	eq'.$postVals[1].'_dtl.addEventListener("click", function () {passClick("1055,'.$unitDat[17+$postVals[1]].','.$postVals[1].',0", "eq_header")});';
} else {
	// nothing is equipped in this slot
	echo 'var eq'.$postVals[1].'_dtl = textBlob("eq0", "eq_tab1", "Nothing equipped");';
}

for ($i=1; $i<sizeof($itemDesc); $i++) {
  $itemDtl = explode('<-->', $itemDesc[$i]);
  if ($itemDtl[0] == $postVals[1]) echo 'var item_'.$i.' = textBlob("eq'.$i.'", "eq_tab2", "'.$itemDtl[1].'");
  item_'.$i.'.addEventListener("click", function () {passClick("1055,'.$i.','.$postVals[1].',1", "eq_header")});';
}
echo '</script>';
?>
