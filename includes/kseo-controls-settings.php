<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
function kseo_controls_settings_group($key, $args = array()) {
    ?>
    <div class="kads-seo-lable-group group-<?php echo esc_attr($key) ?>"><?php echo esc_html($args['label']) ?></div>
    <table class="wp-list-table widefat fixed striped tags kads-seo-tables kads-seo-tables-<?php echo esc_attr($key) ?>">
        <?php
        foreach ($args['items'] as $k => $params) {
            $fnName = 'kseo_controls_settings_' . $params['type'];
            if (function_exists($fnName)) {
                call_user_func($fnName, $k, $params);
            }
        }
        ?>
    </table>
    <?php
}

function kseo_controls_settings_image($key, $args = array()) {
    $value = absint(get_option($key, $args['default']));
    $style = 'style="background-image: url(' . kseo_get_file_uri('images/image-placeholder.png') . ');"';
    if ($value) {
        $image = wp_get_attachment_image_src($value, 'thumbnail', false);
        if ($image) {
            list( $img_src, $width, $height ) = $image;
            $style = 'style="background-image: url(' . $img_src . ');"';
        }
    }
    ?>
    <tr>
        <td class="kads-seo-first-row"> <label><?php echo esc_html($args['label']) ?></label> </td>
        <td>
            <div class="kads-settings clearfix">
                <div class="kseo-img-placeholder" <?php echo $style ?>>
                    <img src="<?php echo kseo_get_file_uri('images/blank.gif') ?>">
                </div>
                <div class="kads-seo-control-placeholder-right">
                    <input type="hidden" autocomplete="off" class="kseo-upload-input" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>" />
                    <input type="button" id="<?php echo $key; ?>-button" class="button kseo-upload-button" value="<?php _e('Choose', 'kseo') ?>" />
                    <?php
                    $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
                    if (!empty($desc)) {
                        echo '<div class="kseo-desc">' . $desc . '</div>';
                    }
                    ?>
                </div>
            </div>
        </td>
    </tr>
    <?php
}

function kseo_controls_settings_yesno($key, $args = array()) {
    $value = get_option($key, $args['default']);
    $selectedlass = ' selected';
    ?>
    <tr>
        <td class="kads-seo-first-row"> <label><?php echo esc_html($args['label']) ?></label> </td>
        <td>
            <div class="kads-settings clearfix">
                <div class="kads-seo-button-yesno">
                    <label class="kads-seo-yesno yes<?php echo $value == '1' ? $selectedlass : ''; ?>" for="<?php echo $key; ?>-yes" data-value="1">
                        <input class="screen-reader-text" autocomplete="off" id="<?php echo $key; ?>-yes" name="<?php echo $key; ?>" type="radio" <?php checked($value, '1'); ?> value="1">
                        <span class="button button-small display-options">
                            <?php _e('Yes', 'kseo') ?>
                        </span>
                    </label>
                    <label class="kads-seo-yesno no<?php echo $value == '0' ? $selectedlass : ''; ?>" for="<?php echo $key; ?>-no" data-value="0">
                        <input class="screen-reader-text" autocomplete="off" id="<?php echo $key; ?>-no" name="<?php echo $key; ?>" type="radio" <?php checked($value, '0'); ?> value="0">
                        <span class="button button-small display-options">
                            <?php _e('No', 'kseo') ?>
                        </span>
                    </label>
                </div>
                <?php
                $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
                if (!empty($desc)) {
                    echo '<div class="kseo-desc">' . $desc . '</div>';
                }
                ?>
            </div>
        </td>
    </tr>
    <?php
}

function kseo_controls_settings_checkboxs($key, $args = array()) {
    $items = isset($args['options']) && $args['options'] ? $args['options'] : array();
    ?>
    <tr>
        <td class="kads-seo-first-row"> <label><?php echo esc_html($args['label']) ?></label> </td>
        <td>
            <div class="kads-settings clearfix">
                <div class="kads-seo-row-content">
                    <?php
                    foreach ($items as $k => $name) {
                        $id = $key . '-' . $k;
                        $value = get_option($id, $args['default']);
                        ?>
                        <label for="<?php echo $id; ?>">
                            <input type="checkbox" autocomplete="off" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="1" <?php checked($value, '1'); ?> />
                            <?php echo $name ?>
                        </label>
                    <?php } ?>
                </div>
                <?php
                $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
                if (!empty($desc)) {
                    echo '<div class="kseo-desc">' . $desc . '</div>';
                }
                ?>
            </div>
        </td>
    </tr>
    <?php
}

function kseo_controls_settings_radio($key, $args = array()) {
    $value = get_option($key, $args['default']);
    $items = isset($args['options']) && $args['options'] ? $args['options'] : array();
    ?>
    <tr>
        <td class="kads-seo-first-row"> <label><?php echo esc_html($args['label']) ?></label> </td>
        <td>
            <div class="kads-settings clearfix">
                <div class="kads-seo-row-content">
                    <?php foreach ($items as $k => $name) { ?>
                        <label for="<?php echo $key . '-' . $k; ?>">
                            <input type="radio" autocomplete="off" name="<?php echo $key; ?>" id="<?php echo $key . '-' . $k; ?>" value="<?php echo $k; ?>" <?php checked($value, $k); ?> />
                            <?php echo $name ?>
                        </label>
                    <?php } ?>

                </div>
                <?php
                $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
                if (!empty($desc)) {
                    echo '<div class="kseo-desc">' . $desc . '</div>';
                }
                ?>
            </div>
        </td>
    </tr>
    <?php
}

