<?php

function getYamlFilePathFromApp($app) {
  return $app['i3WebManager']['default']['path']
    . $app['i3WebManager']['default']['name'];
}

function getI3Layouts() {
  // TODO: read this from i3
  return array(
    'default' => 'default',
    'tabbed' => 'tabbed',
    'stacking' => 'stacking'
  );
}
