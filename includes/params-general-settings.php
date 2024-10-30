<?php
function kseo_params_general_settings() {
    return array(
        'kseo-canonical-all' => array(
            'label' => __('SEO Setting For Canonical', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_canonical_no_override' => array(
                    'label' => __('None Override', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Custom canonical link vill no override', 'kseo')
                ),
                '_kseo_canonical_unpaged' => array(
                    'label' => __('Unpaged', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Custom canonical link vill Unpaged', 'kseo')
                ),
            )
        ),
        'kseo-group-all' => array(
            'label' => __('SEO Setting For All Page', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_social_site_name' => array(
                    'label' => __('Site Name:', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'placeholder' => get_bloginfo('name', 'display')
                ),
                '_kseo_general_meta_keywords' => array(
                    'label' => __('Meta Keywords:', 'kseo'),
                    'default' => '',
                    'type' => 'textarea',
                    'maxchars' => 300,
                    'desc' => __('Each search engine sets their own rules. However, it is a good practice to have less than 10% of the total words of a page. If for example your page has 300 words it is better to have maximum 30 words in your meta keyword tag. Otherwise could be considered overstuffing.', 'kseo')
                ),
                '_kseo_general_meta_description' => array(
                    'label' => __('Meta Description:', 'kseo'),
                    'default' => '',
                    'type' => 'textarea',
                    'maxchars' => 320,
                    'placeholder' => get_bloginfo('description', 'display'),
                    'desc' => __('It’s not the number of words that count. It’s actually the number of characters length. Because, Google will cut off anything more than 155(roughly) characters. Optimizing for in the description and try to limit its length to 25-30 words. Also try to use no more than two sentences.', 'kseo')
                ),
                '_kseo_social_default_image' => array(
                    'label' => __('Default Image', 'kseo'),
                    'default' => '',
                    'type' => 'image'
                ),
                '_kseo_social_default_logo' => array(
                    'label' => __('Default Logo', 'kseo'),
                    'default' => '',
                    'type' => 'image',
                    'desc' => __('Get this data if your theme does not have the custom_logo function', 'kseo')
                ),
                '_kseo_general_enable_rating' => array(
                    'label' => __('Enable Rating site', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Custom for site ratting not Product rating', 'kseo')
                ),
                '_kseo_general_googlebot' => array(
                    'label' => __('Google Meta Tag:', 'kseo'),
                    'default' => '',
                    'options' => array(
                        '' => ' - Choose Google Meta - ',
                        'noarchive' => 'No Archive',
                        'nosnippet' => 'No Snippet',
                        'noindex' => 'No index',
                        'nofollow' => 'No Follow'
                    ),
                    'type' => 'select',
                    'desc' => __('In this article you can find info on', 'kseo') . ' <strong>' . __('Google Meta Tag', 'kseo') . '</strong>. '
                    . __('Especially we don’t have Google Meta tags.', 'kseo') . '<br><a target="_blank" href="http://online-seo-information.blogspot.com/2008/12/meta-tags-google-meta-tags-google-meta.html">' . __('See More At', 'kseo') . '</a>'
                ),
            )
        ),
        'kseo-group-home' => array(
            'label' => __('Home Page Settings', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_general_home_title' => array(
                    'label' => __('Home Title:', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'maxchars' => 80,
                    'placeholder' => get_bloginfo('name', 'display')
                ),
                '_kseo_general_home_description' => array(
                    'label' => __('Home Description:', 'kseo'),
                    'default' => '',
                    'type' => 'textarea',
                    'maxchars' => 320,
                    'placeholder' => get_bloginfo('description', 'display'),
                    'desc' => __('It’s not the number of words that count. It’s actually the number of characters length. Because, Google will cut off anything more than 155(roughly) characters. Optimizing for in the description and try to limit its length to 25-30 words. Also try to use no more than two sentences.', 'kseo')
                ),
                '_kseo_social_home_image' => array(
                    'label' => __('Home Image', 'kseo'),
                    'default' => '',
                    'type' => 'image'
                ),
            )
        ),
        'kseo-group-title' => array(
            'label' => __('Title Settings', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_general_home_title_format' => array(
                    'label' => __('Home Page Title Format:', 'kseo'),
                    'default' => '%page_title% | %blog_description%',
                    'type' => 'text',
                    'desc' => __('This controls the format of the title tag for your Home Page.<br />The following macros are supported:', 'kseo')
                    . '<ul><li>' . __('%blog_title% - Your blog title', 'kseo') . '</li><li>' .
                    __('%blog_description% - Your blog description', 'kseo') . '</li><li>' .
                    __('%page_title% - The original title of the page', 'kseo') . '</li><li></ul>',
                ),
                '_kseo_general_page_title_format' => array(
                    'label' => __('Page Title Format:', 'kseo'),
                    'default' => '%page_title% | %blog_title%',
                    'type' => 'text',
                    'desc' => __('This controls the format of the title tag for Pages.<br />The following macros are supported:', 'kseo')
                    . '<ul><li>' . __('%blog_title% - Your blog title', 'kseo') . '</li><li>' .
                    __('%blog_description% - Your blog description', 'kseo') . '</li><li>' .
                    __('%page_title% - The original title of the page', 'kseo') . '</li><li>' .
                    __('%post_date% - The date the page was published (localized)', 'kseo') . '</li><li>' .
                    __('%post_year% - The year the page was published (localized)', 'kseo') . '</li><li>' .
                    __('%post_month% - The month the page was published (localized)', 'kseo') . '</li>',
                ),
                '_kseo_general_post_title_format' => array(
                    'label' => __('Post Title Format:', 'kseo'),
                    'default' => '%post_title% | %blog_title%',
                    'type' => 'text',
                    'desc' => __('This controls the format of the title tag for Posts.<br />The following macros are supported:', 'kseo')
                    . '<ul><li><li>' . __('%blog_title% - Your blog title', 'kseo') . '</li><li>' .
                    __('%blog_description% - Your blog description', 'kseo') . '</li><li>' .
                    __('%post_title% - The original title of the post', 'kseo') . '</li><li>' .
                    __('%category_title% - The (main) category of the post', 'kseo') . '</li><li>' .
                    __('%post_date% - The date the post was published (localized)', 'kseo') . '</li><li>' .
                    __('%post_year% - The year the post was published (localized)', 'kseo') . '</li><li>' .
                    __('%post_month% - The month the post was published (localized)', 'kseo') . '</li></ul>',
                ),
                '_kseo_general_category_title_format' => array(
                    'label' => __('Category Title Format:', 'kseo'),
                    'default' => '%category_title% | %blog_title%',
                    'type' => 'text',
                    'desc' => __('This controls the format of the title tag for Category Archives.<br />The following macros are supported:', 'kseo') .
                    '<ul><li>' . __('%blog_title% - Your blog title', 'kseo') . '</li><li>' .
                    __('%blog_description% - Your blog description', 'kseo') . '</li><li>' .
                    __('%category_title% - The original title of the category', 'kseo') . '</li><li>' .
                    __('%category_description% - The description of the category', 'kseo') . '</li></ul>'
                ),
                '_kseo_general_tax_title_format' => array(
                    'label' => __('Tax Title Format:', 'kseo'),
                    'default' => '%tax_title% | %blog_title%',
                    'type' => 'text',
                    'desc' => __('This controls the format of the title tag for Custom Post Archives.<br />The following macros are supported:', 'kseo') .
                    '<ul><li>' . __('%blog_title% - Your blog title', 'kseo') . '</li><li>' .
                    __('%blog_description% - Your blog description', 'kseo') . '</li><li>' .
                    __('%tax_title% - The original archive title given by wordpress', 'kseo') . '</li></ul>'
                ),
                '_kseo_general_archive_title_format' => array(
                    'label' => __('Archive Title Format:', 'kseo'),
                    'default' => '%archive_title% | %blog_title%',
                    'type' => 'text',
                    'desc' => __('This controls the format of the title tag for Custom Post Archives.<br />The following macros are supported:', 'kseo') .
                    '<ul><li>' . __('%blog_title% - Your blog title', 'kseo') . '</li><li>' .
                    __('%blog_description% - Your blog description', 'kseo') . '</li><li>' .
                    __('%archive_title% - The original archive title given by wordpress', 'kseo') . '</li></ul>'
                ),
                '_kseo_general_date_title_format' => array(
                    'label' => __('Author Date Title Format:', 'kseo'),
                    'default' => '%date% | %blog_title%',
                    'type' => 'text',
                    'desc' => __('This controls the format of the title tag for Date Archives.<br />The following macros are supported:', 'kseo') .
                    '<ul><li>' . __('%blog_title% - Your blog title', 'kseo') . '</li><li>' .
                    __('%blog_description% - Your blog description', 'kseo') . '</li><li>' .
                    __('%date% - The original archive title given by wordpress, e.g. "2007" or "2007 August"', 'kseo') . '</li><li>' .
                    __('%day% - The original archive day given by wordpress, e.g. "17"', 'kseo') . '</li><li>' .
                    __('%month% - The original archive month given by wordpress, e.g. "August"', 'kseo') . '</li><li>' .
                    __('%year% - The original archive year given by wordpress, e.g. "2007"', 'kseo') . '</li></ul>'
                ),
                '_kseo_general_author_title_format' => array(
                    'label' => __('Author Archive Title Format:', 'kseo'),
                    'default' => '%author% | %blog_title%',
                    'type' => 'text',
                    'desc' => __('This controls the format of the title tag for Author Archives.<br />The following macros are supported:', 'kseo') .
                    '<ul><li>' . __('%blog_title% - Your blog title', 'kseo') . '</li><li>' .
                    __('%blog_description% - Your blog description', 'kseo') . '</li><li>' .
                    __('%author% - The original archive title given by wordpress, e.g. "Steve" or "John Smith"', 'kseo') . '</li></ul>'
                ),
                '_kseo_general_tag_title_format' => array(
                    'label' => __('Tag Title Format:', 'kseo'),
                    'default' => '%tag% | %blog_title%',
                    'type' => 'text',
                    'desc' => __('This controls the format of the title tag for Tag Archives.<br />The following macros are supported:', 'kseo') .
                    '<ul><li>' . __('%blog_title% - Your blog title', 'kseo') . '</li><li>' .
                    __('%blog_description% - Your blog description', 'kseo') . '</li><li>' .
                    __('%tag% - The name of the tag', 'kseo') . '</li></ul>'
                ),
                '_kseo_general_search_title_format' => array(
                    'label' => __('Search Title Format:', 'kseo'),
                    'default' => '%search% | %blog_title%',
                    'type' => 'text',
                    'desc' => __('This controls the format of the title tag for the Search page.<br />The following macros are supported:', 'kseo') .
                    '<ul><li>' . __('%blog_title% - Your blog title', 'kseo') . '</li><li>' .
                    __('%blog_description% - Your blog description', 'kseo') . '</li><li>' .
                    __('%search% - What was searched for', 'kseo') . '</li></ul>'
                ),
                '_kseo_general_404_title_format' => array(
                    'label' => __('404 Title Format:', 'kseo'),
                    'default' => 'Nothing found for %request_words%',
                    'type' => 'text',
                    'desc' => __('This controls the format of the title tag for the 404 page.<br />The following macros are supported:', 'kseo') .
                    '<ul><li>' . __('%blog_title% - Your blog title', 'kseo') . '</li><li>' .
                    __('%blog_description% - Your blog description', 'kseo') . '</li><li>' .
                    __('%request_url% - The original URL path, like "/url-that-does-not-exist/"', 'kseo') . '</li><li>' .
                    __('%request_words% - The URL path in human readable form, like "Url That Does Not Exist"', 'kseo') . '</li><li>' .
                    __('%404_title% - Additional 404 title input"', 'kseo') . '</li></ul>'
                ),
                '_kseo_general_paged_title_format' => array(
                    'label' => __('Paged Format:', 'kseo'),
                    'default' => '- Part %page%',
                    'type' => 'text',
                    'desc' => __('This string gets appended/prepended to titles of paged index pages (like home or archive pages).', 'kseo')
                    . __('The following macros are supported:', 'kseo')
                    . '<ul><li>' . __('%page% - The page number', 'kseo') . '</li></ul>'
                )
            )
        ),
        'kseo-group-noindex' => array(
            'label' => __('Noindex Settings & Advanced Settings', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_general_noindex_categories' => array(
                    'label' => __('Noindex for Categories:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to index Category Archives. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_general_noindex_archives' => array(
                    'label' => __('Noindex for Archives:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to index Archives. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_general_noindex_date' => array(
                    'label' => __('Noindex for Date:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to index Date Archives. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_general_noindex_author' => array(
                    'label' => __('Noindex for Author:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to index Author Archives. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_general_noindex_tax' => array(
                    'label' => __('Noindex for Tag:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to index Tax Archives. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_general_noindex_tag' => array(
                    'label' => __('Noindex for Tag:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to index Tag Archives. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_general_noindex_search' => array(
                    'label' => __('Noindex for Search page:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to index Search page. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_general_noindex_404' => array(
                    'label' => __('Noindex for 404 page:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to index 404 page. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_general_noindex_paginated' => array(
                    'label' => __('Noindex for Paginated pages/posts:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to index paginated pages/posts. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_general_nofollow_paginated' => array(
                    'label' => __('Nofollow for Paginated pages/posts:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this to ask search engines not to index paginated pages/posts. Useful for avoiding duplicate content.', 'kseo')
                ),
                '_kseo_general_remove_descriptions_paginated' => array(
                    'label' => __('Remove Descriptions For Paginated Pages:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this and your Meta Descriptions will be removed from page 2 or later of paginated content.', 'kseo')
                ),
                '_kseo_general_remove_descriptions_tags' => array(
                    'label' => __('Remove Descriptions For Tags', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Check this and your Meta Descriptions will be removed from page 2 or later of paginated content.', 'kseo')
                ),
            )
        ),
    );
}
