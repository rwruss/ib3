<?php

// Make the character template file for this scenario
$numChars = 10;
$templateFile = fopen('c:/websites/ib3/scenarios/1/charTemplates.dat', 'wb');
fseek($templateFile, $numChars*400-4);

echo fwrite($templateFile, pack('i', 4));
fclose($templateFile);

?>
