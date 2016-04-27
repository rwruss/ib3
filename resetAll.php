<?php

echo "Reset Game";

include("./users/fillNames.php");

$clearFile = fopen("./users/playerNames.dat", "w");
fclose($clearFile);

$clearFile = fopen("./users/userCheck.dat", "w");
fclose($clearFile);

$clearFile = fopen("./users/userDat.dat", "w");
fclose($clearFile);

$clearFile = fopen("./games/gameList.lst", "w");
fclose($clearFile);

$clearFile = fopen("./games/openGames.dat", "w");
fclose($clearFile);

$clearFile = fopen("./users/userSlots.slt", "w+b");
fseek($clearFile, 39);
fwrite($clearFile, pack("C", 0));
fclose($clearFile);

session_start();
session_destroy();


?>