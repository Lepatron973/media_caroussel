<div class="wrap">
    <h1><?php esc_html_e('Média Carousel Settings', 'audio-carousel'); ?></h1>

    <!-- Formulaire pour ajouter un nouvel audio -->
    <form method="post" action="" enctype="multipart/form-data">
    <?php wp_nonce_field('audio_carousel_upload', 'audio_carousel_nonce'); ?>
    <h2><?php esc_html_e('Ce plugin permet d\'ajouter un caroussel video / audio à l\'endroit shouaité via un shortcode. Vous pourrez ainsi ajouter différents médias tels que les vidéos ou les audios.
', 'audio-carousel'); ?></h2>
    <h2><?php esc_html_e('Le plugin est aussi utilisable via un shortcode, copiez ce code sur la page de confirmation de commande que vous avec choisis: [audio_caroussel]', 'audio-carousel'); ?></h2>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="audio_title"><?php esc_html_e('Titre du média', 'audio-carousel'); ?></label>
            </th>
            <td>
                <input type="text" name="audio_title" id="audio_title" class="regular-text" required>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="audio_file"><?php esc_html_e('Télécharger un média', 'audio-carousel'); ?></label>
            </th>
            <td>
                <input type="file" name="audio_file" id="audio_file" class="regular-text" accept="video/*" required>
            </td>
        </tr>
    </table>
    <?php submit_button(__('Enregistrer', 'audio-carousel')); ?>
</form>



    <hr>
    <!-- Liste des audios existants -->
    <h2><?php esc_html_e('Médias existant', 'audio-carousel'); ?></h2>
    <?php
$audios = \AudioCarousel\Admin\Controllers\AdminController::get_all_audios();

if (!empty($audios)): ?>
    <table class="widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Title', 'audio-carousel'); ?></th>
                <th><?php esc_html_e('Audio File', 'audio-carousel'); ?></th>
                <th><?php esc_html_e('Actions', 'audio-carousel'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($audios as $index => $audio): ?>
                <tr>
                    <td><?php echo esc_html($audio['title']); ?></td>
                    <td>
                        <a href="<?php echo esc_url($audio['file_url']); ?>" target="_blank">
                            <?php echo esc_html($audio['file_url']); ?>
                        </a>
                    </td>
                    <td>
                        <form method="post" action="" style="display:inline;">
                            <?php wp_nonce_field('delete_audio', 'delete_audio_nonce'); ?>
                            <input type="hidden" name="audio_id" value="<?php echo esc_attr($audio['id']); ?>">
                            <input type="hidden" name="audio_title" value="<?php echo esc_attr($audio['file_url']); ?>">
                            <input type="submit" name="delete_audio" class="button button-link-delete" value="<?php esc_attr_e('Delete', 'audio-carousel'); ?>">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p><?php esc_html_e('Aucun média présent.', 'audio-carousel'); ?></p>
<?php endif; ?>

</div>

