#!/usr/bin/env php
<?php
require '../includes/utilities.php';

$output_directory = create_output_home_directory();
$object_directories = get_directory_contents($fedora_objectstore);
foreach ($object_directories as $object_directory) {
  $object_uris = get_subset_filtered_directory_contents("{$fedora_objectstore}/{$object_directory}", '../subsets/demo-subset-simple.txt');
  foreach ($object_uris as $object_uri) {
    $object_uri_path = "{$fedora_objectstore}/{$object_directory}/{$object_uri}";
    $pid_data = extract_data_from_object($object_uri_path);
    $pid_encoded_label = urlencode($pid_data['label']);
    logmsg("Object {$object_uri}: pid='{$pid_data['pid']}' cmodel='{$pid_data['cmodel']}' title='{$pid_encoded_label}'", $output_directory);
    copy_object($object_uri_path, $output_directory); 
    foreach ($pid_data['datastreams'] as $datastream) {
      $datastream_uri = datastream_to_uri($datastream); 
      $datastream_uri_path = get_path_to_datastream($datastream_uri);
      logmsg("Object {$object_uri} datastream {$datastream_uri}: {$datastream_uri_path}", $output_directory);
      copy_datastream($datastream_uri_path, $output_directory);
    }
  }
}
logmsg("Creation of filtered copy completed at {$output_directory}", $output_directory);
