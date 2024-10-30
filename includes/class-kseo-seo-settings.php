<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Main KadsSeo Class.
 *
 * @class KadsSeo
 * @version	1.1.0
 */
final class KadsSeoSettings {

    /**
     * KadsSeo version.
     *
     * @var string
     */
    public $_version = '1.0.0';
    private $_meta;
    private $_general_settings;
    private $_social_settings;
    private $page_general_settings = 'kads_seo_general_settings';
    private $page_social_settings = 'kads_seo_social_meta';
    public static $_instance;

    /**
     * Main KadsSeo Instance.
     *
     * Ensures only one instance of KadsSeo is loaded or can be loaded.
     *
     * @since 2.1
     * @static
     * @see WC()
     * @return KadsSeo - Main instance.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 2.1
     */
    public function __wakeup() {
        
    }

    /**
     * Auto-load in-accessible properties on demand.
     *
     * @param mixed $key Key name.
     * @return mixed
     */
    public function __get($key) {
        
    }

    /**
     * KadsSeo Constructor.
     */
    public function __construct() {

        if (is_admin() || ( defined('WP_CLI') && WP_CLI )) {
            $this->init_hooks();
        }
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     * @return bool
     */
    private function is_request($type) {
        switch ($type) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined('DOING_AJAX');
            case 'cron' :
                return defined('DOING_CRON');
            case 'frontend' :
                return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        
    }

    public function set_meta() {
        $post_title = '';
        $post_content = '';
        $social_description = '';
        $meta_keywords = '';
        if (isset($_GET['post'])) {
            $id = absint($_GET['post']);
            $post = get_post($id);
            $post_title = kseo_text_limit($post->post_title, 80);
            $post_content = kseo_text_limit($post->post_content, 320);
            $social_description = kseo_text_limit($post->post_content, 1000);

            $meta_keywords = get_option('_kseo_general_meta_keywords');
        }
        $this->_meta = kseo_params_meta($post_title, $meta_keywords, $post_content, $social_description);

        $this->_general_settings = kseo_params_general_settings();
        $this->_social_settings = kseo_params_social_settings();
    }

    /**
     * Init KadsSeo when WordPress Initialises.
     */
    public function init() {
        // Set up localisation.
        $this->load_plugin_textdomain();
    }

    /**
     * Hook into actions and filters.
     *
     * @since 2.3
     */
    private function init_hooks() {
        $this->set_meta();
        add_action('admin_init', array($this, 'init'));
        add_action('admin_menu', array($this, 'plugin_menu'));

        if (isset($_POST['kseo_action'])) {
            switch ($_POST['kseo_action']) {
                case 'general_settings':
                    $this->save_general_settings();
                    break;
                case 'social_settings':
                    $this->save_social_settings();
                    break;

                default:
                    break;
            }
        }
    }

    public function save_general_settings() {
        foreach ($this->_general_settings as $group) {
            foreach ($group['items'] as $k => $option) {
                if (isset($_POST[$k])) {
                    update_option($k, $_POST[$k]);
                } else {
                    delete_option($k);
                }
            }
        }
    }

    public function save_social_settings() {
        foreach ($this->_social_settings as $group) {
            foreach ($group['items'] as $k => $option) {
                if (isset($_POST[$k])) {
                    update_option($k, $_POST[$k]);
                } else {
                    delete_option($k);
                }
            }
        }
    }

    public function plugin_menu() {

        add_menu_page(__('Kads Seo', 'kseo'), __('Kads Seo', 'kseo'), 'manage_options', $this->page_general_settings, array($this, 'general_settings'), 'dashicons-image-filter', 2);

        $page_general_settings = add_submenu_page($this->page_general_settings, __('General Settings', 'kseo'), __('General Settings', 'kseo'), 'manage_options', $this->page_general_settings, array($this, 'general_settings'));
        add_action("admin_print_scripts-$page_general_settings", array($this, 'loadjs_admin_head'));

        $page_social_meta = add_submenu_page($this->page_general_settings, __('Social Meta', 'kseo'), __('Social Meta', 'kseo'), 'manage_options', $this->page_social_settings, array($this, 'social_meta'));
        add_action("admin_print_scripts-$page_social_meta", array($this, 'loadjs_admin_head'));

//        $page_xml_sitemap = add_submenu_page($this->page_general_settings, __('XML Sitemap', 'kseo'), __('XML Sitemap', 'kseo'), 'manage_options', 'kads_seo_xml_sitemap', array($this, 'xml_sitemap'));
//        add_action("admin_print_scripts-$page_xml_sitemap", array($this, 'loadjs_admin_head'));

        add_action('admin_print_scripts', array($this, 'loadjs_admin_post_head'));
        if ($this->_meta) {
            add_action('add_meta_boxes', array($this, 'add_metaboxes'));
            add_action('save_post', array($this, 'save_meta'), 1, 2); // save the custom fields
        }
    }

    public function options_set() {
        ?>
        <div class="kseo-post-meta">
            <?php
            wp_nonce_field(basename(__FILE__), 'kseo_nonce');
            $tab_contents = array();
            $tab_titles = array();
            foreach ($this->_meta as $key => $params) {
                if (!isset($params['type'])) {
                    continue;
                }
                $type = $params['type'];
                if ($type == 'group') {
                    if (!isset($params['items'])) {
                        continue;
                    }
                    $tab_titles[$key] = $params['label'];
                    $tab_contents[$key] = $params['items'];
                }
            }
            ?>
            <div class="kads-seo-tab-groups">
                <?php
                if ($tab_titles) {
                    $active = ' active';
                    ?>
                    <div class="kads-seo-tab-wrapper">
                        <?php
                        foreach ($tab_titles as $key => $value) {
                            ?>
                            <a class="kads-seo-tab-control<?php echo $active ?>" action="<?php echo $key ?>"><?php echo $value ?></a>
                            <?php
                            $active = '';
                        }
                        ?>
                    </div>  
                    <?php
                }
                if ($tab_contents) {
                    $active = ' active';
                    foreach ($tab_contents as $key => $items) {
                        ?>
                        <div class="kads-seo-tab-content kads-seo-tab-<?php echo $key . $active ?>">
                            <?php
                            foreach ($items as $k => $item) {
                                $this->set_control_html($k, $item);
                            }
                            ?>
                        </div>    
                        <?php
                        $active = '';
                    }
                }
                ?>
            </div>
        </div>       
        <?php
    }

    public function set_control_html($k, $item) {
        global $post;
        $type = $item['type'];
        $fnName = 'kseo_controls_' . $type;
        $value = get_post_meta($post->ID, $k, true);
        if (function_exists($fnName)) {
            call_user_func($fnName, $k, $item, $value);
        }
    }

    /**
     * Save the Metabox Data
     *
     * @since 1.0.0
     */
    public function save_meta($post_id, $post) {

        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        if (!wp_verify_nonce($_POST['kseo_nonce'], basename(__FILE__))) {
            return $post_id;
        }

        // Is the user allowed to edit the post or page?
        if (!current_user_can('edit_post', $post->ID)) {
            return $post_id;
        }
        $projects_meta = array();


        // OK, we're authenticated: we need to find and save the data
        // We'll put it into an array to make it easier to loop though.
        foreach ($this->_meta as $key => $option) {
            $type = $option['type'];
            if ($type == 'group') {
                if ($option['items']) {
                    $items = $option['items'];
                    foreach ($items as $k => $value) {
                        if (isset($_POST[$k])) {
                            $projects_meta[$k] = kseo_set_controls_value($value['type'], $_POST[$k]);
                        }
                    }
                }
            }
        }
        // Add values of $projects_meta as custom fields
        foreach ($projects_meta as $key => $value) { // Cycle through the $projects_meta array!
            if ($post->post_type == 'revision') {
                return; // Don't store custom data twice
            }
            $value = implode(',', (array) $value); // If $value is an array, make it a CSV (unlikely)
            if (get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
                update_post_meta($post->ID, $key, $value);
            } else { // If the custom field doesn't have a value
                add_post_meta($post->ID, $key, $value);
            }
            if (!$value) {
                delete_post_meta($post->ID, $key); // Delete if blank
            }
        }
    }

    public function general_settings() {
        $args = array(
            'urlpost' => 'admin.php?page=' . $this->page_general_settings,
            'kseo_controls' => $this->_general_settings,
            'kseo_version' => $this->_version
        );
        $this->get_template('general', $args);
    }

    public function social_meta() {
        $args = array(
            'urlpost' => 'admin.php?page=' . $this->page_social_settings,
            'kseo_controls' => $this->_social_settings,
            'kseo_version' => $this->_version
        );
        $this->get_template('social', $args);
    }

    public function xml_sitemap() {
        $this->get_template('sitemap');
    }

    public function loadjs_admin_head() {
        wp_enqueue_style('auto-hads-admin-style', kseo_get_file_uri('css/admin-style.css'));
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('kseo-settings', kseo_get_file_uri('js/kseo-settings.js'), array('jquery', 'wp-color-picker', 'jquery-form'), '2.1.2', true);
        $params = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajax_kseo' => wp_create_nonce('kseo_' . kseo_get_file()),
        );
        wp_localize_script('kseo-meta-box', 'kseo_cf', $params);
    }

