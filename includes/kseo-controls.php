<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
function kseo_set_controls_value($type, $value) {
    $s = '';
    switch ($type) {
        case 'text':
            $s = sanitize_text_field($value);
            break;
        case 'categories':
            $s = '';
            foreach ($value as $v) {
                if ($s) {
                    $s .=',';
                }
                $s .= $v;
            }
            break;
        case 'features':
            $s = maybe_serialize($value);
            break;
        default:
            $s = $value;
            break;
    }
    return $s;
}

function kseo_get_controls_value($type, $value) {
    switch ($type) {
        case 'categories':
            $val = trim($value, ',');
            $categories = array_filter(explode(',', $val));
            if ($categories) {
                return $categories;
            }
            return array();
        case 'features':
            $features = maybe_unserialize($value);
            return $features;
        default:
            return $value;
    }
    return $value;
}

function kseo_controls_features($key, $args = array(), $values = null) {
    $items = array();
    if ($values) {
        $items = kseo_get_controls_value('features', $values);
    }
    ?>
    <div class="kseo-field kseo-controls clearfix">
        <label for="<?php echo $key; ?>" class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</label>
        <div class="kseo-control-right kseo-features-option-set">
            <p class="kseo-desc"><?php _e('Simple text here:', 'kseo') ?> ✔ ✖ </p>
            <table id="<?php echo $key; ?>-table" class="kseo-features-table">
                <thead>
                <th class="col-head-name"><?php _e('Name of Features', 'kseo') ?></th>
                <th class="col-head-free"><?php _e('Free Version', 'kseo') ?></th>
                <th class="col-head-pro"><?php _e('Pro Version', 'kseo') ?></th>
                <th class="col-head-remove"></th>
                </thead>
                <tbody>
                    <?php
                    if ($items && $items['name']):
                        foreach ($items['name'] as $k => $name) :
                            $free = $items['free'][$k];
                            $pro = $items['pro'][$k];
                            ?>
                            <tr class="kseo-features-table-group">
                                <td><input type="text" name="<?php echo $key; ?>[name][]" autocomplete="off" value="<?php echo $name ?>" /></td>
                                <td><input type="text" name="<?php echo $key; ?>[free][]" autocomplete="off" value="<?php echo $free ?>" /></td>
                                <td><input type="text" name="<?php echo $key; ?>[pro][]" autocomplete="off" value="<?php echo $pro ?>" /></td>
                                <td><a class="features-remove" href="#"><?php _e('x', 'kseo') ?></a></td>
                            </tr>
                            <?php
                        endforeach;
                    else:
                        ?>
                        <tr class="kseo-features-table-group">
                            <td><input type="text" name="<?php echo $key; ?>[name][]" autocomplete="off" value="" /></td>
                            <td><input type="text" name="<?php echo $key; ?>[free][]" autocomplete="off" value="" /></td>
                            <td><input type="text" name="<?php echo $key; ?>[pro][]" autocomplete="off" value="" /></td>
                            <td><a class="features-remove" href="#"><?php _e('x', 'kseo') ?></a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"><button id="add-row" type="button" data-icon="<?php _e('x', 'kseo') ?>" data-input="<?php echo $key; ?>" class="button button-small kseo-features-button"><?php _e('Add Rows', 'kseo') ?></button></td>
                    </tr>
                </tfoot>
            </table>
            <?php
            $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
            if (!empty($desc)) {
                echo '<div class="kseo-desc">' . $desc . '</div>';
            }
            ?>
        </div>
    </div>
    <?php
}

function kseo_controls_fileupload($key, $args = array(), $values = null) {
    $value = '';
    if ($values) {
        $value = $values;
    }
    $class_fileupload_show = '';
    if (!empty($value)) {
        $class_fileupload_show = ' fileuploa-show';
    }
    ?>
    <div class="kseo-field kseo-controls kseo-uploads clearfix">
        <label for="<?php echo $key; ?>" class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</label>
        <div class="kseo-control-right kseo-upload-option-set kseo-upload-option-fileupload">
            <div class="kseo-fileupload-info<?php echo $class_fileupload_show ?> clearfix">
                <div class="kseo-upload-content">
                    <strong><?php _e('File', 'kseo') ?></strong>: <span id="<?php echo $key; ?>-name-html"><?php echo $value; ?></span>
                    <a href="#" class="delete" title="<?php _e('Remove file', 'kseo') ?>">x</a>
                    <input id="<?php echo $key; ?>-name" type="hidden" name="<?php echo $key; ?>" autocomplete="off" value="<?php echo $value; ?>" />
                </div>
            </div>
            <div class="kseo-control-fileupload-button<?php echo $class_fileupload_show ?> clearfix">
                <div class="kseo-button">
                    <input type="button" id="<?php echo $key; ?>-fileupload" data-info="<?php _e('No file uploaded...', 'kseo') ?>" data-file="<?php echo $key; ?>" class="button kseo-fileupload-button" value="<?php _e('Choose File', 'kseo') ?>" />
                </div>
                <div class="progress">
                    <div class="bar"></div >
                    <div class="percent"><?php _e('No file uploaded...', 'kseo') ?></div >
                </div>
            </div>
            <?php
            $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
            if (!empty($desc)) {
                echo '<div class="kseo-desc">' . $desc . '</div>';
            }
            ?>
        </div>
    </div>
    <?php
}

