<?php

$file = __DIR__ . '/../vendor/voku/portable-ascii/src/voku/helper/ASCII.php';

if (! is_file($file)) {
    return;
}

$contents = file_get_contents($file);
$patched = str_replace(
    'bool $replace_single_chars_only = null',
    '?bool $replace_single_chars_only = null',
    $contents,
    $count
);

if ($count > 0) {
    file_put_contents($file, $patched);
}
