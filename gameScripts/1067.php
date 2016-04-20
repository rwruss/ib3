<?php

/*
This will convert a resource cart into a permanant PLAYER sub-city
*/
include('./cityClass.php');
include("./slotFunctions.php");

// Load the resource cart data (transported resources and buildings) and save to the players city slot (if not already linked)
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $_SESSION['selectedUnit']*$defaultblocksize);
$unitDat = unpack('i*', fread($unitFile, 400));

// Change all building statuses to undeployed (should already been done via creation of the unit)

// Create a new parent city ID and set parameters


// Add the parent city ID to the current city

// Remove the unit cart / other items from the map and change the mapslot to display the new city

?>