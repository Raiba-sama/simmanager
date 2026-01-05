<?php
// Test de l'extension intl dans le contexte Apache
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Extension intl</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Test Extension intl dans Apache</h1>
    
    <h2>1. Extension chargée ?</h2>
    <?php if (extension_loaded('intl')): ?>
        <p class="success">✓ Extension intl chargée</p>
    <?php else: ?>
        <p class="error">✗ Extension intl NON chargée</p>
        <p>Extensions chargées: <?php echo implode(', ', get_loaded_extensions()); ?></p>
    <?php endif; ?>
    
    <h2>2. Classe NumberFormatter disponible ?</h2>
    <?php if (class_exists('NumberFormatter')): ?>
        <p class="success">✓ Classe NumberFormatter disponible</p>
        
        <h2>3. Test d'instanciation</h2>
        <?php
        try {
            $formatter = new NumberFormatter('fr_FR', NumberFormatter::CURRENCY);
            echo '<p class="success">✓ NumberFormatter peut être instancié</p>';
            echo '<p>Test formatage: ' . $formatter->format(1234.56) . '</p>';
        } catch (Exception $e) {
            echo '<p class="error">✗ Erreur lors de l\'instanciation: ' . $e->getMessage() . '</p>';
        }
        ?>
    <?php else: ?>
        <p class="error">✗ Classe NumberFormatter NON disponible</p>
    <?php endif; ?>
    
    <h2>Informations PHP</h2>
    <p>PHP Version: <?php echo PHP_VERSION; ?></p>
    <p>php.ini utilisé: <?php echo php_ini_loaded_file(); ?></p>
    <p>Extension dir: <?php echo ini_get('extension_dir'); ?></p>
    
    <h2>Logs PHP (dernières erreurs)</h2>
    <?php
    $error_log = ini_get('error_log');
    if ($error_log && file_exists($error_log)) {
        $lines = file($error_log);
        $recent_lines = array_slice($lines, -10);
        echo '<pre>' . htmlspecialchars(implode('', $recent_lines)) . '</pre>';
    } else {
        echo '<p>Aucun log PHP trouvé</p>';
    }
    ?>
</body>
</html>

