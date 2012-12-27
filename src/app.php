<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\FormServiceProvider;

use b55\Forms as b55Form;

$app = new Application();

$app->register(new UrlGeneratorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new TwigServiceProvider(), array(
  'twig.path' => array(__DIR__.'/../templates'),
  'twig.options' => array('cache' => __DIR__.'/../cache'),
));
$app->register(new Silex\Provider\TranslationServiceProvider(),  array(
  'locale' => 'en',
  'translator.domains' => array(),
));
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
}));

$app['i3wm_forms'] = array(
  'configForm' => new b55Form\configForms($app['form.factory']),
);

return $app;
