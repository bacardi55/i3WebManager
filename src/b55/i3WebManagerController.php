<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use b55\i3WebManager as i3wm;
use b55\Forms;

require_once __DIR__ . '/Resources/lib/utils.php';

$app['i3wm'] = function () use ($app) {
  $default_file = getYamlFilePathFromApp($app);
  return new i3wm\i3WebManager($default_file);
};

$app->match('/', function () use ($app) {
  $i3wm = $app['i3wm'];

  if ($i3wm->is_new() === true) {
    return $app['twig']->render('index.html', array(
      'configs' => null
    ));
  }
  else {
    return $app['twig']->render('index.html', array(
      'configs' => $i3wm->getConfigsNames()
    ));
  }
});

$app->match('/config/new', function (Request $request) use ($app) {
  $i3wm = $app['i3wm'];
  $i3Form = $app['i3wm_forms']['configForm'];

  $form = $i3Form->getAddForm();
  $data = array();
  if ('POST' === $request->getMethod()) {
    $form->bind($request);

    if ($form->isValid()) {
      $data = $form->getData();

      $i3wm->addConfig($data['config_name'], $data['config_nb_workspace']);
      $default_file = getYamlFilePathFromApp($app);
      $i3wm->save($default_file);

      // Redirect to naming workspace page.
      return $app->redirect('/config/'.$data['config_name'].'/workspaces/edit');
    }
  }

  return $app['twig']->render('config/new.html', array(
    'form' => $form->createView()
  ));
});

$app->match('config/{config_name}', function ($config_name) use ($app) {
  $i3wm = $app['i3wm'];
  if (!is_string($config_name)) {
    return new Response($app['twig']->render($page, array('code' => '404')), $code);
  }

  $conf = $i3wm->getConfigs($config_name);

  return $app['twig']->render('config/see.html', array(
    'config' => $conf,
  ));
});

$app->match('config/{config_name}/{workspace_name}', function () use ($app) {
  $i3wm = $app['i3wm'];
  if (!is_string($config_name)) {
    return new Response($app['twig']->render($page, array('code' => '404')), $code);
  }

  $conf = $i3wm->getConfig($config_name);
});