function kseo_controls_categories($key, $args = array(), $values = NULL) {
    $value = $args['default'];
    $categories = array();
    if ($values) {
        $value = trim($values, ',');
        $categories = kseo_get_controls_value('categories', $values);
    }
    $items = get_terms('category', 'parent=0&hide_empty=0');
    ?>
    <div class="kseo-field kseo-controls clearfix">
        <label for="<?php echo $key; ?>" class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</label>
        <ul id="<?php echo $key; ?>" class="categorychecklist form-no-clear">
            <?php foreach ($items as $term) { ?>
                <li id="category-<?php echo $term->term_id; ?>" class="popular-category">
                    <label class="selectit">
                        <input value="<?php echo $term->term_id; ?>" name="<?php echo $key; ?>[]" autocomplete="off" id="in-category-<?php echo $term->term_id; ?>" <?php checked((in_array($term->term_id, $categories) ? 1 : 0), 1); ?> type="checkbox">
                        <span><?php echo $term->name; ?></span>
                    </label>
                </li>
            <?php } ?>
        </ul>
        <?php
        $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
        if (!empty($desc)) {
            echo '<div class="kseo-desc">' . $desc . '</div>';
        }
        ?>
    </div>
    <?php
}

function kseo_controls_category($key, $args = array(), $values = null) {
    $value = $args['default'];
    if ($values) {
        $value = kseo_get_controls_value('categories', $values);
    }
    $items = get_terms('category', 'parent=0&hide_empty=0');
    ?>
    <div class="kseo-field kseo-controls clearfix">
        <label for="<?php echo $key; ?>" class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</label>
        <select id="<?php echo $key; ?>" name="<?php echo $key; ?>" autocomplete="off">
            <option value="0"><?php _e('Select a Category', 'kseo') ?></option>
            <?php foreach ($items as $term) { ?>
                <option value="<?php echo $term->term_id; ?>" <?php selected($term->term_id, $value) ?>><?php echo $term->name; ?></option>
            <?php } ?>
        </select>
        <?php
        $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
        if (!empty($desc)) {
            echo '<div class="kseo-desc">' . $desc . '</div>';
        }
        ?>
    </div>
    <?php
}

function kseo_controls_price($key, $args = array(), $values = null) {
    $value = $args['default'];
    if ($values) {
        $value = $values;
    }
    ?>
    <div class="kseo-field kseo-controls price-items clearfix">
        <label for="<?php echo $key; ?>" class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</label>
        <input class="price-numeric" size="12" autocomplete="off" maxlength="15" type="text" id="<?php echo $key; ?>" value="<?php echo $value; ?>" />
        <span class="currency">đ</span>
        <span class="short-price-show"><?php _e('Negotiate', 'kseo') ?></span>
        <input class="price-hide" name="<?php echo $key; ?>" value="<?php echo $value; ?>" type="hidden">
        <?php
        $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
        if (!empty($desc)) {
            echo '<div class="kseo-desc">' . $desc . '</div>';
        }
        ?>
    </div>
    <?php
}

function kseo_controls_text($key, $args = array(), $values = null) {
    $value = $args['default'];
    if ($values) {
        $value = $values;
    }
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
    <div class="kseo-field kseo-controls clearfix">
        <label for="<?php echo $key; ?>" class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</label>
        <div class="kseo-row-content">
            <input type="text" class="kseo-field-text <?php echo $class ?>"<?php echo $att ?> placeholder="<?php echo esc_attr($placeholder)?>" autocomplete="off" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>" />
        <?php
        if (!empty($desc)) {
            echo '<div class="kseo-desc">' . $desc . '</div>';
        }
        ?>
        </div>
    </div>
    <?php
}

function kseo_controls_checkbox($key, $args = array(), $values = null) {
    $value = $args['default'];
    if ($values) {
        $value = $values;
    }
    ?>
    <div class="kseo-field kseo-controls clearfix">
        <span class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</span>
        <div class="kseo-row-content">
            <label for="<?php echo $key; ?>">
                <input type="checkbox" autocomplete="off" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="1" <?php checked($value, '1'); ?> />
            </label>
            <?php
            $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
            if (!empty($desc)) {
                echo '<div class="kseo-desc">' . $desc . '</div>';
            }
            ?>
        </div>
    </div>
    <?php
}

