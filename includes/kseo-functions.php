<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
function kseo_upload_dir($dirs) {
    $dirs['subdir'] = '/kseo';
    $dirs['path'] = $dirs['basedir'] . '/kseo';
    $dirs['url'] = $dirs['baseurl'] . '/kseo';

    return $dirs;
}

function kseo_link_download($style = '', $echo = true) {
    global $kseo;
    $html = '';
    if ($kseo->_kseo_attachment_file) {
        $dirs = wp_upload_dir();
        if (file_exists($dirs['basedir'] . '/kseo/' . $kseo->_kseo_attachment_file)) {
            $link = $dirs['baseurl'] . '/kseo/' . $kseo->_kseo_attachment_file;
            $html = sprintf('<a title="%3$s" href="%1$s" class="kseo-button blue %2$s">%3$s</a>', $link, $style, __('Free Download', 'kseo'));
        }
    }
    if (empty($html)) {
        $html = sprintf('<a title="%2$s" href="#" class="kseo-button gray %1$s">%2$s</a>', $style, __('Comming Soon', 'kseo'));
    }
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

function kseo_file_upload() {
    check_ajax_referer('kseo_' . kseo_get_file(), 'ajax_kseo_nonce');

    if (!(is_array($_POST) && is_array($_FILES) && defined('DOING_AJAX') && DOING_AJAX)) {
        return;
    }
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
    $upload_overrides = array('test_form' => false);

    $response = array();
    $response['success'] = 0;
    foreach ($_FILES as $file) {
        add_filter('upload_dir', 'kseo_upload_dir');
        $file_info = wp_handle_upload($file, $upload_overrides);

        $dirs = wp_upload_dir();
        if (!file_exists($dirs['basedir'] . '/kseo/index.html')) {
            fopen($dirs['basedir'] . "/kseo/index.html", "w");
        }

        remove_filter('upload_dir', 'kseo_upload_dir');
        // do something with the file info...
        $response['success'] = 1;

        $info = array(
            'name' => basename($file_info['file']),
            'type' => $file_info['type']
        );

        wp_send_json_success($info);
    }
}

add_action('wp_ajax_kseo_add_data_file', 'kseo_file_upload');
add_action('wp_ajax_nopriv_kseo_add_data_file', 'kseo_file_upload');

function kseo_get_template($name, array $args = array()) {
    foreach ($args AS $key => $val) {
        $$key = $val;
    }
    $path = apply_filters('kseo_template', kseo_get_file('templates/'));

    $file = $path . $name . '.php';

    ob_start();
    if (file_exists($file)) {
        include( $file );
    }
    $return = ob_get_clean();
    return $return;
}

if (!function_exists('kseo_add_script')) {

    /**
     * Checks if the template is assigned to the page
     */
    function kseo_add_script($name) {
        global $kseo_ajax_url;
        $script_url = kseo_get_file_uri('js/' . $name);

        $script_name = 'kseo-' . kseo_string_Safe($name);
        wp_enqueue_script($script_name, $script_url, array('jquery'), FALSE, TRUE);
        if (!$kseo_ajax_url) {
            $kseo_ajax_url = true;

            wp_localize_script($script_name, 'kseo', array('ajaxurl' => admin_url('admin-ajax.php'))
            );
        }

        return $script_url;
    }

}
if (!function_exists('kseo_script_template')) {

    /**
     * Checks if the template is assigned to the page
     */
    function kseo_script_template($name, array $args = array(), $autoname = FALSE) {

        foreach ($args AS $key => $val) {
            $$key = $val;
        }
        $file = kseo_get_file('script-js/' . $name . '.php');
        ob_start();
        if (file_exists($file)) {

            include( $file );
        }
        $html = ob_get_contents();
        ob_end_clean();

        kseo_get_footer_script($html);
        $data = trim(preg_replace('#<script[^>]*>(.*)</script>#is', '$1', $html));
        return $data;
    }

}

if (!function_exists('kseo_string_safe')) {

    function kseo_string_Safe($string) {
        $trans = array(
            "đ" => "d", "ă" => "a", "â" => "a", "á" => "a", "à" => "a",
            "ả" => "a", "ã" => "a", "ạ" => "a",
            "ấ" => "a", "ầ" => "a", "ẩ" => "a", "ẫ" => "a", "ậ" => "a",
            "ắ" => "a", "ằ" => "a", "ẳ" => "a", "ẵ" => "a", "ặ" => "a",
            "é" => "e", "è" => "e", "ẻ" => "e", "ẽ" => "e", "ẹ" => "e",
            "ế" => "e", "ề" => "e", "ể" => "e", "ễ" => "e", "ệ" => "e",
            "í" => "i", "ì" => "i", "ỉ" => "i", "ĩ" => "i", "ị" => "i",
            "ư" => "u", "ô" => "o", "ơ" => "o", "ê" => "e",
            "Ư" => "u", "Ô" => "o", "Ơ" => "o", "Ê" => "e",
            "ú" => "u", "ù" => "u", "ủ" => "u", "ũ" => "u", "ụ" => "u",
            "ứ" => "u", "ừ" => "u", "ử" => "u", "ữ" => "u", "ự" => "u",
            "ó" => "o", "ò" => "o", "ỏ" => "o", "õ" => "o", "ọ" => "o",
            "ớ" => "o", "ờ" => "o", "ở" => "o", "ỡ" => "o", "ợ" => "o",
            "ố" => "o", "ồ" => "o", "ổ" => "o", "ỗ" => "o", "ộ" => "o",
            "ú" => "u", "ù" => "u", "ủ" => "u", "ũ" => "u", "ụ" => "u",
            "ứ" => "u", "ừ" => "u", "ử" => "u", "ữ" => "u", "ự" => "u",
            "ý" => "y", "ỳ" => "y", "ỷ" => "y", "ỹ" => "y", "ỵ" => "y",
            "Ý" => "Y", "Ỳ" => "Y", "Ỷ" => "Y", "Ỹ" => "Y", "Ỵ" => "Y",
            "Đ" => "D", "Ă" => "A", "Â" => "A", "Á" => "A", "À" => "A",
            "Ả" => "A", "Ã" => "A", "Ạ" => "A",
            "Ấ" => "A", "Ầ" => "A", "Ẩ" => "A", "Ẫ" => "A", "Ậ" => "A",
            "Ắ" => "A", "Ằ" => "A", "Ẳ" => "A", "Ẵ" => "A", "Ặ" => "A",
            "É" => "E", "È" => "E", "Ẻ" => "E", "Ẽ" => "E", "Ẹ" => "E",
            "Ế" => "E", "Ề" => "E", "Ể" => "E", "Ễ" => "E", "Ệ" => "E",
            "Í" => "I", "Ì" => "I", "Ỉ" => "I", "Ĩ" => "I", "Ị" => "I",
            "Ư" => "U", "Ô" => "O", "Ơ" => "O", "Ê" => "E",
            "Ư" => "U", "Ô" => "O", "Ơ" => "O", "Ê" => "E",
            "Ú" => "U", "Ù" => "U", "Ủ" => "U", "Ũ" => "U", "Ụ" => "U",
            "Ứ" => "U", "Ừ" => "U", "Ử" => "U", "Ữ" => "U", "Ự" => "U",
            "Ó" => "O", "Ò" => "O", "Ỏ" => "O", "Õ" => "O", "Ọ" => "O",
            "Ớ" => "O", "Ờ" => "O", "Ở" => "O", "Ỡ" => "O", "Ợ" => "O",
            "Ố" => "O", "Ồ" => "O", "Ổ" => "O", "Ỗ" => "O", "Ộ" => "O",
            "Ú" => "U", "Ù" => "U", "Ủ" => "U", "Ũ" => "U", "Ụ" => "U",
            "Ứ" => "U", "Ừ" => "U", "Ử" => "U", "Ữ" => "U", "Ự" => "U",);

        //remove any '-' from the string they will be used as concatonater
        $str = trim($string);
        $str = str_replace('-', ' ', $str);

        $str = strtr($str, $trans);

        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $str);

        // lowercase and trim
        $str = trim(strtolower($str));
        return $str;
    }

}

