<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use b55\i3WebManager as i3wm;
use b55\Entity\i3Client as i3Client;
use b55\Entity\i3Workspace as i3Workspace;
use b55\Forms;

require_once __DIR__ . '/Resources/lib/utils.php';

$app['i3wm'] = function () use ($app) {
  $default_file = getYamlFilePathFromApp($app);
  return new i3wm\i3WebManager($default_file);
};

/* INDEX */
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

/* Create config page */
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
      return $app->redirect('/config/'.$data['config_name']);
    }
  }

  return $app['twig']->render('config/new.html', array(
    'form' => $form->createView()
  ));
});

/* List of workspace */
$app->match('/config/{config_name}', function ($config_name) use ($app) {
  $i3wm = $app['i3wm'];
  if (!is_string($config_name)) {
    return new Response($app['twig']->render($page, array('code' => '404')), $code);
  }

  $conf = $i3wm->getConfigs($config_name);

  return $app['twig']->render('config/see.html', array(
    'config' => $conf,
  ));
});

/* Remove config */
$app->match('/config/{config_name}/remove', function ($config_name) use ($app) {
  if (!is_string($config_name)) {
    return new Response($app['twig']->render($page, array('code' => '404')), $code);
  }

  $i3wm = $app['i3wm'];
  $i3wm->removeConfig($config_name);

  return $app->redirect('/');
});


/* List of clients */
$app->match('/config/{config_name}/{workspace_name}',
  function (Request $request, $config_name, $workspace_name) use ($app) {

  $i3wm = $app['i3wm'];
  if (!is_string($config_name) || !is_string($workspace_name)) {
    return new Response($app['twig']->render($page, array('code' => '404')), $code);
  }

  $i3Config = $i3wm->getConfigs($config_name);

  $data = array();
  if ($workspace_name == 'new') {
    $i3Workspace = new i3Workspace('new');
    $data['is_new'] = 1;
  }
  else {
    $i3Workspace = $i3Config->getWorkspaces($workspace_name);

    if ($i3Workspace instanceof i3Workspace) {
      $data = array(
        'name' => $i3Workspace->getName(),
        'is_new' => 0,
      );
    }
  }

  $i3Form = $app['i3wm_forms']['configForm'];
  $form = $i3Form->getWorkspaceForm($data);

  if ('POST' === $request->getMethod()) {
    $form->bind($request);
    if ($form->isValid()) {
      $data = $form->getData();
      if ($data['is_new'] == 1) {
        $i3Config->addWorkspace(new i3Workspace($data['name']));
        $i3wm->save();
      }
      else {
        $i3Workspace->setName($data['name']);
        $i3wm->save();
      }
      return $app->redirect('/config/' . $config_name);
    }
  }

  return $app['twig']->render('config/see_workspace.html', array(
    'form' => $form->createView(),
    'config_name' => $config_name,
    'workspace' => $i3Workspace,
  ));

});

/* Remove workspace */
$app->match('/config/{config_name}/{workspace_name}/remove',
  function ($config_name, $workspace_name) use ($app) {

  if (!is_string($config_name) || !is_string($workspace_name)) {
    return new Response($app['twig']->render($page, array('code' => '404')), $code);
  }
  $i3wm = $app['i3wm'];
  $i3wm->removeWorkspace($config_name, $workspace_name);

  return $app->redirect('/config/' . $config_name);
});

/* Edit a client */
$app->match('/config/{config_name}/{workspace_name}/{client_name}',
  function (Request $request, $config_name, $workspace_name, $client_name = NULL) use ($app) {

  $i3wm = $app['i3wm'];
  if (!is_string($config_name) || !is_string($workspace_name)
    || ($client_name && !is_string($client_name))) {

    return new Response($app['twig']->render($page, array('code' => '404')), $code);
  }

  $i3Config = $i3wm->getConfigs($config_name);
  $i3Workspace = $i3Config->getWorkspaces($workspace_name);

  $data = array();
  if ($client_name == 'new') {
    $i3Client = new i3Client('new');
    $data['is_new'] = 1;
  }
  else {
    $i3Client = $i3Workspace->getClient($client_name);

    if ($i3Client instanceof i3Client) {
      $data = array(
        'name' => $i3Client->getName(),
        'command' => $i3Client->getCommand(),
        'arguments' => $i3Client->getArguments(),
        'is_new' => 0,
      );
    }
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

      if ($data['is_new'] == 1) {
        $i3wm->addClient($config_name, $workspace_name, $i3Client);
      }
      else {
        $i3wm->save();
      }


      return $app->redirect('/config/' . $config_name . '/' .  $workspace_name);
    }
  }

  return $app['twig']->render('config/see_clients.html', array(
    'config_name' => $config_name,
    'workspace_name' => $workspace_name,
    'client' => $i3Client,
    'form' => $form->createView(),
  ));
});
/* Remove client */
$app->match('/config/{config_name}/{workspace_name}/{client_name}/remove',
  function ($config_name, $workspace_name, $client_name) use ($app) {

  if (!is_string($config_name) || !is_string($workspace_name)
    || !is_string($client_name)) {

    return new Response($app['twig']->render($page, array('code' => '404')), $code);
  }

  $i3wm = $app['i3wm'];
  $i3wm->load();
  $i3wm->removeClient($config_name, $workspace_name, $client_name);

  return $app->redirect('/config/' . $config_name . '/' . $workspace_name);
});


