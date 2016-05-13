<?php

if (flock($msgFile, LOCK_EX)) {
	fseek($msgFile, 0, SEEK_END);
	$msgID = ftell($msgFile)/100;
	fseek($msgFile, $msgID*100-1);
	fwrite($msgFile, pack('C', 0);
	flock($msgFile, LOCK_UN);
}

?>