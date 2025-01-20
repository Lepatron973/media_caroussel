<?php

/**
 * Plugin Name: Média Carousel
 * Plugin URI: https://github.com/Lepatron973/media_caroussel
 * Description: A WordPress plugin for adding audio carousels.
 * Version: 0.6
 * Author: Lepatron973
 * Author URI: https://sunitronics.fr
 * Text Domain: media-carousel
 * Github Plugin URI: https://github.com/Lepatron973/media_caroussel
 */

defined('ABSPATH') || exit;

if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'updater.php';
    new GitHub_Updater(__FILE__, 'Lepatron973', 'media_caroussel');
}

// Inclure l'autoloader personnalisé
require_once __DIR__ . '/autoloader.php';

use AudioCarousel\Core\Init;

// Initialiser les services du plugin
function audio_carousel_bootstrap()
{
    Init::register_services();
}
add_action('plugins_loaded', 'audio_carousel_bootstrap');

//création de la bdd lors de l'ativation du plugin
register_activation_hook(__FILE__, 'audio_carousel_create_table');

function audio_carousel_create_table()
{
    //creation du sous dossier dans upload
    if (!is_dir(dirname(plugin_dir_path(__FILE__), 2) . "/uploads/audio_caroussel")) {
        mkdir(dirname(plugin_dir_path(__FILE__), 2) . "/uploads/audio_caroussel");
    }

    global $wpdb;

    $table_name = $wpdb->prefix . 'audio_carousel';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        file_url TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Fonction pour supprimer récursivement un dossier et son contenu
function deleteFolderRecursively($folderPath)
{
    if (is_dir($folderPath)) {
        $items = array_diff(scandir($folderPath), ['.', '..']); // Exclure '.' et '..'
        foreach ($items as $item) {
            $itemPath = $folderPath . DIRECTORY_SEPARATOR . $item;
            if (is_dir($itemPath)) {
                deleteFolderRecursively($itemPath); // Supprimer les sous-dossiers
            } else {
                unlink($itemPath); // Supprimer les fichiers
            }
        }
        rmdir($folderPath); // Supprimer le dossier une fois qu'il est vide
    }
}
//suppression de la bdd lors de la désactivation

register_uninstall_hook(__FILE__, 'audio_carousel_remove_table');

function audio_carousel_remove_table()
{
    //suppression du sous dossier dans uploads
    if (is_dir(dirname(plugin_dir_path(__FILE__), 2) . "/uploads/audio_caroussel")) {
        deleteFolderRecursively(dirname(plugin_dir_path(__FILE__), 2) . "/uploads/audio_caroussel");
    }
    global $wpdb;

    $table_name = $wpdb->prefix . 'audio_carousel';

    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}


//  function audio_carousel_general_section_callback() {
//     echo '<p>' . __('Configure the general settings for the Audio Carousel.', 'audio-carousel') . '</p>';
// }