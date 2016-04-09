<?php

echo '<script>';

for ($i=0; $i<sizeof($jobDesc); $i+=6) {
  echo 'var task = unitTaskOpt('.$i.', "ordersContent", "'.$jobDesc[$i+3].'");';
}
echo '</script>';



?>
