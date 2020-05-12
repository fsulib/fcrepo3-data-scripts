<?php
require 'variables.php';
date_default_timezone_set('America/New_York');

function logmsg($text) {
  global $log;
  $time = date('D Y-m-d H:i', time());
  $text = "{$time} - {$text}";
  echo "{$text}\n";
  $logfile = fopen($log, 'a');
  fwrite($logfile, "{$text}\n");
  fclose($logfile);
}

function create_mlocate_db($type) {
  switch ($type) {
    case "objects":
      global $objects_mlocatedb;
      logmsg("Indexing Fedora objectStore...");
      shell_exec("updatedb -v -U /data/objectStore -o {$objects_mlocatedb}");
      logmsg("Fedora objectStore indexed to {$objects_mlocatedb}.");
      break;
    case "datastreams":
      global $datastreams_mlocatedb;
      logmsg("Indexing Fedora datastreamStore...");
      shell_exec("updatedb -v -U /data/datamp -o {$datastreams_mlocatedb}");
      logmsg("Fedora datastreamStore indexed to {$datastreams_mlocatedb}.");
      break;
    default:
      echo "{$type} is not a supported option.\n";
  }
}

function get_directory_contents($directory) {
  $contents = scandir($directory);
  $filter = array('.', '..');
  return array_diff($contents, $filter);
}

function get_namespace_from_uri($uri) {
  preg_match('/info%3Afedora%2F(.*)%3A.*/', $uri, $result);
  return $result[1];
}
