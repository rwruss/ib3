<?php



echo 'Option details for task type '.$postVals[1].' and unit # '.$postVals[2].'<p>
Task 1 - Foraging - do you wish to start this task here?

<script>
confirmButtons("Foraging - do you wish to start this task here?", "1051,'.$postVals[1].','.$postVals[2].','.$_SESSION["selectedItem"].', '.$postVals[1].','.$postVals[2].'", "taskDtlContent", 2, "Start!");

</script>';

?>
