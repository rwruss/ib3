<?php

// Load message data

$msgFile = fopen($gamePath.'/messages.dat', 'rb');
fseek($msgFile, $msgID*100);
$msgDat = unpack('i*', fread($msgFile, 100));
fclose($msgFile);

include ('../gameScripts/msg/msgRead_'.$msgDat[4].'.php');
/*
$contentStart = 0;
$contentEnd = 0;
if (strlen($content)>0) {
	$contentFile = fopen($gamePath.'/messages.dat', 'r+b');
	if (flock($contentFile, LOCK_EX) {
		fseek($contentFile, 0, SEEK_END);
		$contentStart = ftell($contentFile);
		fwrite($contentFile, $content);
		$contentEnd = ftell($contentFile);
		
		flock($contentFile, LOCK_UN);
	}
	fclose($contentFile);
}

include('../gameScripts/msg/msgSend_'.$messageOptions[0].'.php');
fclose($msgFile);
*/
?>