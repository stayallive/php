<?php

// Usage: php bin/generate-supported-extensions.php 8.4

$blacklist = [
    'cassandra',      // Not supported on debian images
    'ddtrace',        // This one seems to cause package conflicts which results in failing the build
    'ecma_intl',      // This one seems to cause build issues
    'geos',           // This one seems to cause build issues
    'geospatial',     // This one seems to cause build issues
    'gmagick',        // Conflicts with imagick which is preferred
    'imap',           // This one seems to cause build issues
    'ion',            // This one seems to cause build issues
    'ioncube_loader', // This one seems to cause build issues
    'oci8',           // This one seems to cause build issues
    'opencensus',     // This one seems to cause build issues
    'parallel',       // Requires ZTS build
    'phpy',           // This one seems to cause build issues
    'pdo_oci',        // This one seems to cause build issues
    'pdo_sqlsrv',     // This one seems to cause build issues
    'rdkafka',        // This one seems to cause build issues
    'relay',          // This one seems to cause build issues
    'snuffleupagus',  // This one seems to cause build issues
    'sqlsrv',         // This one seems to cause build issues
    'swoole',         // Conflicts with openswoole which is preferred
    'uopz',           // This one seems to cause build issues
    'xdiff',          // This one seems to cause build issues
];

$supported = file_get_contents('https://github.com/mlocati/docker-php-extension-installer/raw/refs/heads/master/data/supported-extensions');

$version = $argv[1] ?? '';

if (empty($version)) {
    echo "PHP version required to be passed in the first argument." . PHP_EOL;

    exit(1);
}

$dockerFilePath = __DIR__ . "/../{$version}.Dockerfile";

if (!file_exists($dockerFilePath)) {
    echo "Dockerfile for PHP version {$version} does not exist." . PHP_EOL;

    exit(1);
}

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

$dockerFile = file_get_contents($dockerFilePath);

$dockerFile = preg_replace(
    '/^RUN extensions=".*/m',
    'RUN extensions="' . $supportedExtensions . '" \\',
    $dockerFile,
);

file_put_contents($dockerFilePath, $dockerFile);

$readmeFlag = $argv[2] ?? '';

if ($readmeFlag === '--readme') {
    $readmeFilePath = __DIR__ . '/../README.md';

    $readmeFile = file_get_contents($readmeFilePath);

    $supportedList = PHP_EOL;

    $supportedList .= implode(PHP_EOL, array_map(
        static fn ($extension) => "- {$extension}",
        explode(' ', $supportedExtensions),
    ));

    $supportedList .= PHP_EOL;

    $readmeFile = preg_replace(
        '#^<!-- Generated with script/generate-extensions-list.php -->.*<!-- /Generated with script/generate-extensions-list.php -->#ms',
        "<!-- Generated with script/generate-extensions-list.php -->{$supportedList}<!-- /Generated with script/generate-extensions-list.php -->",
        $readmeFile,
    );

    file_put_contents($readmeFilePath, $readmeFile);
}
