<?php

include("./slotFunctions.php");
include('/cityClass.php');
echo'
<script>
useDeskTop.newPane("rscWork");
var thisDiv = useDeskTop.getPane("rscWork");
console.log("add to " + thisDiv);
</script>';

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
echo 'Filed opened';
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$rscPoint = unpack('i*', fread($unitFile, 400));
$baseCity = new city([intval($rscPoint[15]), $unitFile]);
echo 'Loaded city data: ('.$baseCity->cityData[1].')<br>';
echo $baseCity->aps().' points available.';

include('../gameScripts/1061-1.php');
//print_r($baseCity->cityData);
//fclose($unitFile);
?>
