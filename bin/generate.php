<?php

// Usage: php bin/generate.php

$supported = [
    '8.0',
    '8.1',
    '8.2',
    '8.3',
    '8.4',
];

$readmeVersion = '8.4';

$last = array_key_last($supported);

foreach ($supported as $version) {
    $params = $version === $readmeVersion ? '--readme' : '';

    exec('php ' . __DIR__ . "/generate-supported-extensions.php {$version} {$params}");
}
