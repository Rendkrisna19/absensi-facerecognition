<?php

$dir = new RecursiveDirectoryIterator('d:\\FILE CLIENT\\NOVITA-TA\\absensi\\resources\\views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = [
    '#002D8B' => '#F97316',
    '#001f63' => '#EA580C',
    '#1A4BAF' => '#FB923C',
    'rgba(0,45,139,0.4)' => 'rgba(249,115,22,0.4)',
    'rgba(0, 45, 139, 0.4)' => 'rgba(249, 115, 22, 0.4)',
    '#0a2c9c' => '#F97316',
    '#1e40af' => '#FB923C',
    '#08227a' => '#EA580C',
    'bg-blue-' => 'bg-orange-',
    'text-blue-' => 'text-orange-',
    'border-blue-' => 'border-orange-',
    'ring-blue-' => 'ring-orange-',
    'shadow-blue-' => 'shadow-orange-',
    'hover:bg-blue-' => 'hover:bg-orange-',
    'hover:text-blue-' => 'hover:text-orange-',
    'focus:border-blue-' => 'focus:border-orange-',
    'focus:ring-blue-' => 'focus:ring-orange-'
];

foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $original = $content;
    
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    // Also replace blue-X to orange-X using regex to be safe it doesn't affect other things, but str_replace is fine above.
    // Let's do a regex for blue-\d+
    $content = preg_replace('/\b(bg|text|border|ring|shadow|from|to|via)-blue-(\d+)\b/', '$1-orange-$2', $content);
    $content = preg_replace('/\bhover:(bg|text|border)-blue-(\d+)\b/', 'hover:$1-orange-$2', $content);
    $content = preg_replace('/\bfocus:(ring|border)-blue-(\d+)\b/', 'focus:$1-orange-$2', $content);

    if ($original !== $content) {
        file_put_contents($path, $content);
        echo "Updated: $path\n";
    }
}

echo "Done.\n";
