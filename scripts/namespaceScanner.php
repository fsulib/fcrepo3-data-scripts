#!/usr/bin/env php
<?php
require '../includes/utilities.php';

$counts = array('total' => 0);
foreach ($namespaces as $namespace) {
  $counts[$namespace] = 0;
}

$children = get_directory_contents($fedora_objectstore);
foreach ($children as $child) {
  $subchildren = get_directory_contents("{$fedora_objectstore}/{$child}");
  foreach ($subchildren as $subchild) {
    $uri_namespace = get_namespace_from_object_uri($subchild);
    foreach ($namespaces as $namespace) {
      if ($uri_namespace == $namespace) {
        $counts[$namespace]++;
      }      
    } 
    $counts['total']++;
  }
}

echo "{$counts['total']} total objects in {$fedora_objectstore}.\n";
foreach ($namespaces as $namespace) {
  $percent = ($counts[$namespace] / $counts['total']) * 100;
  $formatted_percent = number_format($percent, 1);
  echo "{$counts[$namespace]} {$namespace} objects, {$formatted_percent}% of total objects.\n";
}
