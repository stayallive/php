<?php

// Usage: php bin/generate-supported-extensions.php 8.4

$supported = file_get_contents('https://github.com/mlocati/docker-php-extension-installer/raw/refs/heads/master/data/supported-extensions');

$version = $argv[1];

$blacklist = [
    'cassandra', // Not supported on debian images
    'ddtrace',   // This one seems to cause package conflicts which results in failing the build
    'gmagick',   // Conflicts with imagick which is preferred
    'oci8',      // This one seems to cause build issues
    'parallel',  // Requires ZTS build
    'pdo_oci',   // This one seems to cause build issues
    'relay',     // This one seems to cause build issues
    'xdiff',     // This one seems to cause build issues
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
