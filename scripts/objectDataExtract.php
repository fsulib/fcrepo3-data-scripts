#!/usr/bin/env php
<?php
require '../includes/utilities.php';

$children = get_directory_contents($fedora_objectstore);
foreach ($children as $child) {
  $subchildren = get_directory_contents("{$fedora_objectstore}/{$child}");
  foreach ($subchildren as $subchild) {
    if (is_uri_in_desired_namespace($subchild)) {
      echo "{$subchild}\n";
      print_r(extract_data_from_object("{$fedora_objectstore}/{$child}/{$subchild}"));
    }
  }
}
