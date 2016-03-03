<?php

echo '<script>newConfirmBox("confirm");
document.getElementById("promptBox").innerHTML = "Do you really want to abandon this settlement and mobilize the population?"
var acceptItem = document.getElementById("acceptBox");
acceptItem.innerHTML = "Yes - Mobilize the Tribe!";
acceptItem.addEventListener("click", function() {scrMod(\'1047,'.$_SESSION['selectedItem'].\');});
document.getElementById("declineBox").innerHTML = "No - Stay Put.";


</script>';

?>