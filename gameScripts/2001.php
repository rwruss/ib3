<?php

include("./slotFunctions.php");
//Load city info
$cityID = $_SESSION['selectedItem'];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $cityID*400);
$cityDat = unpack('i*', fread($unitFile, 400));
fclose($unitFile);
echo 'City Data: ';
print_r($cityDat);

// Load resource Data
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
$cityRsc = array_fill(1, 100, 0);
$rscDat = unpack("i*", readSlotData($slotFile, $cityDat[11], 40));
$numHave = sizeof($rscDat)/2;
fclose($slotFile);
echo '<P>Rsc Data:<br>';
print_r($rscDat);

echo '<p>City Resources: ';
for ($i=1; $i<$numHave; $i++) {
	$cityRsc[$rscDat[$i*2-1]] = $rscDat[$i*2];
}	
print_r($cityRsc);
echo '<script>window["updateRSC"] = function() {
	//alert(document.getElementById("rscType").value + ", " + document.getElementById("rscAmt").value);
	sendstr = "2002,"+document.getElementById("rscType").value + "," + document.getElementById("rscAmt").value
	alert(sendstr);
	makeBox(\'dumbbox\', sendstr, 500, 500, 200, 50);
	
}</script>
<p>Resource Type: <select id="rscType">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
</select><br>
<input id="rscAmt"><div onclick="updateRSC()">Update</div>';

?>