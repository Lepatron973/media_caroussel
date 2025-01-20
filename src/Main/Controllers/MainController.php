<?php

namespace AudioCarousel\Main\Controllers;

class MainController {

    public static function audio_carousel_enqueue_assets() {
        // Swiper.js
        wp_enqueue_style('swiper-css', 'https://unpkg.com/swiper/swiper-bundle.min.css');
        wp_enqueue_script('swiper-js', 'https://unpkg.com/swiper/swiper-bundle.min.js', array(), null, true);
    
        // Plugin styles et scripts
        wp_enqueue_style('audio-carousel-style', plugins_url('Public/assets/style.css', dirname(__DIR__)));
        wp_enqueue_script('audio-carousel-script', plugins_url('Public/assets/script.js', dirname(__DIR__)), array('jquery', 'swiper-js'), null, true);
    }

    // Cette méthode sera utilisée pour afficher la section avec le carrousel audio
    public static function display_media_section() {
        // Récupérer les fichiers audio depuis les options du plugin
        $audios = \AudioCarousel\Admin\Controllers\AdminController::get_all_audios();
        // Inclure la vue et passer les données nécessaires (fichiers audio)
        include dirname(plugin_dir_path(__FILE__),2) . '/Main/Views/AudioSection.php';
    }

   
}