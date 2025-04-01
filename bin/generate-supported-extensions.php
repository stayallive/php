<?php

// Usage: php bin/generate-supported-extensions.php 8.4

$supported = file_get_contents('https://github.com/mlocati/docker-php-extension-installer/raw/refs/heads/master/data/supported-extensions');

$version = $argv[1];

$blacklist = [
    'cassandra',      // Not supported on debian images
    'ddtrace',        // This one seems to cause package conflicts which results in failing the build
    'geos',           // This one seems to cause build issues
    'geospatial',     // This one seems to cause build issues
    'gmagick',        // Conflicts with imagick which is preferred
    'ion',            // This one seems to cause build issues
    'ioncube_loader', // This one seems to cause build issues
    'oci8',           // This one seems to cause build issues
    'opencensus',     // This one seems to cause build issues
    'parallel',       // Requires ZTS build
    'phpy',           // This one seems to cause build issues
    'pdo_oci',        // This one seems to cause build issues
    'relay',          // This one seems to cause build issues
    'snuffleupagus',  // This one seems to cause build issues
    'swoole',         // Conflicts with openswoole which is preferred
    'uopz',           // This one seems to cause build issues
    'xdiff',          // This one seems to cause build issues
];

$supportedExtensions = implode(
    ' ',
    array_filter(
        array_map(
            static fn ($extensionSupport) => explode(' ', $extensionSupport, 2)[0],
            array_filter(
                explode("\n", $supported),
                static fn ($extensionSupport) => str_contains($extensionSupport, $version),
            ),
        ),
        static fn ($extension) => !in_array($extension, $blacklist, true),
    ),
);

echo $supportedExtensions . "\n";
