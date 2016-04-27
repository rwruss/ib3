<?php

echo "Reset Games";


$clearFile = fopen("./users/userSlots.slt", "w+b");
fseek($clearFile, 39);
fwrite($clearFile, pack("C", 0));
fclose($clearFile);


?>