<?php

$gameID = 6;

$checkFile = fopen("c:\websites\ib3\games\\2\mapSlotFile.slt", "rb");
fseek($checkFile,  5200*404);
$unitList = array_filter(unpack("i*", fread($checkFile, 404)));
print_r($unitList);
fclose($checkFile);

$unitFile = fopen("c:\websites\ib3\games\\2\unitDat.dat", "rb");
foreach ($unitList as $unitID) {
	fseek($unitFile, $unitID*400);
	$unitDat = unpack('i*', fread($unitFile, 400));
	echo '<p>';
	print_r($unitDat);
}

echo '<hr>';
for ($i=0; $i<7; $i++) {
	fseek($unitFile, $i*400);
	$unitDat = unpack('i*', fread($unitFile, 400));
	echo '<p>';
	print_r($unitDat);
}
fclose($unitFile);
?>