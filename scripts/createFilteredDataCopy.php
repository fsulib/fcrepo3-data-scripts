#!/usr/bin/env php
<?php
require '../includes/utilities.php';

$output_directory = create_output_home_directory();
logmsg("Beginning creation of filtered copy at {$output_directory}");
$children = get_directory_contents($fedora_objectstore);
foreach ($children as $child) {
  $subchildren = get_subset_filtered_directory_contents("{$fedora_objectstore}/{$child}", '../subsets/demo-subset-all.txt');
  foreach ($subchildren as $subchild) {
    $pid_data = extract_data_from_object("{$fedora_objectstore}/{$child}/{$subchild}");
    $pid_log_msg = "{$pid_data['pid']}: [{$pid_data['cmodel']}] '{$pid_data['label']}'";
    echo "Processing object {$pid_log_msg}\n";
    shell_exec("echo {$pid_log_msg} >> {$output_directory}/log.txt");
    foreach ($pid_data['datastreams'] as $datastream) {
      echo "Processing object {$pid_data['pid']}'s datastream {$datastream}\n";
    }
  }
}
logmsg("Creation of filtered copy completed at {$output_directory}");
