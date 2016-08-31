<?php

session_start();
//if (!isset($_SESSION['userCount'])) $_SESSION['userCount'] = 0;

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');


$chatFile = fopen('chat.dat', 'rb');
fseek($chatFile, 0, SEEK_END);
$size = ftell($chatFile);

if ($size > $_SESSION['userCount']) {
  fseek($chatFile, $_SESSION['userCount']);
  $newStuff = fread($chatFile, $size - $_SESSION['userCount']);
  //echo "data: need new stuff {$newStuff}\n\n";
  echo "data: {$newStuff}\n\n";

} else {
  echo "data: nothing new.\n\n";
}
flush();
//$time = date('r');
//echo "data: Run ".$count." times,  The server time is: {$time}";
//echo "data: Yo mama {$_SESSION['userCount']} times!\n\n";

$time = date('r');
//echo "data: Some message {$time} - Size: {$size} vs {$_SESSION['userCount']}\n\n";
flush();

fclose($chatFile);

$_SESSION['userCount'] = $size;

?>
