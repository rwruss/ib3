<?php

// Make the character template file for this scenario
$numChars = 10;
$templateFile = fopen('./charTemplates.dat', 'wb');
fseek($templateFile, $numChars*400-4);
fwrite($templateFile, pack('i', 4));

?>