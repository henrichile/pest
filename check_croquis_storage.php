#!/usr/bin/env php
<?php

/**
 * Script de diagnóstico para verificar el almacenamiento de archivos de croquis
 */

echo "=== Diagnóstico de Almacenamiento de Croquis ===\n\n";

// 1. Verificar directorios
echo "1. Verificando directorios:\n";
$directories = [
    'storage/app/public/services/croquis',
    'public/storage',
    'public/storage/services/croquis',
];

foreach ($directories as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    $exists = file_exists($fullPath);
    $isLink = is_link($fullPath);
    $isDir = is_dir($fullPath);
    $writable = is_writable($fullPath);

    echo "   - $dir:\n";
    echo "     Existe: " . ($exists ? '✓' : '✗') . "\n";
    if ($exists) {
        echo "     Es symlink: " . ($isLink ? '✓' : '✗') . "\n";
        echo "     Es directorio: " . ($isDir ? '✓' : '✗') . "\n";
        echo "     Escribible: " . ($writable ? '✓' : '✗') . "\n";
        if ($isLink) {
            echo "     Apunta a: " . readlink($fullPath) . "\n";
        }
    }
    echo "\n";
}

// 2. Verificar archivos de croquis existentes
echo "2. Archivos en storage/app/public/services/croquis/:\n";
$croquisDir = __DIR__ . '/storage/app/public/services/croquis';
if (is_dir($croquisDir)) {
    $files = scandir($croquisDir);
    $imageFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']);
    });

    if (count($imageFiles) > 0) {
        foreach ($imageFiles as $file) {
            $filePath = $croquisDir . '/' . $file;
            $size = filesize($filePath);
            $sizeKB = round($size / 1024, 2);
            echo "   - $file ($sizeKB KB)\n";
        }
    } else {
        echo "   (vacío)\n";
    }
} else {
    echo "   El directorio no existe\n";
}

echo "\n3. Verificando permisos:\n";
echo "   - Usuario actual: " . get_current_user() . "\n";
echo "   - UID: " . getmyuid() . "\n";
echo "   - GID: " . getmygid() . "\n";

// 4. Verificar configuración de PHP
echo "\n4. Configuración de PHP para uploads:\n";
echo "   - upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "   - post_max_size: " . ini_get('post_max_size') . "\n";
echo "   - memory_limit: " . ini_get('memory_limit') . "\n";
echo "   - max_execution_time: " . ini_get('max_execution_time') . "s\n";

// 5. Test de escritura
echo "\n5. Test de escritura:\n";
$testFile = __DIR__ . '/storage/app/public/services/croquis/test_write.txt';
$testContent = 'Test de escritura - ' . date('Y-m-d H:i:s');

try {
    $written = file_put_contents($testFile, $testContent);
    if ($written !== false) {
        echo "   ✓ Escritura exitosa ($written bytes)\n";
        echo "   ✓ Archivo creado en: $testFile\n";

        // Intentar leer
        if (file_exists($testFile)) {
            echo "   ✓ Archivo verificado en disco\n";
            unlink($testFile);
            echo "   ✓ Archivo de prueba eliminado\n";
        }
    } else {
        echo "   ✗ No se pudo escribir el archivo\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Fin del diagnóstico ===\n";
