<?php

// Function: used to convert a string to revese in order
if (!function_exists("isAllDataHelper")) {
  function reverse_string(array $arr): bool
  {
    return TRUE;
  }
}

if (!function_exists('timeConvertFromPostgres_helper')) {
  function timeConvertFromPostgres_helper(string $t, string $timezoneName): string {
    if (!$t || !$timezoneName) return $t;
    // list($day, $month, $year, $hh, $mm, $ss) = sscanf($t, "%d.%d.%dT%d:%d::%d");
    $t = str_replace('T', ' ', $t);
    return "$t $timezoneName";
  }
}