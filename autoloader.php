<?php
/**
 * Autoloader pour charger automatiquement les classes du plugin.
 *
 * @param string $class_name Le nom complet de la classe avec namespace.
 */
function audio_carousel_autoloader($class_name) {
    // Vérifie si le namespace commence par "AudioCarousel"
    if (strpos($class_name, 'AudioCarousel\\') === 0) {
        // Convertir le namespace en chemin de fichier
        $relative_class = str_replace('AudioCarousel\\', '', $class_name);
        $file = __DIR__ . '/src/' . str_replace('\\', '/', $relative_class) . '.php';

        // Inclure le fichier s'il existe
        if (file_exists($file)) {
            require_once $file;
        }
    }
}

// Enregistre l'autoloader
spl_autoload_register('audio_carousel_autoloader');
