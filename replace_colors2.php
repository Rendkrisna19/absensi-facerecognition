<?php

$dir = new RecursiveDirectoryIterator('d:\\FILE CLIENT\\NOVITA-TA\\absensi\\resources\\views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = [
    '/#1e3b8b/i' => '#F08600',
    '/#243c94/i' => '#F08600'
];

foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $original = $content;
    
    foreach ($replacements as $pattern => $replace) {
        $content = preg_replace($pattern, $replace, $content);
    }
    
    if ($original !== $content) {
        file_put_contents($path, $content);
        echo "Updated: $path\n";
    }
}

echo "Done.\n";
