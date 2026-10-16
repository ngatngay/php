<?php

if (!function_exists('ndg')) {
    function ndg(...$vars) {
        if (PHP_SAPI === 'cli') {
            var_dump(...$vars);
        } else {
            ob_start();
            var_dump(...$vars);
            $output = ob_get_clean();

            echo '<pre>';
            echo htmlspecialchars($output, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            echo '</pre>';
        }
    }
}

if (!function_exists('nde')) {
    function nde(...$vars) {
        ndg(...$vars);
        exit(1);
    }
}

