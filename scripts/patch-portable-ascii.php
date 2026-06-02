<?php

$file = __DIR__ . '/../vendor/voku/portable-ascii/src/voku/helper/ASCII.php';

if (! is_file($file)) {
    return;
}

$contents = file_get_contents($file);

$contents = str_replace('??bool $replace_single_chars_only', '?bool $replace_single_chars_only', $contents);

if (strpos($contents, '?bool $replace_single_chars_only = null') === false) {
    $contents = str_replace(
        'bool $replace_single_chars_only = null',
        '?bool $replace_single_chars_only = null',
        $contents
    );
}

file_put_contents($file, $contents);