function kseo_the_blog_content() {
    $content = get_the_excerpt();
    echo kseo_gettext($content);
}

function kseo_text_limit($text, $max = 0) {
    $max_default = 320;
    $text = str_replace(']]>', ']]&gt;', $text);
    $text = preg_replace('|\[(.+?)\](.+?\[/\\1\])?|s', '', $text);
    $text = wp_strip_all_tags($text);
    // Treat other common word-break characters like a space.
    $text2 = preg_replace('/[,._\-=+&!\?;:*]/s', ' ', $text);
    if (!$max) {
        $max = $max_default;
    }
    $max_orig = $max;
    $len = str_word_count($text2);
    if ($max < $len) {
        if (function_exists('mb_strrpos')) {
            $pos = mb_strrpos($text2, ' ', - ( $len - $max ));
            if (false === $pos) {
                $pos = $max;
            }
            if ($pos > $max_default) {
                $max = $pos;
            } else {
                $max = $max_default;
            }
        } else {
            while (' ' != $text2[$max] && $max > $max_default) {
                $max --;
            }
        }

        // Probably no valid chars to break on?
        if ($len > $max_orig && $max < intval($max_orig / 2)) {
            $max = $max_orig;
        }
    }
    $text = kseo_substr($text, 0, $max);

    return trim($text);
}
function kseo_substr( $string, $start = 0, $length = 2147483647 ) {
        $args = func_get_args();
        if ( function_exists( 'mb_substr' ) ) {
                return call_user_func_array( 'mb_substr', $args );
        }

        return call_user_func_array( 'substr', $args );
}

