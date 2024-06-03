<?php

$composerJsonPath = 'composer.json';
$composerLockPath = 'composer.lock';

// Load composer.json and composer.lock files
$composerJson = json_decode(file_get_contents($composerJsonPath), true);
$composerLock = json_decode(file_get_contents($composerLockPath), true);

if (!$composerJson || !$composerLock) {
    echo "Error: Could not read composer.json or composer.lock.\n";
    exit(1);
}

// Function to create the version constraint
function createVersionConstraint($version) {
    if (strpos($version, 'dev') !== false) {
        return $version;
    }
    if (preg_match('/^(\d+\.\d+)\.\d+/', $version, $matches)) {
        return "~" . $matches[1] . ".0";
    }
    return $version;
}

// Update the require section
foreach ($composerLock['packages'] as $package) {
    $name = $package['name'];
    $version = $package['version'];

    if (isset($composerJson['require'][$name])) {
        $composerJson['require'][$name] = createVersionConstraint($version);
    }
}

// Update the require-dev section
foreach ($composerLock['packages-dev'] as $package) {
    $name = $package['name'];
    $version = $package['version'];

    if (isset($composerJson['require-dev'][$name])) {
        $composerJson['require-dev'][$name] = createVersionConstraint($version);
    }
}

// Save the updated composer.json
file_put_contents($composerJsonPath, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "composer.json has been updated.\n";
