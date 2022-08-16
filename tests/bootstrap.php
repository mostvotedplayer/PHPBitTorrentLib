<?php

spl_autoload_register(function($className) {
    $className = str_replace('\\', '/', $className) . '.php';
    if (is_file($className)) {
        include $className;
    }
});