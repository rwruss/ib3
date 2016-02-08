<?php

session_start();
$_SESSION['gameId'] = $postVals[1];
echo "conPane - game ".$postVals[1]."


<script>window.location.replace('./play.php?gameID=".$postVals[1]."')</script>";

?>