<?php ?>
<div class="wrap">
    <h2><?php esc_html_e('Kads SEO Social Options', 'kseo'); ?></h2>

    <div id="wpcom-stats-meta-box-container" class="metabox-holder">
        <?php
        wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
        wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
        ?>
        <div class="postbox-container-kads-seo postbox-kads-seo" style="width: 100%;">
            <div id="normal-sortables" class="meta-box-sortables ui-sortable kads-seo-postbox-warp clearfix">
                <form name="kads-seoconf" id="kads-seo-conf" action='<?php echo $urlpost ?>' method='post' >
                    <div id="referrers" class="postbox kads-seo-has-sidebar">
                        <h3 class="hndle"><span><?php esc_html_e('SEO Social Settings', 'kseo'); ?></span></h3>
                        <div class="inside">
                            <div class="kads-seo-row">
                               <?php
                               if(isset($kseo_controls)){
                                   kseo_run_controls_settings($kseo_controls);
                               }
                               ?>
                            </div>
                            <div class="message">
                                <span class="message-url"></span>
                                <span class="kads-seo-content-url"></span>
                            </div>
                            <div class="kads-seo-row">
                                <input type="hidden" name="kseo_action" value="social_settings">
                                <input type="submit" name="submit" class="button button-primary" value="<?php _e('Save Options', 'kseo') ?>" /> 
                            </div>
                        </div>
                    </div>
                    <div class="kads-seo-sidebar">
                        <?php kseo_sidebar($kseo_version); ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="kads-seo-test-data kads-seo-panel">
    <div class="kads-seo-panel-overplay"></div>
    <div class="kads-seo-test-data-warp">
        <button type="button" class="kads-seo-close"><span class="dashicons dashicons-no-alt"></span></button>
        <div class="kads-seo-test-data-content">

        </div>
    </div>
</div>
<div class="kads-seo-message-data kads-seo-panel">
    <div class="kads-seo-panel-overplay"></div>
    <div class="kads-seo-message-warp">
        <div class="kads-seo-message-data-content">
        </div>
    </div>
</div>
<div class="kads-seo-loading loading-url">
    <div class="kads-seo-loading-warp">
        <div class="kads-seo-loading-content">
            <img class="loading-img" src="<?php echo kseo_get_file_uri('images/loading.gif'); ?>" >
            <span><?php _e('Loading data...', 'kseo') ?></span>
        </div>
    </div>
</div>