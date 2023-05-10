<?php

function patchFile($filename) {
    removeDiscriminatorType($filename);
    addCopyright($filename);
}

function addCopyright($filename)
{
    $content = file_get_contents($filename);
    if (str_contains($content, 'LINE Corporation licenses this file to you under the Apache License')) {
        return;
    }

    $year = date('Y');
    $copyright = <<<COPYRIGHT
/**
 * Copyright $year LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
COPYRIGHT;
    $content = preg_replace('/<\?php/s', "<?php\n$copyright", $content);
    file_put_contents($filename, $content);
}

function removeDiscriminatorType($filename)
{
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
    patchFile($filename);
}