    public function loadjs_admin_post_head() {
        wp_enqueue_style('kseo_meta_box_styles', kseo_get_file_uri('css/meta-box.css'), array(), '1.0');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('kseo-meta-box', kseo_get_file_uri('js/kseo-meta-box.js'), array('jquery', 'wp-color-picker', 'jquery-form'), '2.1.2', true);
        $params = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajax_kseo' => wp_create_nonce('kseo_' . kseo_get_file()),
        );
        wp_localize_script('kseo-meta-box', 'kseo_cf', $params);
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

    /**
     * How to Add a Metabox to a Custom Post Type
     * Add the Kads SEO Meta Boxes
     * Source: http://wptheming.com/2010/08/custom-metabox-for-post-type/
     *
     * @since 1.0.0
     */
    public function add_metaboxes() {
        if ($this->_meta) {
            $post_types = get_post_types();
            foreach ($post_types as $key => $val) {
                switch ($key) {
                    case 'attachment':
                    case 'revision':
                    case 'nav_menu_item':
                    case 'custom_css':
                    case 'oembed_cache':
                    case 'customize_changeset':
                        break;
                    default:
                        add_meta_box('kseo_seo_options', 'Kads SEO information', array($this, 'options_set'), $key, 'normal', 'high');
                        break;
                }
            }
        }
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Locales found in:
     *      - WP_LANG_DIR/KadsSeo/KadsSeo-LOCALE.mo
     *      - WP_LANG_DIR/plugins/KadsSeo-LOCALE.mo
     */
    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'kseo');

        unload_textdomain('kseo');
        load_textdomain('kseo', WP_LANG_DIR . '/kads-seo/kseo-' . $locale . '.mo');
        load_plugin_textdomain('kseo', false, kseo_get_file('i18n/languages'));
    }

}
