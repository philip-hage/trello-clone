<?php
include 'core/global.init.php';
$system['docroot'] = $_SERVER['DOCUMENT_ROOT'] . '/';
include $system['docroot'] . 'templates/blocks/block.head.php';
if (isset($application['pageFolder']) && !empty($application['pageFolder']) && isset($application['pageTemplate']) && !empty($application['pageTemplate'])) {
 
  if (file_exists('templates/pages/' . $application['pageFolder'] . '/' . $application['pageTemplate'] . '.php')) {
    include 'templates/pages/' . $application['pageFolder'] . '/' . $application['pageTemplate'] . '.php';
  }
}

include $system['docroot'] . 'templates/blocks/block.foot.php';
