<?php

namespace AudioCarousel\Admin\Controllers;

class AdminController {

    // Initialiser la page des paramètres
    public static function init_settings_page() {
        add_menu_page(
            'Média Carousel Settings', // Titre de la page
            'Média Carousel',          // Texte du menu
            'manage_options',          // Capacité requise
            'audio-carousel',          // Slug de la page
            [self::class, 'render_settings_page'], // Fonction de rendu
            'dashicons-controls-volumeon', // Icône du menu
            99                          // Position dans le menu
        );

        // Ajouter un sous-menu pour la liste des audios
        // self::audio_carousel_add_admin_submenu();
    }

    // Rendu de la page principale des paramètres
    public static function render_settings_page() {
        self::handle_audio_submission();
        include dirname(plugin_dir_path(__FILE__), 2) . '/Admin/Views/SettingsPage.php';
    }

    // Ajouter un sous-menu pour la liste des audios
    // public static function audio_carousel_add_admin_submenu() {
    //     add_submenu_page(
    //         'audio-carousel',                      // Slug du parent
    //         __('List of Audios', 'audio-carousel'), // Titre de la page
    //         __('Audio List', 'audio-carousel'),     // Texte du menu
    //         'manage_options',                      // Capacité requise
    //         'audio-carousel-list',                 // Slug du sous-menu
    //         [self::class, 'render_audio_list_page'] // Fonction de rendu
    //     );
    // }

    // Rendu de la page "List of Audios"
    public static function render_audio_list_page() {
        self::handle_audio_submission();
        include dirname(plugin_dir_path(__FILE__), 2) . '/Admin/Views/AudioListPage.php';
    }

    // Enregistrement des paramètres
    public static function audio_carousel_register_settings() {
        // Enregistre un groupe de paramètres
        register_setting('audio_carousel_settings', 'audio_carousel_options');

        // Ajouter une section
        add_settings_section(
            'audio_carousel_general_section',               // ID de la section
            __('General Settings', 'audio-carousel'),       // Titre
            [self::class, 'general_section_callback'],      // Fonction de rappel
            'audio-carousel'                                // Slug de la page
        );

        // Ajouter des champs dans la section
        add_settings_field(
            'audio_carousel_autoplay',                      // ID
            __('Enable Autoplay', 'audio-carousel'),        // Label
            [self::class, 'autoplay_field_callback'],       // Fonction de rendu du champ
            'audio-carousel',                               // Slug de la page
            'audio_carousel_general_section'                // ID de la section
        );

        add_settings_field(
            'audio_carousel_autoplay_delay',
            __('Autoplay Delay (ms)', 'audio-carousel'),
            [self::class, 'autoplay_delay_field_callback'],
            'audio-carousel',
            'audio_carousel_general_section'
        );

        add_settings_field(
            'audio_carousel_audio_files',
            __('Audio Files', 'audio-carousel'),
            [self::class, 'audio_files_field_callback'],
            'audio-carousel',
            'audio_carousel_general_section'
        );
    }

    // Fonction de rappel pour la section
    public static function general_section_callback() {
        echo '<p>' . __('Configure the general settings for the Audio Carousel.', 'audio-carousel') . '</p>';
    }

    // Fonction de rappel pour le champ "Enable Autoplay"
    public static function autoplay_field_callback() {
        $options = get_option('audio_carousel_options');
        $autoplay = isset($options['autoplay']) ? $options['autoplay'] : '';
        echo '<input type="checkbox" name="audio_carousel_options[autoplay]" value="1" ' . checked(1, $autoplay, false) . '>';
    }

    // Fonction de rappel pour le champ "Autoplay Delay"
    public static function autoplay_delay_field_callback() {
        $options = get_option('audio_carousel_options');
        $delay = isset($options['autoplay_delay']) ? $options['autoplay_delay'] : 3000;
        echo '<input type="number" name="audio_carousel_options[autoplay_delay]" value="' . esc_attr($delay) . '" min="1000">';
    }

