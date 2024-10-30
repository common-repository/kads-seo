
<div class="wrap about-wrap">
    <h1><?php printf(esc_html__('Welcome to Kads SEO %s', 'kseo'), $version); ?></h1>
    <div
        class="about-text"><?php printf(esc_html__('Kads SEO %s contains new features, bug fixes, increased security, and tons of under the hood performance improvements.', 'kseo'), $version); ?></div>

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active" id="kseo-about"
           href="<?php echo esc_url(admin_url(add_query_arg(array('page' => 'kseo-about'), 'index.php'))); ?>">
               <?php esc_html_e('What&#8217;s New', 'kseo'); ?>
        </a>
    </h2>


    <div id='sections'>
        <section>
            <div id="welcome-panel" class="">
                <div class="welcome-panel-content">
                    <div class="welcome-panel-column-container">
                        <div class="welcome-panel-column">
                            <h3><?php echo esc_html(__('Support Kads SEO', 'kseo')); ?></h3>
                            <p class="message"><?php echo esc_html(__('There are many ways you can help support Kads SEO.', 'kseo')); ?></p>
                            <p class="message kseo-message"><?php echo esc_html(__('Upgrade to Kads SEO Pro to access priority support and premium features.', 'kseo')); ?></p>
                            
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

</div>