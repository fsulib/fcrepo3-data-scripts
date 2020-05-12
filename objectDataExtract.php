#!/usr/bin/env php
<?php
require 'utilities.php';

$children = get_directory_contents($fedora_objectstore);
foreach ($children as $child) {
  $subchildren = get_directory_contents("{$fedora_objectstore}/{$child}");
  foreach ($subchildren as $subchild) {
    echo $subchild;
  }
}
