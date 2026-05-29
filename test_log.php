<?php
$lines = file('c:\xampp\apache\logs\error.log');
$last = array_slice($lines, -50);
foreach($last as $l) {
  echo $l;
}
