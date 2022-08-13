<?php

Router::connect('/', array('controller' => 'elections', 'action' => 'index', '62053691-0184-496f-8738-1619acb5b862'));
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
CakePlugin::routes();

require CAKE . 'Config' . DS . 'routes.php';
