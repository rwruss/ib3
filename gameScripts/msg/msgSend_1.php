<?php
$msgID = 0;
if (flock($msgFile, LOCK_EX)) {
	fseek($msgFile, 0, SEEK_END);
	$msgID = ftell($msgFile)/100;
	fseek($msgFile, $msgID*100-1);
	fwrite($msgFile, pack('C', 0);
	flock($msgFile, LOCK_UN);
}

fseek($msgFile, $msgID*100)
fwrite($msgFile, pack('i*', $mOpt[0], $mOpt[1], $mOpt[2], $mOpt[3], $mOpt[4], $contentStart, $contentEnd, $mOpt[5], $mOpt[6]));

?>