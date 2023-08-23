<?php

Router::connect('/', array('controller' => 'elections', 'action' => 'index', '6436cd74-71d0-45ae-ae38-4d560a8c0008'));
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
CakePlugin::routes();

require CAKE . 'Config' . DS . 'routes.php';
