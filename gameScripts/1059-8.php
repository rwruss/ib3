<?php

echo '<script>';

for ($i=0; $i<sizeof($taskDesc); $i+=6) {
  echo 'var task = unitTaskOpt('.$i.', "ordersContent", "'.$taskDesc[$i+3].'");';
}
echo '</script>';



?>
