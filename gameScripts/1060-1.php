<?php

// Get job descriptions
//$taskDesc = explode('<->', file_get_contents($scnPath.'/tasks.desc'));

echo 'Gather a resource from the map.  Do you wish to '.$typeInfo[2].'?  Select the amount of effort you want to put into
gathering this resource.  The more you gather, the more the surrounding area will be depleted.

<script>
addDiv("jobOptions", "cButtons", document.getElementById("taskDtlContent"));

var opt1 = optionButton("", "jobOptions", "10%");
opt1.addEventListener("click", function() {scrMod("1061,'.$postVals[1].',1")});

var opt2 = optionButton("", "jobOptions", "25%");
opt2.addEventListener("click", function() {scrMod("1061,'.$postVals[1].',2")});

var opt3 = optionButton("", "jobOptions", "50%");
opt3.addEventListener("click", function() {scrMod("1061,'.$postVals[1].',3")});

var opt4 = optionButton("", "jobOptions", "100%");
opt4.addEventListener("click", function() {scrMod("1061,'.$postVals[1].',4")});
</script>';

?>
