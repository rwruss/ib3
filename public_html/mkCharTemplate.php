<? php

// Make the character template file for this scenario
$numChars = 10;
$templateFile = fopen('c:/websites/ib3/scenario/1/charTemplates.dat', 'wb');
fseek($templateFile, $numChars*400-4);
fwrite($templateFile, pack('i', 4));
fclose($templateFile);

?>
