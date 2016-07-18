<?php

// Read message to see who it was From
$messageContentFile = fopen($gamePath.'/messages.dat', 'rb');
fseek($messageContentFile, $postVals[1]);
$headDat = unpack('i*', fread($messageContentFile, 40));
print_r($headDat);
echo '<script>
msgBox(useDeskTop.getPane("readMsg"), "3001,'.$headDat[3].','.$postVals[1].'",1);
</script>';

?>
