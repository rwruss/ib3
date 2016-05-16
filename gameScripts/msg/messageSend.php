<?php

//include("../gameScripts/".$postVals[0].".php");
$msgFile = fopen($gamePath.'/messages.dat', 'rb');
$contentStart = 0;
$contentEnd = 0;
if (strlen($content)>0) {
	$contentFile = fopen($gamePath.'/customMsg.dat', 'r+b');
	if (flock($contentFile, LOCK_EX) {
		fseek($contentFile, 0, SEEK_END);
		$contentStart = ftell($contentFile);
		fwrite($contentFile, $content);
		$contentEnd = ftell($contentFile);
		
		flock($contentFile, LOCK_UN);
	}
	fclose($contentFile);
}



include('../gameScripts/msg/msgSend_'.$mOpt[0].'.php');

// Add to inbox for each player in the to List
$inboxDat = pack('i*', 1, $msgID);
foreach($toList as $toID) {
	
	echo 'Send the message to '.$toID;
}
fclose($msgFile);

?>