<?php
function kseo_params_meta($post_title, $meta_keywords, $post_content, $social_description) {
    $param = array(
        'kseo-group-normal' => array(
            'label' => __('General', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_post_title' => array(
                    'label' => __('Title', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'maxchars' => 80,
                    'placeholder' => $post_title,
                ),
                '_kseo_post_meta_keywords' => array(
                    'label' => __('Meta Keywords', 'kseo'),
                    'default' => '',
                    'type' => 'textarea',
                    'maxchars' => 300,
                    'placeholder' => $meta_keywords,
                    'desc' => __('Each search engine sets their own rules. However, it is a good practice to have less than 10% of the total words of a page. If for example your page has 300 words it is better to have maximum 30 words in your meta keyword tag. Otherwise could be considered overstuffing.', 'kseo')
                ),
                '_kseo_post_meta_description' => array(
                    'label' => __('Meta Description', 'kseo'),
                    'default' => '',
                    'type' => 'textarea',
                    'maxchars' => 320,
                    'placeholder' => $post_content,
                    'desc' => __('It’s not the number of words that count. It’s actually the number of characters length. Because, Google will cut off anything more than 155(roughly) characters. Optimizing for in the description and try to limit its length to 25-30 words. Also try to use no more than two sentences.', 'kseo')
                ),
                '_kseo_post_social_image' => array(
                    'label' => __('Image social', 'kseo'),
                    'default' => '',
                    'type' => 'image'
                ),
                '_kseo_post_social_description' => array(
                    'label' => __('Social Description', 'kseo'),
                    'default' => '',
                    'type' => 'textarea',
                    'maxchars' => 1000,
                    'placeholder' => $social_description,
                    'desc' => __('There isn\'t a hard limit on the data you can put there, but in various rendering places Facebook will limit it. The limit in news feed is different from the limit in Ticker which is different than the limit on timeline.', 'kseo')
                ),
            )
        ),
        'kseo-group-noindex' => array(
            'label' => __('Advanced', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_post_noindex' => array(
                    'label' => __('Noindex for this Entry', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to index this Archive. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_post_nofollow' => array(
                    'label' => __('Nofollow for this Entry', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to follow this Archive. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_post_canonical' => array(
                    'label' => __('Custom Canonical', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'placeholder' => __('Custom canonical link', 'kseo'),
                ),
            )
        ),
        'kseo-group-price' => array(
            'label' => __('Products', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_post_product_enable' => array(
                    'label' => __('Enable Product', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Post will enable as Product [ld+json]', 'kseo')
                ),
                '_kseo_post_product_enable_price' => array(
                    'label' => __('Enable Price', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Post will enable as product with price [ld+json]', 'kseo')
                ),
                '_kseo_post_product_price_from' => array(
                    'label' => __('Price', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'placeholder' => __('Single price or from Price', 'kseo'),
                ),
                '_kseo_post_product_price_to' => array(
                    'label' => __('To Price', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'placeholder' => __('To Price', 'kseo'),
                ),
                '_kseo_post_product_price_currency' => array(
                    'label' => __('Price Currency', 'kseo'),
                    'default' => 'USD',
                    'type' => 'text',
                    'placeholder' => __('Price Currency', 'kseo'),
                ),
                '_kseo_post_product_rating_value' => array(
                    'label' => __('Rating Custom Value', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'placeholder' => __('Manually install if there are no reviews yet', 'kseo'),
                ),
                '_kseo_post_product_brand' => array(
                    'label' => __('Brand', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_product_author' => array(
                    'label' => __('Review Author', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_product_seller' => array(
                    'label' => __('Seller Author', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
            )
        ),
        'kseo-group-local-business' => array(
            'label' => __('Local Business', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_post_local_business_enable' => array(
                    'label' => __('Enable Local Business', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Post will enable as Local Business [ld+json]', 'kseo')
                ),
                '_kseo_post_local_business_title' => array(
                    'label' => __('Custom Title', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'maxchars' => 80,
                    'placeholder' => $post_title,
                ),
                '_kseo_post_local_business_content' => array(
                    'label' => __('Description', 'kseo'),
                    'default' => '',
                    'type' => 'textarea',
                    'maxchars' => 320,
                    'placeholder' => $post_content,
                ),
                '_kseo_post_local_business_address' => array(
                    'label' => __('Business Address', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'placeholder' => __('Business Address', 'kseo'),
                ),
                '_kseo_post_local_business_telephone' => array(
                    'label' => __('Business Telephone', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'placeholder' => __('Business Telephone', 'kseo'),
                ),
                '_kseo_post_local_business_priceRange' => array(
                    'label' => __('Price Range', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'placeholder' => __('Single Price Range', 'kseo'),
                ),
                '_kseo_post_local_business_worstRating' => array(
                    'label' => __('WorstRating', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'placeholder' => __('Single Price Range', 'kseo'),
                ),
                '_kseo_post_local_business_worstRating' => array(
                    'label' => __('WorstRating', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'placeholder' => __('Single Price Range', 'kseo'),
                ),
                
                
            )
        ),
        'kseo-group-faqpage' => array(
            'label' => __('FAQ Page', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_post_faqpage_enable' => array(
                    'label' => __('Enable FAQ Page', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Post will enable as FAQ Page [ld+json]', 'kseo')
                ),
                '_kseo_post_faqpage_name' => array(
                    'label' => __('FAQ Name', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'maxchars' => 80,
                    'placeholder' => $post_title,
                ),
                '_kseo_post_faqpage_question_1' => array(
                    'label' => __('FAQ Question 1', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_faqpage_answer_1' => array(
                    'label' => __('---- FAQ Answer 1', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_faqpage_question_2' => array(
                    'label' => __('FAQ Question 2', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_faqpage_answer_2' => array(
                    'label' => __('---- FAQ Answer 2', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_faqpage_question_3' => array(
                    'label' => __('FAQ Question 3', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_faqpage_answer_3' => array(
                    'label' => __('---- FAQ Answer 3', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_faqpage_question_4' => array(
                    'label' => __('FAQ Question 4', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_faqpage_answer_4' => array(
                    'label' => __('---- FAQ Answer 4', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_faqpage_question_5' => array(
                    'label' => __('FAQ Question 5', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_faqpage_answer_5' => array(
                    'label' => __('---- FAQ Answer 5', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_faqpage_question_6' => array(
                    'label' => __('FAQ Question 6', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_post_faqpage_answer_6' => array(
                    'label' => __('---- FAQ Answer 6', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
            )
        )
    );
    return $param;
}
