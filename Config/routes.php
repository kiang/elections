<?php

Router::connect('/', array('controller' => 'areas', 'action' => 'map'));
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
CakePlugin::routes();

require CAKE . 'Config' . DS . 'routes.php';
