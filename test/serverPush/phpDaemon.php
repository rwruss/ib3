<?php

header("Content-Type: text/event-stream\n\n");
header('Cache-Control: no-cache');
header('Connection: keep-alive');


$time = date('r');
echo "data: The server time is: {$time}\n\n";
ob_flush();
flush();

?>
