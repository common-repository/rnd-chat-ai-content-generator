<?php

spl_autoload_register(function ($class) {
    $necessary = true;
    $file = null;
    if (strpos($class, 'VcwAI_') !== false) {
        $file = Viral_Content_With_Ai_Path . 'classes/' . str_replace('vcwai_', '', strtolower($class)) . '.php';
    }

    if ($file) {
        if (!$necessary && !file_exists($file)) {
            return;
        }
        if (!file_exists($file)) {
            return;
        }
        require($file);
    }
});
