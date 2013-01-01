<?php
$app['debug'] = true; //TODO REMOVE

// configure your app for the production environment
$app['i3WebManager'] = array(
  'default' => array(
    'path' => __DIR__ . '/../src/b55/Resources/',
    'name' => 'i3config.yml',
  )
);

$app['i3_config_file'] = __DIR__ . '/../src/b55/Resources/config';
