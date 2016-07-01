<?php

//Write a message to an object or city
echo '<script>
useDeskTop.newPane("characters");
thisDiv = useDeskTop.getPane("characters");

textBlob("", thisDiv, "To: '.$postVals[1].'");
msgDiv = addDiv("", "", thisDiv);
msgBox = document.createElement("textArea");
msgDiv.appendChild(msgBox)';



?>
