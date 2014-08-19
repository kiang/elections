<?php

if (!empty($parents)) {
    foreach ($parents AS $parent) {
        $this->Html->addCrumb($parent['Area']['name'], array('action' => 'map', $parent['Area']['id']));
    }
}
echo $this->Html->getCrumbs();
