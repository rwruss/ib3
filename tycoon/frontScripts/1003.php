<?php

session_start();
if (isset($_SESSION['playerId'])) {
	echo "conPanePlayer Stats";
	}
else include("../scripts/1002.php");

?>