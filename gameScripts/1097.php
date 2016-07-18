<?php

//Write a message to an object or city
echo '<script>
useDeskTop.newPane("characters");
thisDiv = useDeskTop.getPane("characters");

textBlob("", thisDiv, "To: '.$postVals[1].'");
msgBox(thisDiv, "3001,'.$postVals[1].',0",0);';
?>