    // Fonction de rappel pour le champ "Audio Files"
    public static function audio_files_field_callback() {
        $options = get_option('audio_carousel_options');
        $audio_files = isset($options['audio_files']) ? $options['audio_files'] : [];
        echo '<textarea name="audio_carousel_options[audio_files]" rows="5" cols="50">' . esc_textarea(implode("\n", $audio_files)) . '</textarea>';
        echo '<p>' . __('Enter one audio file URL per line.', 'audio-carousel') . '</p>';
    }
    public static function insert_audio($title, $file_url) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'audio_carousel';
        
        $wpdb->insert(
            $table_name,
            [
                'title' => sanitize_text_field($title),
                'file_url' => esc_url_raw($file_url),
            ],
            ['%s', '%s']
        );
    }
    public static function get_all_audios() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'audio_carousel';
    
        return $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A);
    }
    public static function delete_audio($id,$title) {
        unlink(WP_CONTENT_DIR."/uploads/audio_caroussel/".basename($title));
        global $wpdb;
        $table_name = $wpdb->prefix . 'audio_carousel';
        
        $wpdb->delete($table_name, ['id' => intval($id)], ['%d']);
       
    }
        
    public static function custom_upload_subdirectory($upload) {
        // Nom du sous-répertoire
        $subdir = '/audio_caroussel';
    
        // Modifier les chemins et URL
        $upload['subdir'] = $subdir;
        $upload['path']   = $upload['basedir'] . $subdir;
        $upload['url']    = $upload['baseurl'] . $subdir;
    
        return $upload;
    }
    
    
    
    public static function handle_audio_submission() {
        
        add_filter('upload_dir', [self::class,"custom_upload_subdirectory"]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Vérifiez le nonce
           
            if (isset($_POST['audio_carousel_nonce']) || isset($_POST['delete_audio_nonce'])) {

               
                if ( isset($_POST['audio_carousel_nonce']) && wp_verify_nonce($_POST['audio_carousel_nonce'], 'audio_carousel_upload')) {
                    // Vérifiez si un fichier est bien téléversé
                    if (isset($_FILES['audio_file']) && !empty($_FILES['audio_file']['name'])) {
                        if (!function_exists('wp_handle_upload')) {
                            require_once(ABSPATH . 'wp-admin/includes/file.php');
                        }
    
                        $upload_overrides = ['test_form' => false];
    
                        // Gérer le téléversement
                        $uploaded_file = wp_handle_upload($_FILES['audio_file'], $upload_overrides);
                        if (isset($uploaded_file['file'])) {
                            $file_url = $uploaded_file['url'];
                            $title = sanitize_text_field($_POST['audio_title']);
    
                            // Enregistrer dans la base de données
                            global $wpdb;
                            $table_name = $wpdb->prefix . 'audio_carousel';
                            $wpdb->insert(
                                $table_name,
                                [
                                    'title' => $title,
                                    'file_url'  => $file_url,
                                ],
                                [
                                    '%s',
                                    '%s',
                                ]
                            );
    
                            echo '<div class="updated"><p>' . esc_html__('Audio uploaded successfully!', 'audio-carousel') . '</p></div>';
                        } else {
                            echo '<div class="error"><p>' . esc_html__('Error uploading file: '. $uploaded_file["error"], 'audio-carousel') . '</p></div>';
                        }
                    } else {
                        echo '<div class="error"><p>' . esc_html__('No file uploaded.', 'audio-carousel') . '</p></div>';
                    }
                    if (isset($_POST['audio_title'], $_POST['audio_file'])) {
                        self::insert_audio($_POST['audio_title'], $_POST['audio_file']);
                        add_settings_error('audio_carousel', 'audio_added', __('Audio added successfully.', 'audio-carousel'), 'success');
                    }
                }

                if (isset($_POST['delete_audio_nonce']) && wp_verify_nonce($_POST['delete_audio_nonce'], 'delete_audio')) {
                    if (isset($_POST['audio_id'])) {
                        self::delete_audio($_POST['audio_id'],$_POST['audio_title']);
                        add_settings_error('audio_carousel', 'audio_deleted', __('Audio deleted successfully.', 'audio-carousel'), 'success');
                    }
                }
            }
            
        }
        remove_filter('upload_dir', [self::class,"custom_upload_subdirectory"]);
    }
    
}
