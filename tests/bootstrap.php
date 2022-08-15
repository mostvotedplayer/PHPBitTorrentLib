<?php

spl_autoload_register(function($className) {
    $className = str_replace('\\', '/', $className);
    include $className . '.php';
});