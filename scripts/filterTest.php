#!/usr/bin/env php
<?php
require '../includes/utilities.php';

/*
//$count = 0;
$children = get_directory_contents($fedora_objectstore);
foreach ($children as $child) {
  $subchildren = get_subset_filtered_directory_contents("{$fedora_objectstore}/{$child}", '../subsets/demo-subset-all.txt');
  foreach ($subchildren as $subchild) {
    echo "{$subchild}\n";
    shell_exec("echo {$subchild} >> /root/fcrepo3-data-scripts/subsets/all.txt");
    $count++;
  }
}
//echo "Count: {$count}\n";
*/

$input = load_subset_file('../subsets/demo-subset-all.txt');
$output = load_subset_file('../subsets/all.txt');
$diff = array_diff($input, $output);
foreach ($diff as $diffy) {
  shell_exec("echo {$diffy} >> /root/fcrepo3-data-scripts/subsets/missing.txt"); 
}
