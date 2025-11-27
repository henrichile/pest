<?php
// test_upload.php - Standalone File Upload Test
// Place this in public/ directory

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test de Subida de Archivos (Standalone)</h1>";

// Display Configuration
echo "<h2>Configuración PHP</h2>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "<li>max_execution_time: " . ini_get('max_execution_time') . "</li>";
echo "<li>file_uploads: " . ini_get('file_uploads') . "</li>";
echo "</ul>";

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Resultado de Subida</h2>";
    
    if (empty($_FILES)) {
        echo "<p style='color: red; font-weight: bold;'>⚠️ \$_FILES está vacío. El servidor web (Nginx/Apache) probablemente bloqueó la solicitud antes de llegar a PHP (413 Payload Too Large).</p>";
    } else {
        echo "<pre>";
        print_r($_FILES);
        echo "</pre>";
        
        if (isset($_FILES['test_file']) && $_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['test_file']['tmp_name'];
            $name = basename($_FILES['test_file']['name']);
            $size = $_FILES['test_file']['size'];
            
            echo "<p style='color: green;'>✅ Archivo recibido correctamente: $name ($size bytes)</p>";
            echo "<p>Ubicación temporal: $tmp_name</p>";
        } else {
            echo "<p style='color: red;'>❌ Error en la subida. Código de error: " . ($_FILES['test_file']['error'] ?? 'Desconocido') . "</p>";
            // Explain error codes
            $errors = [
                1 => 'UPLOAD_ERR_INI_SIZE (Excede upload_max_filesize en php.ini)',
                2 => 'UPLOAD_ERR_FORM_SIZE (Excede MAX_FILE_SIZE en formulario)',
                3 => 'UPLOAD_ERR_PARTIAL (Subida parcial)',
                4 => 'UPLOAD_ERR_NO_FILE (No se subió archivo)',
                6 => 'UPLOAD_ERR_NO_TMP_DIR (Falta carpeta temporal)',
                7 => 'UPLOAD_ERR_CANT_WRITE (No se pudo escribir en disco)',
                8 => 'UPLOAD_ERR_EXTENSION (Extensión PHP detuvo la subida)',
            ];
            if (isset($_FILES['test_file']['error']) && isset($errors[$_FILES['test_file']['error']])) {
                echo "<p><strong>Explicación:</strong> " . $errors[$_FILES['test_file']['error']] . "</p>";
            }
        }
    }
}
?>

<hr>
<h2>Formulario de Prueba</h2>
<form action="test_upload.php" method="post" enctype="multipart/form-data">
    <label>Selecciona un archivo (intenta con la imagen que falla):</label><br>
    <input type="file" name="test_file" required><br><br>
    <button type="submit" style="padding: 10px 20px; font-size: 16px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">Subir Archivo de Prueba</button>
</form>
