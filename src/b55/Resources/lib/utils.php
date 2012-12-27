<?php

function getYamlFilePathFromApp($app) {
  return $app['i3WebManager']['default']['path']
    . $app['i3WebManager']['default']['name'];
}