function kseo_run_controls_settings($controls_meta = array()) {
    if ($controls_meta) {
        foreach ($controls_meta as $key => $params) {
            if (isset($params['type'])) {
                $fnName = 'kseo_controls_settings_' . $params['type'];
                if (function_exists($fnName)) {
                    call_user_func($fnName, $key, $params);
                }
            }
        }
    }
}

function kseo_sidebar($version = '') {
    ?>
    <div class="kads-seo-sidebar-box postbox">
        <h3 class="hndle"><span><?php esc_html_e('About', 'kseo'); ?><span class="Taha" style="float:right;">Version <b><?php echo esc_html($version) ?></b></span></span></h3>
        <div class="content-sidebar">
            <div class="tabs-panel">
                <h2>Kads SEO</h2>
                <ul>
                    <li>Advanced support for e-commerce</li>
                    <li>Video SEO Module</li>
                    <li>SEO for Categories, Tags and Custom Taxonomies</li>
                    <li>Access to Video Screencasts</li>
                    <li>Access to Premium Support Forums</li>
                    <li>Access to Knowledge Center</li>
                </ul>
                <a href="#">Click here</a> to file a feature request/bug report.
                <div class="kseo-socials">
                    <a class="dashicons di-twitter" target="_blank" href="https://twitter.com/huynhduy1985" title="Follow me on Twitter"></a>
                    <a class="dashicons di-facebook" target="_blank" href="https://www.facebook.com/huynhduy1985" title="Follow me on Facebook"></a>
                </div>
            </div>
        </div>  
    </div>
    <div class="kads-seo-sidebar-box postbox">
        <h3 class="hndle"><span><?php esc_html_e('Support', 'kseo'); ?></span></h3>
        <div class="content-sidebar">
            <div class="tabs-panel">
                <div class="kseo-support">
                    <a target="_blank" href="#"><span class="kseoicon dashicons dashicons-menu"></span><span>Read the Kads Seo user guide</span></a>
                    <a target="_blank" title="Plugin Support Forum" href="#"><span class="kseoicon dashicons dashicons-admin-site"></span><span>Access our Premium Support Forums</span></a>
                    <a target="_blank" title="Plugin Changelog" href="#"><span class="kseoicon dashicons dashicons-dashboard"></span><span>View the Changelog</span></a>
                    <a target="_blank" href="#"><span class="kseoicon dashicons dashicons-admin-links"></span><span> Watch video tutorials</span></a>
                    <a target="_blank" href="#"><span class="kseoicon dashicons dashicons-format-chat"></span><span>Getting started? Read the Beginners Guide</span></a>
                </div>
            </div>
        </div> 
    </div>
    <?php
}
