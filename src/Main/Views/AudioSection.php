<div class="swiper-container">
    
    <div class="swiper-wrapper" id="swiper-wrapper">
        <?php if ( ! empty( $audios ) ): ?>
            <?php
        
                $nbToDisplay = 3;
                
                $list = ceil(count($audios) / $nbToDisplay); // Nombre total de groupes de 3
                $audioCount = count($audios);
                for ($i=0; $i < $list; $i++): ?>
                <div class="swiper-slide">
                    <div class="e-con-inner">
                        <div class="elementor-element elementor-element-47c3611 e-grid e-con-boxed e-con e-child" data-id="47c3611" data-element_type="container">
                            <div class="elementor-element elementor-element-be5272c e-grid e-con-full e-con e-child" data-id="be5272c" data-element_type="container">
                                <?php
                                // Calculer le début et la fin du groupe
                                $startIndex = $i * $nbToDisplay; // L'index de départ pour cette itération
                                $endIndex = min($startIndex +  $nbToDisplay, $audioCount); // L'index de fin (sans dépasser le total)

                                // Boucle à l'intérieur du groupe actuel
                                for ($b = $startIndex; $b < $endIndex; $b++): ?>
                                    <div class="elementor-element elementor-element-acbb8b6 elementor-widget elementor-widget-video" data-id="acbb8b6" data-element_type="widget" data-settings="{&quot;video_type&quot;:&quot;hosted&quot;,&quot;controls&quot;:&quot;yes&quot;}" data-widget_type="video.default">
                                        <div class="elementor-widget-container">
                                            <div class="e-hosted-video elementor-wrapper elementor-open-inline">
                                                <video class="elementor-video mtz-vlc-mkjjc" src="<?= $audios[$b]['file_url']; ?>" controls="" preload="metadata" controlslist="nodownload"></video>
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        <?php else: ?>
            <p><?php esc_html_e( 'Aucun média à afficher.', 'audio-carousel' ); ?></p>
        <?php endif; ?>
    </div> <!-- swiper-wrapper -->
    
    <!-- Navigation et pagination -->
    <div class="swiper-pagination swiper-nav"></div>
    <div class="swiper-button-next swiper-nav"></div>
    <div class="swiper-button-prev swiper-nav"></div>
</div> <!-- swiper-container -->

<script type="text/javascript">
         // Récupérer les audios via PHP et les rendre disponibles en JavaScript
const audios = <?= json_encode($audios); ?>;
</script>
