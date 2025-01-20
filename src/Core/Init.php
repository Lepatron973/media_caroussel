<?php

namespace AudioCarousel\Core;

use AudioCarousel\Admin\Controllers\AdminController;
use AudioCarousel\Main\Controllers\MainController;

class Init {
    public static function register_services() {
        // Initialiser les contrôleurs administratifs
        add_action('admin_menu', [AdminController::class, 'init_settings_page']);
        add_action('admin_init', [AdminController::class, 'audio_carousel_register_settings']);
        //ajouter les scripts du caroussel
        add_action('wp_enqueue_scripts', [MainController::class, 'audio_carousel_enqueue_assets']);
        add_shortcode('audio_caroussel', [MainController::class, 'display_media_section']);
    }
}
