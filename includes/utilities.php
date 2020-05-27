<?php
require 'variables.php';
date_default_timezone_set('America/New_York');

function logmsg($text) {
  global $log;
  echo $log;
  $time = date('D Y-m-d H:i:s', time());
  $text = "{$time} - {$text}";
  echo "{$text}\n";
  shell_exec("echo {$text} >> {$log}");
}

function create_output_home_directory() {
  global $output_home;
  $time = date('Y-m-d_H-i-s', time());
  shell_exec("cd {$output_home}; mkdir {$time}");
  $output_dir = "{$output_home}/{$time}";
  shell_exec("cd {$output_dir}; mkdir objectStore; mkdir datastreamStore; touch log.txt");
  return $output_dir;
}

function create_mlocate_object_db() {
  global $objects_mlocatedb;
  logmsg("Indexing Fedora objectStore...");
  shell_exec("updatedb -v -U /data/objectStore -o {$objects_mlocatedb}");
  logmsg("Fedora objectStore indexed to {$objects_mlocatedb}.");
}

function create_mlocate_datastream_db() {
  global $datastreams_mlocatedb;
  logmsg("Indexing Fedora datastreamStore...");
  shell_exec("updatedb -v -U /data/datamp -o {$datastreams_mlocatedb}");
  logmsg("Fedora datastreamStore indexed to {$datastreams_mlocatedb}.");
}

function get_directory_contents($directory) {
  $contents = scandir($directory);
  $filter = array('.', '..');
  return array_diff($contents, $filter);
}

function get_subset_filtered_directory_contents($directory, $subsetfile) {
  $contents = get_directory_contents($directory);
  $subset = load_subset_file($subsetfile);
  return array_intersect($contents, $subset);
}

function load_subset_file($file_path) {
  $array = file($file_path, FILE_IGNORE_NEW_LINES);
  return $array;
}

function get_namespace_from_object_uri($uri) {
  preg_match('/info%3Afedora%2F(.*)%3A.*/', $uri, $result);
  return $result[1];
}

function is_uri_in_desired_namespace($uri) {
  global $namespaces;
  $uri_namespace = get_namespace_from_object_uri($uri); 
  return in_array($uri_namespace, $namespaces);
}

function uri_prefix_strip($uri) {
  return str_replace('info:fedora/', '', $uri);
}

function uri_prefix_add($uri) {
  return "info:fedora/" . $uri;
}

function uri_to_pid($uri) {
  $uri = str_replace('info%3Afedora%2F', '', $uri);
  $uri = str_replace('%3A', ':', $uri);
  return $uri;
}

function extract_data_from_object($path_to_object_uri) {
  $data = array();
  $data['datastreams'] = array();
  $object = simplexml_load_string(file_get_contents($path_to_object_uri));
  foreach ($object->children('foxml', TRUE) as $child) {
    if ($child->getName() == 'objectProperties') {
      $properties = $child->children('foxml', TRUE);
      foreach ($properties as $property) {
        $property_attributes = $property->attributes();
        if ($property_attributes['NAME']->__toString() == 'info:fedora/fedora-system:def/model#label') {
          $data['label'] = $property_attributes['VALUE']->__toString();
        }
      }
    } 
    elseif ($child->getName() == 'datastream') {
      $child_attributes = $child->attributes();
      if ($child_attributes['ID']->__toString() == 'RELS-EXT') {
        $rels_ext = $child->datastreamVersion->xmlContent->children('rdf', TRUE)->RDF->Description;
        if (isset($rels_ext->children('fedora', TRUE)->isMemberOf)) {
          $data['parent'] = uri_prefix_strip($rels_ext->children('fedora', TRUE)->isMemberOf->attributes('rdf', TRUE)->resource->__toString());
        }
        $data['cmodel'] = uri_prefix_strip($rels_ext->children('info:fedora/fedora-system:def/model#')->hasModel->attributes('rdf', TRUE)->resource->__toString());
      }
      else if ($child_attributes['CONTROL_GROUP']->__toString() == 'M') {
        $datastream_versions = $child->children('foxml', TRUE);
        foreach ($datastream_versions as $datastream_version) {
          $content_location_attributes = $datastream_version->contentLocation->attributes();
          $data['datastreams'][] = $content_location_attributes['REF']->__toString();
        }
      }
    }
  }
  $data['pid'] = $object['PID']->__toString();
  return $data;
}
