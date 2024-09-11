<?php

$packages = array();

parsePackageFiles(__DIR__ . '/series');
ksort($packages);
// print_r($packages);
createCsv($packages);

function parsePackageFiles($folder)
{
    foreach (scandir($folder) as $filename) {
        if ($filename[0] === '.') {
            continue;
        }
        parsePackageFile("$folder/$filename");
    }
}

function parsePackageFile($filename)
{
    global $packages;

    if (!preg_match('/packages-(.*?)-(vc14|vc15|vs16|vs17)-(x64)-(stable|staging)\.txt/', $filename, $matches)) {
        return;
    }
    list($dummy, $version, $toolset, $arch, $stage) = $matches;
    $stream = fopen($filename, 'r');
    while (($line = fgets($stream)) !== false) {
        if (preg_match('/(c-client|net-snmp|[^-]+)-(.*?)-(vc14|vc15|vs16|nocrt)-(x64)\.zip/', $line, $matches)) {
            $packages["$matches[1]"]["$version-$toolset-$arch-$stage"] = $matches[2];
        }
    }
    fclose($stream);
}

function createCsv($packages)
{
    $stream = fopen(__DIR__ . '/packages.csv', 'w');
    $variants = getVariants($packages);
    // print_r($variants);
    foreach ($variants as $variant) {
        $parts = explode('-', $variant);
        $phpversion = $parts[0];
        fputs($stream, ",$phpversion-{$parts[3]}");
    }
    fputs($stream, PHP_EOL);

    foreach ($packages as $package => $details) {
        fputs($stream, "$package");
        foreach ($variants as $variant) {
            if (array_key_exists($variant, $details)) {
                fputs($stream, ",$details[$variant]");
            } else {
                fputs($stream, ',');
            }
        }
        fputs($stream, PHP_EOL);
    }
    fclose($stream);
}

function getVariants($packages)
{
    $variants = [];
    foreach ($packages as $package) {
        foreach (array_keys($package) as $key) {
            $variants[$key] = null;
        }
    }
    return array_keys($variants);
}
