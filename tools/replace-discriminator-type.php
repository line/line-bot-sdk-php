<?php

function replaceFile($filename) {
    $content = file_get_contents($filename);
    $content = preg_replace("/\n\s+\/\/ Initialize discriminator property with the model name\./s", '', $content);
    $content = preg_replace('/\n\s+\$this->container\[\'type\'\] = static::\$openAPIModelName;/s', '', $content);
    file_put_contents($filename, $content);
}

$recursive_directory_iterator = new \RecursiveDirectoryIterator(
    __DIR__ . '/../src/',
    \FilesystemIterator::SKIP_DOTS 
    | \FilesystemIterator::KEY_AS_PATHNAME
    | \FilesystemIterator::CURRENT_AS_FILEINFO
);
$iterator = new RecursiveIteratorIterator($recursive_directory_iterator, RecursiveIteratorIterator::LEAVES_ONLY);
foreach ($iterator as $filename) {
    if (!str_ends_with($filename, '.php')) {
        continue;
    }
    echo $filename;
    replaceFile($filename);
}
