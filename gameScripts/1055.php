<?php

// pVals - item #, slot #, option #

if ($postVals[3] == 1) {
  echo 'Item description and stats
  <script>
  confirmButtons("", "1056,'.$postVals[1].','.$postVals[2].',1", "eq_header", 3, "Equip", "");
  </script>';
} else {
  echo 'Item description and stats
  <script>
  confirmButtons("", "1056,'.$postVals[1].','.$postVals[2].',0", "eq_header", 3, "Drop", "");
  </script>';
}

?>