function kseo_controls_checkboxs($key, $args = array(), $values = null) {
    $items = isset($args['items']) && $args['items'] ? $args['items'] : array();
    ?>
    <div class="kseo-field kseo-controls clearfix">
        <span class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</span>
        <div class="kseo-row-content">
            <?php
            foreach ($items as $k => $name) {
                $id = $key . '-' . $k;
                $value = 0;
                if ($values) {
                    $value = $values;
                }
                ?>
                <label for="<?php echo $id; ?>">
                    <input type="checkbox" autocomplete="off" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="1" <?php checked($value, '1'); ?> />
                    <?php echo $name ?>
                </label>
            <?php } ?>
            <?php
            $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
            if (!empty($desc)) {
                echo '<div class="kseo-desc">' . $desc . '</div>';
            }
            ?>
        </div>
    </div>
    <?php
}

function kseo_controls_radio($key, $args = array(), $values = null) {
    $value = $args['default'];
    if ($values) {
        $value = $values;
    }
    $items = isset($args['items']) && $args['items'] ? $args['items'] : array();
    ?>
    <div class="kseo-field kseo-controls clearfix">
        <span class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</span>
        <div class="kseo-row-content">
            <?php foreach ($items as $k => $name) { ?>
                <label for="<?php echo $key . '-' . $k; ?>">
                    <input type="radio" autocomplete="off" name="<?php echo $key; ?>" id="<?php echo $key . '-' . $k; ?>" value="<?php echo $k; ?>" <?php checked($value, $k); ?> />
                    <?php echo $name ?>
                </label>
            <?php } ?>
            <?php
            $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
            if (!empty($desc)) {
                echo '<div class="kseo-desc">' . $desc . '</div>';
            }
            ?>
        </div>
    </div>
    <?php
}

function kseo_controls_icon($key, $args = array(), $values = null) {
    $value = $args['default'];
    if ($values) {
        $value = $values;
    }
    $position = isset($args['position']) && $args['position'] ? ' position="' . $args['position'] . '"' : '';
    ?>
    <div class="kseo-field kseo-controls clearfix">
        <label for="<?php echo $key; ?>" class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</label>
        <input class="regular-text" type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>" />
        <a id="preview_<?php echo $key; ?>" href="#" data-target="#<?php echo $key; ?>"<?php echo $position; ?>  class="button icon-picker"><svg class="icon icon-<?php echo $value; ?>" aria-hidden="true" role="img"><use href="#icon-<?php echo $value; ?>" xlink:href="#icon-<?php echo $value; ?>"></use></svg></a>
        <?php
        $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
        if (!empty($desc)) {
            echo '<div class="kseo-desc">' . $desc . '</div>';
        }
        ?>
    </div>
    <?php
}

function kseo_controls_select($key, $args = array(), $values = null) {
    $value = $args['default'];
    if ($values) {
        $value = $values;
    }
    $items = isset($args['items']) && $args['items'] ? $args['items'] : array();
    ?>
    <div class="kseo-field kseo-controls clearfix">
        <label for="<?php echo $key; ?>" class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</label>
        <select name="<?php echo $key; ?>" autocomplete="off" id="<?php echo $key; ?>">
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
    <?php
}

function kseo_controls_textarea($key, $args = array(), $values = null) {
    $value = $args['default'];
    if ($values) {
        $value = $values;
    }
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
    <div class="kseo-textarea-container kseo-controls">
        <span class="kseo-box-title"><?php echo esc_html($args['label']) ?> :</span>
        <div class="kseo-control-content">
            <textarea class="kseo-textarea <?php echo esc_attr($class) ?>"<?php echo $att ?> placeholder="<?php echo esc_attr($placeholder)?>" autocomplete="off" name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo $value; ?></textarea>
            <?php
            if (!empty($desc)) {
                echo '<div class="kseo-desc">' . $desc . '</div>';
            }
            ?>
        </div>
    </div>
    <?php
}

function kseo_controls_color($key, $args = array(), $values = null) {
    $value = $args['default'];
    if ($values) {
        $value = $values;
    }
    ?>
    <div class="kseo-field kseo-controls clearfix">
        <label for="<?php echo $key; ?>" class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</label>
        <input name="<?php echo $key; ?>" type="text" autocomplete="off" value="<?php echo $value; ?>" class="kseo-meta-color" />
        <?php
        $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
        if (!empty($desc)) {
            echo '<div class="kseo-desc">' . $desc . '</div>';
        }
        ?>
    </div>
    <?php
}

