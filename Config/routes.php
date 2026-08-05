<?php

Router::connect('/', array('controller' => 'elections', 'action' => 'index', 'f29183a0-c2ce-4267-9a6e-09bf113e49ea'));
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
CakePlugin::routes();

require CAKE . 'Config' . DS . 'routes.php';
