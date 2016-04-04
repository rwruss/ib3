<?php

// Get unit data and look up equipment in the given slot
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[2]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

$itemDesc = explode('<->', file_get_contents($gamePath.'/equip.desc'));
//print_r($itemDesc);

echo '<script>
		newTabMenu("equip");
		newTab("equip", 1, "Currently Equiped");
		newTab("equip", 2, "Available Items");
		newTab("equip", 3, "Player Buildings");
		tabSelect("equip", 1);
		';
// Show item that is currently equiped.
$itemDtl = explode('<-->', $itemDesc[17+$postvals[1]]);
echo 'textBlob("eq'.$i.', "equip_1", '.$itemDtl[1].')<br>';

for ($i=1; $i<sizeof($itemDesc); $i++) {
  $itemDtl = explode('<-->', $itemDesc[$i]);
  if ($itemDtl[0] == $postVals[1]) echo 'var item_'.$i.' = textBlob("eq'.$i.', "equip_2", '.$itemDtl[1].');
  item_'.$i.'.addeventlistener("click", function () {scrMod("1055,'.$i.'")});';
}
echo '</script>
?>