function kseo_controls_image($key, $args = array(), $values = null) {
    $value = $args['default'];
    $style = '';
    if ($values) {
        $value = $values;
        
    }
     $style = 'style="background-image: url(' . kseo_get_file_uri('images/image-placeholder.png') . ');"';
    if ($values) {
        $value = $values;
        $image = wp_get_attachment_image_src($value, 'thumbnail', false);
        if ($image) {
            list( $img_src, $width, $height ) = $image;
            $style = 'style="background-image: url(' . $img_src . ');"';
        }
    }
    ?>
    <div class="kseo-field kseo-controls kseo-uploads clearfix">
        <label for="<?php echo $key; ?>" class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</label>
        <div class="kseo-control-right kseo-upload-option-set">
            <div class="kseo-img-placeholder" <?php echo $style ?>>
                <img src="<?php echo kseo_get_file_uri('images/blank.gif') ?>">
            </div>
            <div class="kseo-control-placeholder-right">
                <input type="hidden" autocomplete="off" class="kseo-upload-input" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>" />
                <input type="button" id="<?php echo $key; ?>-button" class="button kseo-upload-button" value="<?php _e('Choose', 'kseo') ?>" />
            </div>
            <?php
            $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
            if (!empty($desc)) {
                echo '<div class="kseo-desc">' . $desc . '</div>';
            }
            ?>
        </div>
    </div>
    <?php
}

function kseo_controls_yesno($key, $args = array(), $values = null) {
    $value = $args['default'];
    if ($values) {
        $value = absint($values);
    }
    $selectedlass = ' selected';
    ?>
    <div class="kseo-field kseo-controls kseo-yesno-control selection clearfix">
        <span class="kseo-row-title"><?php echo esc_html($args['label']) ?> :</span>
        <div class="button-yesno">
            <label class="yesno yes<?php echo $value == 1 ? $selectedlass : ''; ?>" for="<?php echo $key; ?>-yes" data-value="1">
                <input class="screen-reader-text" autocomplete="off" id="<?php echo $key; ?>-yes" name="<?php echo $key; ?>" type="radio" <?php checked($value, 1); ?> value="1">
                <span class="button button-small display-options">
                    <?php _e('Yes', 'kseo') ?>
                </span>
            </label>
            <label class="yesno no<?php echo $value == 0 ? $selectedlass : ''; ?>" for="<?php echo $key; ?>-no" data-value="0">
                <input class="screen-reader-text" autocomplete="off" id="<?php echo $key; ?>-no" name="<?php echo $key; ?>" type="radio" <?php checked($value, 0); ?> value="0">
                <span class="button button-small display-options">
                    <?php _e('No', 'kseo') ?>
                </span>
            </label>
            <?php
            $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
            if (!empty($desc)) {
                echo '<div class="kseo-desc">' . $desc . '</div>';
            }
            ?>
        </div>
    </div>
    <?php
}

function kseo_controls_gallery($key, $args = array(), $values = null) {
    $value = $args['default'];
    $attachments = array();
    if ($values) {
        $value = trim($values, ',');
        $attachments = array_filter(explode(',', $value));
    }
    ?>
    <div class="kseo-list-images-container kseo-controls">
        <span class="kseo-box-title"><?php echo esc_html($args['label']) ?> :</span>
        <div class="kseo-box-images clearfix">
            <ul class="kseo-images">
                <?php
                if (!empty($attachments)) {
                    foreach ($attachments as $attachment_id) {
                        $attachment = wp_get_attachment_image($attachment_id, 'thumbnail');

                        echo '<li class="image" data-attachment_id="' . esc_attr($attachment_id) . '">
                                ' . $attachment . '
                                <a href="#" class="delete tips" data-tip="' . esc_attr__('Delete image', 'kseo') . '">' . __('Delete', 'kseo') . '</a>
                        </li>';
                    }
                }
                ?>
            </ul>
            <input type="hidden" class="kseo-image-gallery" name="<?php echo esc_attr($key) ?>" autocomplete="off" value="<?php echo esc_attr($value); ?>" />
            <?php
            $desc = isset($args['desc']) && $args['desc'] ? $args['desc'] : '';
            if (!empty($desc)) {
                echo '<div class="kseo-desc">' . $desc . '</div>';
            }
            ?>
        </div>
        <p class="add-kseo-images hide-if-no-js">
            <a href="#" class="button-small button" data-choose="<?php esc_attr_e('Add Images to Gallery', 'kseo'); ?>" data-update="<?php esc_attr_e('Add to gallery', 'kseo'); ?>" data-delete="<?php esc_attr_e('Delete image', 'kseo'); ?>" data-text="<?php esc_attr_e('Delete', 'kseo'); ?>"><?php _e('Add gallery images', 'kseo'); ?></a>
        </p>
    </div>
    <?php
}

function kseo_controls_hidden($key, $args = array(), $values = null) {
    $value = $args['default'];
    if ($values) {
        $value = $values;
    }
    ?>
    <input type="hidden" id="<?php echo $key; ?>" autocomplete="off" name="<?php echo $key ?>" value="<?php echo $value; ?>" />
    <?php
}
