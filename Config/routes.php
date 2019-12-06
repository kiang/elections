<?php

Router::connect('/', array('controller' => 'elections', 'action' => 'index', '5cbf16f4-d818-447e-b916-5ee30a8c0003'));
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
CakePlugin::routes();

require CAKE . 'Config' . DS . 'routes.php';
