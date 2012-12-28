<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use b55\i3WebManager as i3wm;
use b55\Entity\i3Client as i3Client;
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

$app->match('config/{config_name}/{workspace_name}',
  function (Request $request, $config_name, $workspace_name) use ($app) {

  $i3wm = $app['i3wm'];
  if (!is_string($config_name) || !is_string($workspace_name)) {
    return new Response($app['twig']->render($page, array('code' => '404')), $code);
  }

  $i3Config = $i3wm->getConfigs($config_name);
  $i3Workspace = $i3Config->getWorkspaces($workspace_name);

  $data = array();
  if ($i3Workspace instanceof i3Workspace) {
    $data['name'] = $i3Workspace->getName();
  }
  $i3Form = $app['i3wm_forms']['configForm'];
  $form = $i3Form->getWorkspaceForm($data);

  if ('POST' === $request->getMethod()) {
    $form->bind($request);
    if ($form->isValid()) {
      $data = $form->getData();
      $i3Workspace->setName($data['name']);

      $i3wm->setWorkspace($config_name, $i3Workspace, $workspace_name);

      $default_file = getYamlFilePathFromApp($app);
      $i3wm->save($default_file);

      $message[] = 'This workspace has been saved';

      return $app->redirect('/config/' . $config_name);
    }
  }

  return $app['twig']->render('config/see_workspace.html', array(
    'form' => $form->createView(),
    'config_name' => $config_name,
    'workspace' => $i3Workspace,
  ));

});
$app->match('config/{config_name}/{workspace_name}/{client_name}',
  function (Request $request, $config_name, $workspace_name, $client_name) use ($app) {

  $i3wm = $app['i3wm'];
  if (!is_string($config_name) || !is_string($workspace_name)
    || !is_string($client_name)) {

    return new Response($app['twig']->render($page, array('code' => '404')), $code);
  }

  $message = array();
  $i3Config = $i3wm->getConfigs($config_name);
  $i3Workspace = $i3Config->getWorkspaces($workspace_name);
  $i3Client = $i3Workspace->getClient($client_name);

  $data = array();
  if ($i3Client instanceof i3Client) {
    $data = array(
      'name' => $i3Client->getName(),
      'command' => $i3Client->getCommand(),
      'arguments' => $i3Client->getArguments(),
    );
  }

  $i3Form = $app['i3wm_forms']['configForm'];
  $form = $i3Form->getClientForm($data);

  if ('POST' === $request->getMethod()) {
    $form->bind($request);
    if ($form->isValid()) {
      $data = $form->getData();
      $i3Client->setName($data['name']);
      $i3Client->setCommand($data['command']);
      $i3Client->setArguments($data['arguments']);

      $i3wm->setClient($config_name, $workspace_name, $i3Client, $client_name);

      $default_file = getYamlFilePathFromApp($app);
      $i3wm->save($default_file);

      $message[] = 'This client has been saved';

      return $app->redirect('/config/' . $config_name . '/' .  $workspace_name);
    }
  }

  return $app['twig']->render('config/see_clients.html', array(
    'config_name' => $config_name,
    'workspace_name' => $workspace_name,
    'client' => $i3Client,
    'form' => $form->createView(),
  ));


  $data = array();
});
