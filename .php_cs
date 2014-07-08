<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('tmp')
    ->name('*.php')
    ->name('*.ctp')
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->finder($finder)
;