function kseo_controls_settings_checkbox($key, $args = array()) {
    $value = get_option($key, $args['default']);
    ?>
    <tr>
        <td class="kads-seo-first-row"> <label><?php echo esc_html($args['label']) ?></label> </td>
        <td>
            <div class="kads-settings clearfix">
                <input type="checkbox" autocomplete="off" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="1" <?php checked($value, '1'); ?> />
                <?php
                $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
                if (!empty($desc)) {
                    echo '<div class="kseo-desc">' . $desc . '</div>';
                }
                ?>
            </div>
        </td>
    </tr>
    <?php
}

function kseo_controls_settings_select($key, $args = array()) {
    $value = get_option($key, $args['default']);
    $items = isset($args['options']) && $args['options'] ? $args['options'] : array();
    ?>
    <tr>
        <td class="kads-seo-first-row"> <label><?php echo esc_html($args['label']) ?></label> </td>
        <td>
            <div class="kads-settings clearfix">
                <select name="<?php echo $key; ?>" class="kads-seo-select" autocomplete="off" id="<?php echo $key; ?>">
                    <?php foreach ($items as $k => $name) { ?>
                        <option value="<?php echo $k ?>" <?php selected($value, $k); ?>><?php echo $name ?></option>';
                    <?php } ?>
                </select>
                <?php
                $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
                if (!empty($desc)) {
                    echo '<div class="kseo-desc">' . $desc . '</div>';
                }
                ?>
            </div>
        </td>
    </tr>
    <?php
}

function kseo_controls_settings_page($key, $args = array()) {
    $value = get_option($key, $args['default']);
    $dropdown_name = $key;
    $show_option_none = __('&mdash; Select &mdash;');
    $option_none_value = '0';
    $dropdown = wp_dropdown_pages(
            array(
                'name' => $dropdown_name,
                'echo' => 0,
                'show_option_none' => $show_option_none,
                'option_none_value' => $option_none_value,
                'selected' => $value,
                'class' => 'kads-seo-select'
            )
    );
    if (empty($dropdown)) {
        $dropdown = sprintf('<select id="%1$s" name="%1$s">', esc_attr($dropdown_name));
        $dropdown .= sprintf('<option value="%1$s">%2$s</option>', esc_attr($option_none_value), esc_html($show_option_none));
        $dropdown .= '</select>';
    }
    ?>
    <tr>
        <td class="kads-seo-first-row"> <label><?php echo esc_html($args['label']) ?></label> </td>
        <td>
            <div class="kads-settings clearfix">
                <?php echo $dropdown; ?>
                <?php
                $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
                if (!empty($desc)) {
                    echo '<div class="kseo-desc">' . $desc . '</div>';
                }
                ?>
            </div>
        </td>
    </tr>
    <?php
}

function kseo_controls_settings_textarea($key, $args = array()) {
    $value = get_option($key, $args['default']);
    $placeholder = isset($args['placeholder']) && $args['placeholder'] ? $args['placeholder'] : '';
    $maxchars = isset($args['maxchars']) && $args['maxchars'] ? $args['maxchars'] : 0;
    $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
    $maxhtml = '';
    $class = $key;
    $att = '';
    if ($maxchars) {
        $count = $maxchars - str_word_count($value);
        $maxhtml = '<span class="maxchar-count">' . $count . '</span> ' . __('character left', 'kseo') . '. ';
        $class = $key . ' kads-controls-count';
        $desc = $maxhtml . $desc;
        $att = ' maxlength="' . $maxchars . '"';
    }
    ?>
    <tr>
        <td class="kads-seo-first-row"> <label><?php echo esc_html($args['label']) ?></label> </td>
        <td>
            <div class="kads-settings clearfix">
                <textarea class="kads-seo-bot kads-textarea tags <?php echo esc_attr($class) ?>"<?php echo $att ?> placeholder="<?php echo esc_attr($placeholder)?>" autocomplete="off" rows="5" name="<?php echo esc_attr($key) ?>"><?php echo $value ?></textarea>
                <?php
                if (!empty($desc)) {
                    echo '<div class="kseo-desc">' . $desc . '</div>';
                }
                ?>
            </div>
        </td>
    </tr>
    <?php
}

function kseo_controls_settings_text($key, $args = array()) {
    $value = get_option($key, $args['default']);
    $placeholder = isset($args['placeholder']) && $args['placeholder'] ? $args['placeholder'] : '';
    $maxchars = isset($args['maxchars']) && $args['maxchars'] ? $args['maxchars'] : 0;
    $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
    $maxhtml = '';
    $class = $key;
    $att = '';
    if ($maxchars) {
        $count = $maxchars - str_word_count($value);
        $maxhtml = '<span class="maxchar-count">' . $count . '</span> ' . __('character left', 'kseo') . '. ';
        $class = $key . ' kads-controls-count';
        $desc = $maxhtml . $desc;
        $att = ' maxlength="' . $maxchars . '"';
    }
    ?>
    <tr>
        <td class="kads-seo-first-row"> <label><?php echo esc_html($args['label']) ?></label> </td>
        <td>
            <div class="kads-settings clearfix">
                <input class="kads-seo-bot tags <?php echo esc_attr($class) ?>"<?php echo $att ?> placeholder="<?php echo esc_attr($placeholder)?>" autocomplete="off" name="<?php echo esc_attr($key) ?>" value="<?php echo esc_attr($value) ?>" type="text">
                <?php
                if (!empty($desc)) {
                    echo '<div class="kseo-desc">' . $desc . '</div>';
                }
                ?>
            </div>
        </td>
    </tr>
    <?php
}

function kseo_controls_settings_hidden($key, $args = array()) {
    $value = get_option($key, $args['default']);
    ?>
    <input type="hidden" id="<?php echo $key; ?>" autocomplete="off" name="<?php echo $key ?>" value="<?php echo $value; ?>" />
    <?php
}
