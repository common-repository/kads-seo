<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('KadsSeoWelcome')) {

    /**
     * Class KadsSeoWelcome
     */
    // @codingStandardsIgnoreStart
    class KadsSeoWelcome {

        // @codingStandardsIgnoreEnd

        private $version = '1.0.0';

        /**
         * Constructor to add the actions.
         */
        function __construct() {

            if (is_admin() || ( defined('WP_CLI') && WP_CLI )) {
                add_action('admin_menu', array($this, 'add_menus'));
                add_action( 'admin_init', array($this, 'run_welcome') );
                add_action('admin_menu', array($this, 'remove_pages'), 999);
            }
        }

        public function run_welcome() {
            if ( get_transient( '_kseo_activation_redirect' ) ) {
                delete_transient( '_kseo_activation_redirect' );
                $this->init( true );
            }
            
        }
        /**
         * Enqueues style and script.
         *
         * @param $hook
         */
        function welcome_screen_assets($hook) {

            if ('dashboard_page_kseo-about' === $hook) {

                wp_enqueue_style('kads-seo-welcome-css', kseo_get_file_uri('css/welcome.css'), array(), $this->version);
                wp_enqueue_script('kads-seo-welcome-css', kseo_get_file_uri('js/welcome.js'), array('jquery'), $this->version, true);
            }
        }

        /**
         * Removes unneeded pages.
         *
         * @since 2.3.12 Called via admin_menu action instead of admin_head.
         */
        function remove_pages() {
            remove_submenu_page('index.php', 'kseo-about');
        }

        /**
         * Adds (hidden) menu.
         */
        function add_menus() {
            $page = add_dashboard_page(__('Welcome to Kads SEO', 'kseo'), __('Welcome to Kads SEO', 'kseo'), 'manage_options', 'kseo-about', array($this, 'about_screen'));
            add_action("admin_print_scripts-$page", array($this, 'welcome_screen_assets'));
        }

        /**
         * Initial stuff.
         *
         * @param bool $activate
         */
        function init($activate = false) {

            if (!is_admin()) {
                return;
            }

            // Bail if activating from network, or bulk
            if (is_network_admin() || isset($_GET['activate-multi'])) {
                return;
            }

            if (!current_user_can('manage_options')) {
                return;
            }

            wp_cache_flush();

            delete_transient('_kseo_activation_redirect');

            $seen = get_user_meta(get_current_user_id(), '_kseo_seen_about_page', true);

            update_user_meta(get_current_user_id(), '_kseo_seen_about_page', $this->version);


            if (( $this->version === $seen ) || ( true !== $activate )) {
                return;
            }

            wp_safe_redirect(add_query_arg(array('page' => 'kseo-about'), admin_url('index.php')));
            exit;
        }

        /**
         * Outputs the about screen.
         */
        function about_screen() {
            $args = array(
                'version' => $this->version
            );
            $this->get_template('welcome', $args);
        }

        public function get_template($name, array $args = array()) {
            foreach ($args AS $key => $val) {
                $$key = $val;
            }
            $file = kseo_get_file('themes/' . $name . '.php');
            if (file_exists($file)) {
                include( $file );
            }
        }

    }
    $KadsSeoWelcome = new KadsSeoWelcome();
}
