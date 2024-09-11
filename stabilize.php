<?php

// Usage:
// php stabilize.php 8.3 8.4 master

$args = array_slice($argv, 1);
foreach ($args as $arg) {
    stabilize($arg);
}

function stabilize($php)
{
    $pattern = __DIR__ . "/series/packages-$php-v???-x??-staging.txt";
    $filenames = glob($pattern);
    foreach ($filenames as $src) {
        $dst = str_replace('staging.txt', 'stable.txt', $src);
        preg_match('/-(vc14|vc15|vs16|vs17)-(x64|x86)-/', $src, $matches);
        echo "$php-$matches[1]-$matches[2]: ";
        if (file_get_contents($dst) !== file_get_contents($src)) {
            copy($src, $dst);
            echo "stabilized\n";
        } else {
            echo "already stable\n";
        }
    }
}
