<?php

include('./unitClass.php');

$_SESSION['selectedUnit'] = $postVals[1];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');

$thisChar = new char($postVals[1], $unitFile, 400);

if ($thisChar->get('owner') == $pGameID) {
	include ('./1074a.php');
}
else if ($thisChar->get('controller') == $pGameID {
	include ('./1074b.php');
}
else include ('.1074c.php');

?>
