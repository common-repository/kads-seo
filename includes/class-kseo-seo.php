<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class KadsSeo {

    private $logo;
    private $image;
    private $no_image;
    private $canonical;
    private $tags = array();
    private $current_url;
    private $id;
    private $title;
    private $author;
    private $sitename;
    private $description;
    private $social_description;
    private $keywords = '';
    private $homeURL;
    private $ldjson = array();
    private $thumbs;
    private $category_title;
    private $datePublished;
    private $dateModified;
    private $telephone;
    private $maximum_description_length = 320;
    private $maximum_social_length = 1000;
    private $maximum_title_length = 80;
    private $contact_page;
    private $bloginfo_description;
    private $bloginfo_name;
    private $site_rating_value = 5;
    private $site_rating_count = 1;
    private $site_best_rating = 5;
    private $domain;
    private $meta_robots_follow = 'follow';
    private $meta_robots_index = 'index';
    private $is_remove_description = false;
    private static $content_shortcode = array();
    private $product_price = false;
    private $enable_rate = true;

    function __construct() {
        $this->init();
    }

    private function init() {
        remove_action('wp_head', 'rel_canonical');
        if (!is_admin() && !( defined('WP_CLI') && WP_CLI )) {
            add_filter('pre_get_document_title', array($this, 'title_remove'), 30);

            $this->bloginfo_description = get_bloginfo('description', 'display');
            $this->bloginfo_name = get_bloginfo('name', 'display');

            add_action('wp_head', array($this, 'RunSEO'), 1);
            add_action('init', array($this, 'init_minify_html'), PHP_INT_MAX);
        }
    }

    public function title_remove() {
        return $this->wp_title();
    }

    public function init_minify_html() {
        ob_start(array($this, 'minify_html_output'));
    }

    public function minify_html_output($sbuffer) {
        if (substr(ltrim($sbuffer), 0, 5) == '<?xml') {
            return ( $sbuffer );
        }
        $search = array(
            '~>\s+<~', // strip whitespaces after tags, except space
            '/(\>)\s*(\<)/m', // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/', // Remove HTML comments
        );
        $replace = array(
            '><',
            '$1$2',
            ''
        );
        $title = $this->wp_title();
        if (!empty($title)) {
            $sbuffer = preg_replace('/<title([^>]*?)\s*>([^<]*?)<\/title\s*>/is', '<title\\1>' . preg_replace('/(\$|\\\\)(?=\d)/', '\\\\\1', strip_tags($title)) . '</title>', $sbuffer, 1);
        }
        $buffer = preg_replace($search, $replace, $sbuffer);
        return $buffer;
    }

    public function reset_duplicate($originals, $names, $values) {
        $metaTags = array();
        if (count($originals) == count($names) && count($names) == count($values)) {
            for ($i = 0, $limiti = count($names); $i < $limiti; $i++) {
                $metaTags[$names[$i]] = array(
                    'html' => $originals[$i],
                    'value' => $values[$i]
                );
            }
        }
    }

    public function RunSEO() {
        $this->ldjson = array();
        $this->homeURL = get_option('home') . '/';
        $this->no_image = kseo_get_file_uri('images/image-placeholder.png');

        $meta_logo_id = $this->get_option('_kseo_social_default_logo');
        if ($meta_logo_id) {
            $this->logo = $this->get_attachment($meta_logo_id);
        }

        $meta_image_id = $this->get_option('_kseo_social_default_image');
        $this->image = $this->get_attachment($meta_image_id);
        if (!$meta_logo_id && $this->image) {
            $this->logo = $this->image;
        }

        $http = "http://";
        if (is_ssl()) {
            $http = "https://";
        }
        $this->current_url = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->title = $this->bloginfo_name;
        $this->domain = $_SERVER['HTTP_HOST'];
        $this->author = $this->domain;
        $author = $this->get_option('_kseo_geo_author_name');
        if (!empty($author)) {
            $this->author = $author;
        }
        $this->contact_page = absint($this->get_option('_kseo_geo_page_contact'));
        $site_name = $this->get_option('_kseo_social_site_name');
        if (empty($site_name)) {
            $site_name = $this->bloginfo_name;
        }
        $this->sitename = esc_attr($site_name);
        $this->description = $this->get_option('_kseo_general_meta_description');
        $this->category_title = $this->title;

        $this->set_keywords();

        if ($this->ishome()) {
            $this->set_home_page();
        } elseif (is_singular()) {
            $this->set_single_post();
            if ($this->id == $this->contact_page && is_page()) {
                $this->contact_page();
            }
        } else {
            $this->set_list_posts();
        }
        $this->meta_robots_paged();

        $this->canonical_generate();

        $this->OutputHTML();
    }

    public function set_site_rating() {
        global $wpdb, $post;
        if (!$post) {
            $post = get_post();
        }
        $product_enable = absint($this->get_post_meta('_kseo_post_product_enable', $post->ID));
        $enable_rating = absint($this->get_option('_kseo_general_enable_rating'));

        if (!$product_enable && !$enable_rating) {
            return;
        }

        $custom_value = $this->get_post_meta('_kseo_post_product_rating_value', $post->ID);
        if (empty($custom_value)) {
            $custom_value = 1;
        }
        if (current_theme_supports('kad-comments-ratings')) {
            $rating = get_transient('amp_ratting_total_' . $post->ID);
            if (!$rating) {
                $post_id = $post->ID;
                $rating = $wpdb->get_row("SELECT COUNT(b.meta_value) as `total`,SUM(b.meta_value) as `total_rate` FROM $wpdb->comments AS a INNER JOIN $wpdb->commentmeta AS b ON a.comment_ID = b.comment_id WHERE a.comment_post_ID = $post_id");
                set_transient('amp_ratting_total_' . $post->ID, $rating, HOUR_IN_SECONDS * 12);
            }
            if ($rating && isset($rating->total_rate) && $rating->total_rate) {
                $this->site_rating_value = round(absint($rating->total_rate) / absint($rating->total), 2);
                $this->site_rating_count = absint($rating->total);
            }
            
            if (!empty($custom_value)) {
                $this->site_rating_count = $custom_value + $this->site_rating_count;
            }
            
        } else {
            if (!empty($custom_value)) {
                $this->site_rating_count = $custom_value + $this->site_rating_count;
                $this->site_rating_value = 5;
            }
        }
        $author = $this->get_post_meta('_kseo_post_product_author', $post_id);
        if (empty($author)) {
            $author = get_author_name($post->post_author);
        }
        if ($product_enable) {

            $sku = '10' . $post->ID;
            $mpn = '100' . $post->ID;
            $brand = $this->get_post_meta('_kseo_post_product_brand', $post_id);


            $items = array(
                "@context" => "http://schema.org/",
                "@type" => "Product",
                "aggregateRating" => array(
                    "@type" => "AggregateRating",
                    "ratingValue" => $this->site_rating_value,
                    "bestRating" => $this->site_best_rating,
                    "reviewCount" => $this->site_rating_count,
                ),
                "review" => array(
                    "@type" => "Review",
                    "reviewRating" => array(
                        "@type" => "AggregateRating",
                        "ratingValue" => $this->site_rating_value,
                        "bestRating" => $this->site_best_rating,
                        "ratingCount" => $this->site_rating_count,
                    ),
                    "author" => array(
                        "@type" => "Person",
                        "name" => $author,
                    ),
                ),
                "sku" => $sku,
                "mpn" => $mpn,
                "name" => esc_attr($post->post_title),
                "description" => esc_attr($this->description)
            );
            if ($this->product_price) {
                $items['offers'] = $this->product_price;
            }

            if (!empty($brand)) {
                $items['brand'] = array(
                    "@type" => "Thing",
                    "name" => $brand
                );
            }

            if (isset($this->thumbs['url'])) {
                $items['image'] = $this->thumbs['url'];
            } else if ($this->logo) {
                $items['image'] = $this->logo;
            }

            $this->set_ldJson($items);
            
        } else {
            $sku = '10' . $post->ID;
            $mpn = '1000' . $post->ID;
            $brand = $this->bloginfo_name;
            $items = array(
                "@context" => "http://schema.org/",
                "@type" => "Product",
                "brand" => array(
                    "@type" => "Thing",
                    "name" => $brand
                ),
                "aggregateRating" => array(
                    "@type" => "AggregateRating",
                    "ratingValue" => $this->site_rating_value,
                    "bestRating" => $this->site_best_rating,
                    "reviewCount" => $this->site_rating_count,
                ),
                "sku" => $sku,
                "mpn" => $mpn,
                "name" => esc_attr($post->post_title),
                "description" => esc_attr($this->description)
            );
            
            if (isset($this->thumbs['url'])) {
                $items['image'] = $this->thumbs['url'];
            } else if ($this->logo) {
                $items['image'] = $this->logo;
            }
            $this->set_ldJson($items);
        }
    }

    public function footer_content() {
        if ($this->site_rating_value) {
            ?>
            <div itemscope itemtype="http://schema.org/Product">
                <span itemprop="name"><?php echo $this->domain ?></span>
                <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                    <?php _e('Rated', 'kseo') ?> <span itemprop="ratingValue"><?php _e('5', 'kseo') ?></span><?php _e('/5 based on', 'kseo') ?> <span itemprop="reviewCount"><?php echo $this->site_rating_value ?></span> <?php _e('reviews', 'kseo') ?>
                </div>
            </div>
            <?php
        }
    }

    private function set_home_page() {
        $home_title = $this->get_option('_kseo_general_home_title');
        if (!empty($home_title)) {
            $this->title = $home_title;
        }
        $home_description = $this->get_option('_kseo_general_home_description');
        if (!empty($home_description)) {
            $this->description = $home_description;
        }

        $home_image = $this->get_option('_kseo_social_home_image');
        if (!empty($home_image)) {
            $this->image = $this->get_attachment($home_image);
        }
        $this->WebSite();
        if (!is_front_page() && is_home()) {

            $this->set_list_posts();
        }
        if (empty($this->contact_page)) {
            $this->contact_page();
        }
        $this->RealEstateAgent();
        $this->LocalBusiness();
        $this->get_queried_home_object();
    }

    public function contact_page() {
        $meta_contact = $this->get_option('_kseo_geo_contact_type', 'person');
        if ($meta_contact == 'person') {
            $this->Person();
        } else {
            $this->Organization();
        }
    }

    function get_ip($ip) {
        global $wpdb;
        $result = wp_cache_get('ip-kads-seo' . $ip);
        if (false === $result) {
            $tabledb = $wpdb->base_prefix . 'kads_seo';
            $sql = $wpdb->prepare("SELECT `id` FROM `$tabledb` WHERE `ip` = '%s'", $ip);
            $result = $wpdb->get_row($sql);
            wp_cache_set('ip-kads-seo' . $ip, $result);
        }
        return $result;
    }

    function add_ip($ip) {
        global $wpdb;
        $tabledb = $wpdb->base_prefix . 'kads_seo';
        $wpdb->insert($tabledb, array(
            'ip' => $ip
        ));
    }

    private function OutputHTML() {
        $meta_google = $this->get_option('_kseo_social_profile_google');
        $meta_facebook = $this->get_option('_kseo_social_profile_facebook');
        $meta_twitter = $this->get_option('_kseo_social_profile_twitter');
        $meta_googlebot = $this->get_option('_kseo_general_googlebot');

        $remove_descriptions_tags = absint($this->get_option('_kseo_general_remove_descriptions_tags'));
        if (is_tag() && $remove_descriptions_tags) {
            $this->is_remove_description = true;
        }


        if (!$this->thumbs) {
            $this->thumbs = $this->image;
        }

        if (empty($this->social_description)) {
            $this->social_description = $this->description;
        }
        if (empty($this->title)) {
            $this->title = $this->sitename;
        }
        if ($this->get_int_option('blog_public')) {
            $robots = $this->meta_robots_index . ',' . $this->meta_robots_follow;
            if ($robots != 'index,follow') {
                ?>
                <meta name="robots" content="<?php echo esc_attr($robots) ?>">
                <?php
            }
        }
        echo $this->canonical;

        if (!empty($meta_google)) {
            ?>
            <link rel="author" href="<?php echo esc_attr($meta_google) ?>/posts"/>
            <link rel="publisher" href="<?php echo esc_attr($meta_google) ?>"/>    
            <?php
        }
        if (!empty($meta_googlebot)) {
            ?>
            <meta name="googlebot" content="<?php echo esc_attr($meta_googlebot) ?>">
            <?php
        }
        if (!empty($this->author)) {
            ?>
            <meta itemprop="author" name="author" content="<?php echo esc_attr($this->author); ?>">
            <?php
        }
        $image_url = $this->no_image;
        $width = 800;
        $height = 600;
        if (isset($this->thumbs['url'])) {
            $image_url = $this->thumbs['url'];
            $width = $this->thumbs['width'];
            $height = $this->thumbs['height'];
        }
        if (!$this->is_remove_description && !empty($this->description)) {
            ?>
            <meta name="description" content="<?php echo esc_attr($this->description); ?>" />
            <?php
        }
        ?>

        <meta name="keywords" content="<?php echo esc_attr($this->keywords); ?>" />

        <meta itemprop="name" content="<?php echo esc_attr($this->title); ?>">

        <?php
        if (!empty($meta_facebook)) {
            ?>
            <meta property="article:publisher" content="<?php echo esc_attr($meta_facebook) ?>">
            <meta property="article:author" content="<?php echo esc_attr($meta_facebook) ?>">    
            <?php
        }

        if ($this->tags) {
            foreach ($this->tags as $tag) {
                ?>
                <meta property="article:tag"   content="<?php echo esc_attr($tag); ?>" />
                <?php
            }
        }
        ?>
        <meta itemprop="image" content="<?php echo esc_attr($this->thumbs['url']); ?>">

        <meta property="og:url"           content="<?php echo esc_url($this->current_url); ?>" />
        <meta property="og:type"          content="website" />
        <meta property="og:title"         content="<?php echo esc_attr($this->title); ?>" />
        <meta property="og:description"   content="<?php echo esc_attr($this->social_description); ?>" />
        <meta property="og:site_name"     content="<?php echo esc_attr($this->sitename); ?>" />
        <meta property="og:image"         content="<?php echo esc_url($image_url); ?>" />
        <meta property="og:image:width"   content="<?php echo esc_attr($width); ?>" />
        <meta property="og:image:height"  content="<?php echo esc_attr($height); ?>" />
        <?php
        if (!empty($this->category_title)) {
            ?>
            <meta property="article:section"   content="<?php echo esc_attr($this->category_title); ?>" />
            <?php
        }
        if (!empty($this->datePublished)) {
            ?>
            <meta property="article:published_time"   content="<?php echo esc_attr($this->datePublished); ?>" />
            <?php
        }

        if (!empty($this->dateModified)) {
            ?>
            <meta property="article:modified_time"   content="<?php echo esc_attr($this->dateModified); ?>" />
            <meta property="og:updated_time"   content="<?php echo esc_attr($this->dateModified); ?>" />
            <?php
        }
        ?>

        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="<?php echo esc_attr($this->title); ?>">
        <meta name="twitter:description" content="<?php echo esc_attr($this->social_description); ?>">

        <?php
        if (!empty($meta_twitter)) {
            ?>
            <meta property="twitter:site" content="<?php echo esc_attr($meta_twitter) ?>">
            <meta name="twitter:creator" content="<?php echo esc_attr($meta_twitter); ?>">
            <?php
        }
        ?>
        <meta name="twitter:image" content="<?php echo esc_url($image_url); ?>">
        <?php
        if ($this->ldjson) {
            $stext = '';
            foreach ($this->ldjson as $content) {
                if (!empty($content)) {
                    $stext.= '<script type="application/ld+json">' . $content . '</script>' . PHP_EOL;
                }
            }
            echo $stext;
        }
    }

    private function set_single_post() {
        global $post;
        if ($post) {
            $post = get_post($post);
            $this->id = $post->ID;
            $this->set_post_meta_data($post->ID, true);

            $this->current_url = get_permalink($post->ID);
            $this->datePublished = get_the_date('', $post);
            $this->dateModified = get_the_modified_date('', $post);
            $tags = get_the_tags($post->ID);
            $arrtags = array();
            if ($tags) {
                foreach ($tags as $tag) {
                    $arrtags[] = $tag->name;
                }
            }
            $this->tags = $arrtags;
            $this->getBreadcrumbList($post);

            if ($post->post_type == 'product' && function_exists('is_woocommerce')) {

                $this->ProductWoocommerce();
            } else {
                $this->setNewsArticle();
            }
            $this->set_site_rating();

            $this->LocalBusiness_Post($post->ID);
            $this->FAQPage_Post($post->ID);
        }
    }

    private function FAQPage_Post($post_id = 0) {
        $enable = absint($this->get_post_meta('_kseo_post_faqpage_enable'));

        if (!$post_id || !$enable) {
            return;
        }
        $title = $this->get_post_meta('_kseo_post_faqpage_name');
        if (empty($title)) {
            $title = $this->title;
        }
        $Questions = array();
        $Questions = $this->set_question($Questions, 1);
        $Questions = $this->set_question($Questions, 2);
        $Questions = $this->set_question($Questions, 3);
        $Questions = $this->set_question($Questions, 4);
        $Questions = $this->set_question($Questions, 5);
        $Questions = $this->set_question($Questions, 6);
        $items = array(
            "@context" => "http://schema.org",
            "@type" => "FAQPage",
            "name" => esc_attr($title),
            "mainEntity" => $Questions
        );
        if ($Questions) {
            $this->set_ldJson($items);
        }
    }

    private function set_question($list, $key) {
        $Question = $this->get_post_meta('_kseo_post_faqpage_question_' . $key);
        $Answer = $this->get_post_meta('_kseo_post_faqpage_answer_' . $key);
        if (empty($Question) || empty($Answer)) {
            return $list;
        }

        $list[] = array(
            "@type" => "Question",
            "name" => esc_attr($Question),
            "answerCount" => 1,
            "acceptedAnswer" => array(
                "@type" => "Answer",
                "text" => esc_attr($Answer),
            )
        );
        return $list;
    }

    private function setNewsArticle() {
        if (!$this->thumbs) {
            $this->thumbs = $this->image;
        }
        $items = array(
            '@context' => "http://schema.org",
            "@type" => "NewsArticle",
            "mainEntityOfPage" => array(
                "@type" => "WebPage",
                "@id" => $this->current_url
            ),
            "headline" => esc_attr($this->title),
            "image" => $this->setThumb($this->thumbs),
            "datePublished" => $this->datePublished,
            "dateModified" => $this->dateModified,
            "description" => esc_attr($this->description),
            "author" => array(
                "@type" => "Person",
                "name" => $this->author
            )
        );

        if ($this->logo) {
            $logo = $this->logo;
            if (isset($logo['height'])) {
                unset($logo['height']);
            }
            if (isset($logo['width'])) {
                unset($logo['width']);
            }
            $items['publisher'] = array(
                "@type" => "Organization",
                "name" => esc_attr($this->sitename),
                "logo" => $logo
            );
        }

        $this->set_ldJson($items);
    }

    private function LocalBusiness_Post($post_id = 0) {
        $enable = absint($this->get_post_meta('_kseo_post_local_business_enable'));

        if (!$post_id || !$enable) {
            return;
        }
        $title = $this->get_post_meta('_kseo_post_local_business_title');
        if (empty($title)) {
            $title = $this->title;
        }
        $description = $this->get_post_meta('_kseo_post_local_business_description');
        if (empty($description)) {
            $description = $this->description;
        }
        $worstRating = absint($this->get_post_meta('_kseo_post_local_business_worstRating'));
        if (empty($worstRating)) {
            $worstRating = 1;
        }

        $reviewCount = absint($this->get_post_meta('_kseo_post_local_business_reviewCount')) + $this->site_rating_count;
        if (empty($reviewCount)) {
            $reviewCount = $this->site_rating_count + 1;
        } else {
            $reviewCount = $reviewCount + $this->site_rating_count;
        }

        $items = array(
            "@context" => "http://schema.org",
            "@type" => "LocalBusiness",
            "name" => esc_attr($title),
            "url" => $this->current_url,
            "description" => esc_attr($description),
            "aggregateRating" => array(
                "@type" => "AggregateRating",
                "ratingValue" => $this->site_rating_value,
                "reviewCount" => $reviewCount,
                "bestRating" => $this->site_best_rating,
                "worstRating" => $worstRating
            )
        );
        if ($this->thumbs) {
            $items['image'] = $this->thumbs;
        }

        $priceRange = $this->get_post_meta('_kseo_post_local_business_priceRange');
        if (!empty($priceRange)) {
            $items['priceRange'] = $priceRange;
        }
        $address = $this->get_post_meta('_kseo_post_local_business_address');
        if (!empty($address)) {
            $items['address'] = array(
                "@type" => "PostalAddress",
                "name" => $address,
            );
        }
        $telephone = $this->get_post_meta('_kseo_post_local_business_telephone');
        if (!empty($telephone)) {
            $items['telephone'] = $telephone;
        }
        $this->set_ldJson($items);
    }

    private function getBreadcrumbList($post = null) {
        if (!$post) {
            $post = get_post();
        }
        $categorylink = '';
        $category = get_category(get_query_var('cat'));
        if ($category) {
            $this->category_title = $category->name;
            $categorylink = get_category_link($category->term_id);
        }
        $breadcrumblist = array();
        $posison = 1;
        $breadcrumblist[] = array(
            "@type" => "ListItem",
            "position" => $posison,
            "item" => array(
                '@type' => 'WebSite',
                "@id" => home_url(),
                "name" => 'Home',
            )
        );

        if (!empty($categorylink)) {
            $posison++;
            $breadcrumblist[] = array(
                "@type" => "ListItem",
                "position" => $posison,
                "item" => array(
                    '@type' => 'WebPage',
                    "@id" => $categorylink,
                    "name" => $this->category_title,
                )
            );
        }
        $breadcrumblist[] = array(
            "@type" => "ListItem",
            "position" => $posison,
            "item" => array(
                '@type' => 'WebPage',
                "@id" => get_permalink($post->ID),
                "name" => $post->post_title,
            )
        );

        if ($breadcrumblist) {
            $items = array(
                '@context' => "http://schema.org",
                "@type" => "BreadcrumbList",
                "itemListElement" => $breadcrumblist
            );
            $this->set_ldJson($items);
        }
    }

    /**
     * 
     * @param type $key
     * @param type $id
     * @return type
     */
    public function get_post_meta($key, $id = 0) {
        if ($id) {
            return get_post_meta($id, $key, true);
        }
        return get_post_meta($this->id, $key, true);
    }

    public function get_queried_object() {
        $object = get_queried_object();
        if ($object && $object->ID) {
            $this->set_post_meta_data($object->ID, true);
        }
    }

    public function get_queried_home_object() {
        if (get_option('show_on_front') == 'page') {
            if (is_front_page()) {
                $post_id = get_option('page_on_front');
                $this->set_post_meta_data($post_id, false, true);
            } else {
                $post_id = get_option('page_for_posts');
                $this->set_post_meta_data($post_id);
            }
        }
    }

    public function set_post_meta_data($post_id, $auto_post_content = false, $is_check_home = false) {
        if ($post_id) {
            $post = get_post($post_id);

            $noindex = absint($this->get_post_meta('_kseo_post_noindex', $post_id));
            if ($noindex) {
                $this->meta_robots_index = 'noindex';
            }
            $nofollow = absint($this->get_post_meta('_kseo_post_nofollow', $post_id));
            if ($nofollow) {
                $this->meta_robots_follow = 'nofollow';
            }

            $enable_price = absint($this->get_post_meta('_kseo_post_product_enable_price', $post_id));
            if ($enable_price) {
                $price_from = $this->get_post_meta('_kseo_post_product_price_from', $post_id);
                $price_to = $this->get_post_meta('_kseo_post_product_price_to', $post_id);
                $price_currency = $this->get_post_meta('_kseo_post_product_price_currency', $post_id);
                if (empty($price_currency)) {
                    $price_currency = 'USD';
                }

                if ($price_to) {
                    $this->product_price = array(
                        '@type' => 'AggregateOffer',
                        'lowPrice' => $price_from,
                        'highPrice' => $price_to,
                        'priceCurrency' => $price_currency,
                        'offerCount' => 1,
                    );
                } else {
                    $y = absint(date("Y")) + 1;

                    $seller = $this->get_post_meta('_kseo_post_product_seller', $post_id);
                    if (empty($seller)) {
                        $seller = 'admin';
                    }
                    $this->product_price = array(
                        '@type' => 'Offer',
                        'priceCurrency' => $price_currency,
                        'url' => get_permalink($post),
                        'price' => $price_from,
                        "itemCondition" => "https://schema.org/UsedCondition",
                        'availability' => "http://schema.org/InStock",
                        'priceValidUntil' => $y . '-11-05',
                        'seller' => array(
                            '@type' => 'Organization',
                            'name' => $seller,
                        )
                    );
                }
            }

            $title = $this->get_post_meta('_kseo_post_title', $post_id);
            $image_id = $this->get_post_meta('_kseo_post_social_image', $post_id);
            $thumbs = $this->get_attachment($image_id);
            $meta_keywords = $this->get_post_meta('_kseo_post_meta_keywords');
            $description = $this->get_post_meta('_kseo_post_meta_description', $post_id);
            $social_description = $this->get_post_meta('_kseo_post_social_description', $post_id);
            if ($auto_post_content) {

                if (!$thumbs) {
                    $content = $this->run_shortcode($post_id, $post->post_content);
                    $thumbs = $this->images_url($post->ID, $content);
                }
                if (empty($description)) {
                    $content = $this->run_shortcode($post_id, $post->post_content);
                    $description = $this->gettext($content, $this->maximum_description_length);
                }
                if (empty($social_description)) {
                    $content = $this->run_shortcode($post_id, $post->post_content);
                    $social_description = $this->gettext($content, $this->maximum_social_length);
                }
            }

            if ($is_check_home) {
                if (empty($title)) {
                    $title = $this->get_option('_kseo_general_home_title');
                }
                if (!$thumbs) {
                    $home_image = $this->get_option('_kseo_social_home_image');
                    $thumbs = $this->get_attachment($home_image);
                }
                if (empty($description)) {
                    $description = $this->get_option('_kseo_general_home_description');
                }
            }
            if (!empty($title)) {
                $this->title = $title;
            } else {
                $this->title = $post->post_title;
            }
            if ($thumbs) {
                $this->thumbs = $thumbs;
            }
            if (!empty($description)) {
                $this->description = $description;
            }
            if (!empty($social_description)) {
                $this->social_description = $social_description;
            }
            $this->set_keywords($meta_keywords);
        }
    }

    public function meta_robots_paged() {
        global $page, $paged;
        // Add a page number if necessary.
        if (( $paged >= 2 || $page >= 2 ) && !is_404()) {
            if ($this->get_int_option('_kseo_general_noindex_paginated')) {
                $this->meta_robots_index = 'noindex';
            }
            if ($this->get_int_option('_kseo_general_nofollow_paginated')) {
                $this->meta_robots_follow = 'nofollow';
            }
            if ($this->get_int_option('_kseo_general_remove_descriptions_paginated')) {
                $this->is_remove_description = true;
            }
        }
    }

    private function canonical_generate() {
        $canonical = false;
        $canonical_override = false;

        // Set decent canonicals for homepage, singulars and taxonomy pages.
        if (is_singular()) {
            $obj = get_queried_object();
            $canonical = get_permalink($obj->ID);

            $canonical_unpaged = $canonical;

            $canonical_override = $this->get_post_meta('_kseo_post_canonical', $obj->ID);

            // Fix paginated pages canonical, but only if the page is truly paginated.
            if (get_query_var('page') > 1) {
                $num_pages = ( substr_count($obj->post_content, '<!--nextpage-->') + 1 );
                if ($num_pages && get_query_var('page') <= $num_pages) {
                    if (!$GLOBALS['wp_rewrite']->using_permalinks()) {
                        $canonical = add_query_arg('page', get_query_var('page'), $canonical);
                    } else {
                        $canonical = user_trailingslashit(trailingslashit($canonical) . get_query_var('page'));
                    }
                }
            }
        } else {
            if (is_search()) {
                $search_query = get_search_query();
                // Regex catches case when /search/page/N without search term is itself mistaken for search term. R.
                if (!empty($search_query) && !preg_match('|^page/\d+$|', $search_query)) {
                    $canonical = get_search_link();
                }
            } elseif (is_front_page()) {
                $canonical = home_url();
            } elseif (is_home() && get_option('show_on_front') === 'page') {

                $posts_page_id = get_option('page_for_posts');
                $canonical = $this->get_post_meta('_kseo_post_canonical', $posts_page_id);

                if (empty($canonical)) {
                    $canonical = get_permalink($posts_page_id);
                }
            } elseif (is_tax() || is_tag() || is_category()) {

                $term = get_queried_object();

                if (!empty($term) && !$this->is_multiple_terms_query()) {

                    $term_link = get_term_link($term, $term->taxonomy);

                    if (!is_wp_error($term_link)) {
                        $canonical = $term_link;
                    }
                }
            } elseif (is_post_type_archive()) {
                $post_type = $this->get_queried_post_type();
                $canonical = get_post_type_archive_link($post_type);
            } elseif (is_author()) {
                $canonical = get_author_posts_url(get_query_var('author'), get_query_var('author_name'));
            } elseif (is_archive()) {
                if (is_date()) {
                    if (is_day()) {
                        $canonical = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day'));
                    } elseif (is_month()) {
                        $canonical = get_month_link(get_query_var('year'), get_query_var('monthnum'));
                    } elseif (is_year()) {
                        $canonical = get_year_link(get_query_var('year'));
                    }
                }
            }

            $canonical_unpaged = $canonical;

            if ($canonical && get_query_var('paged') > 1) {
                global $wp_rewrite;
                if (!$wp_rewrite->using_permalinks()) {
                    if (is_front_page()) {
                        $canonical = trailingslashit($canonical);
                    }
                    $canonical = add_query_arg('paged', get_query_var('paged'), $canonical);
                } else {
                    if (is_front_page()) {
                        $canonical = home_url();
                    }
                    $canonical = user_trailingslashit(trailingslashit($canonical) . trailingslashit($wp_rewrite->pagination_base) . get_query_var('paged'));
                }
            }
        }

        $canonical_no_override = $canonical;

        if (is_string($canonical) && $canonical !== '') {
            // Force canonical links to be absolute, relative is NOT an option.
            if ($this->is_url_relative($canonical) === true) {
                $canonical = $this->base_url($canonical);
            }
        }

        if (is_string($canonical_override) && $canonical_override !== '') {
            $canonical = $canonical_override;
        }


        if (absint($this->get_option('_kseo_canonical_unpaged', 0))) {
            $canonical = $canonical_unpaged;
        }

        if (absint($this->get_option('_kseo_canonical_no_override', 0))) {
            $canonical = $canonical_no_override;
        }

        if (is_string($canonical) && '' !== $canonical) {
            $this->canonical = '<link rel="canonical" href="' . esc_url($canonical, null, 'other') . '" />' . "\n";
        }
    }

    private function is_multiple_terms_query() {

        global $wp_query;

        if (!is_tax() && !is_tag() && !is_category()) {
            return false;
        }

        $term = get_queried_object();
        $queried_terms = $wp_query->tax_query->queried_terms;

        if (empty($queried_terms[$term->taxonomy]['terms'])) {
            return false;
        }

        return count($queried_terms[$term->taxonomy]['terms']) > 1;
    }

    private function get_queried_post_type() {
        $post_type = get_query_var('post_type');
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }

        return $post_type;
    }

    private function base_url($path = null) {
        $url = get_option('home');

        $parts = wp_parse_url($url);

        $base_url = trailingslashit($parts['scheme'] . '://' . $parts['host']);

        if (!is_null($path)) {
            $base_url .= ltrim($path, '/');
        }

        return $base_url;
    }

    private function is_url_relative($url) {
        return ( strpos($url, 'http') !== 0 && strpos($url, '//') !== 0 );
    }

    private function set_list_posts() {
        if (is_404()) {
            if ($this->get_int_option('_kseo_general_noindex_404')) {
                $this->meta_robots_index = 'noindex';
            }
            $this->title = __('Page not found');
        } elseif (is_search()) {
            if ($this->get_int_option('_kseo_general_noindex_search')) {
                $this->meta_robots_index = 'noindex';
            }

            $this->title = sprintf(__('Search Results for &#8220;%s&#8221;'), get_search_query());
        } elseif (is_post_type_archive()) {
            if ($this->get_int_option('_kseo_general_noindex_archives')) {
                $this->meta_robots_index = 'noindex';
            }

            $this->title = post_type_archive_title('', false);
            $this->get_queried_object();
        } elseif (is_home()) {
            $this->title = single_post_title('', false);
            $this->get_queried_home_object();
        } elseif (is_category()) {

            if ($this->get_int_option('_kseo_general_noindex_categories')) {
                $this->meta_robots_index = 'noindex';
            }

            $this->title = single_term_title('', false);
            $desc = term_description();
            if (!empty($desc)) {
                $this->description = $this->gettext($desc, $this->maximum_description_length);
            }
        } elseif (is_tag()) {

            if ($this->get_int_option('_kseo_general_noindex_tag')) {
                $this->meta_robots_index = 'noindex';
            }

            $this->title = single_term_title('', false);
            $desc = term_description();
            if (!empty($desc)) {
                $this->description = $this->gettext($desc, $this->maximum_description_length);
            }
        } elseif (is_tax()) {

            if ($this->get_int_option('_kseo_general_noindex_tax')) {
                $this->meta_robots_index = 'noindex';
            }

            $this->title = single_term_title('', false);
            $desc = term_description();
            if (!empty($desc)) {
                $this->description = $this->gettext($desc, $this->maximum_description_length);
            }
        } elseif (is_author() && $author = get_queried_object()) {

            if ($this->get_int_option('_kseo_general_noindex_author')) {
                $this->meta_robots_index = 'noindex';
            }

            $this->title = $author->display_name;
            $desc = $author->description;
            if (!empty($desc)) {
                $this->description = $this->gettext($desc, $this->maximum_description_length);
            }
        } elseif (is_day()) {

            if ($this->get_int_option('_kseo_general_noindex_date')) {
                $this->meta_robots_index = 'noindex';
            }

            $this->title = get_the_date();
        } elseif (is_month()) {

            if ($this->get_int_option('_kseo_general_noindex_date')) {
                $this->meta_robots_index = 'noindex';
            }

            $this->title = get_the_date(_x('F Y', 'monthly archives date format'));
        } elseif (is_year()) {

            if ($this->get_int_option('_kseo_general_noindex_date')) {
                $this->meta_robots_index = 'noindex';
            }

            $this->title = get_the_date(_x('Y', 'monthly archives date format'));
        }
        $this->ItemListPosts();
    }

    private function ItemListPosts() {
        global $posts;
        $itemListElement = array();
        $i = 0;
        foreach ($posts as $post) {
            $title = $post->post_title;
            $url = get_permalink($post->ID);
            $image_id = $this->get_post_meta('_kseo_post_social_image', $post->ID);
            $thumb = $this->get_attachment($image_id);
            if (!$this->thumbs) {
                $content = $this->run_shortcode($post->ID, $post->post_content);
                $thumb = $this->images_url($post->ID, $content);
            }
            $description = $this->get_post_meta('_kseo_post_meta_description');
            if (empty($description)) {
                $content = $this->run_shortcode($post->ID, $post->post_content);
                $description = $this->gettext($content, $this->maximum_description_length);
            }
            if (empty($description)) {
                $description = $this->description;
            }


            $i++;
            $itemListElement[] = array(
                "@type" => "ListItem",
                "position" => $i,
                "url" => $url,
            );
        }

        $items = array(
            "@context" => "http://schema.org",
            "@type" => "ItemList",
            "itemListElement" => $itemListElement
        );

        $this->set_ldJson($items);
    }

    private function setThumb($thumbs = array()) {
        if (is_array($thumbs) && isset($thumbs['url'])) {
            return array($thumbs['url']);
        }
        return $thumbs;
    }

    private function ProductWoocommerce() {
        $items = array(
            "@context" => "http://schema.org/",
            "@type" => "Product",
            "@id" => $this->current_url,
            "url" => $this->current_url,
            "name" => esc_attr($this->title),
            "image" => $this->setThumb($this->thumbs),
            "description" => esc_attr($this->description),
            "mpn" => $this->id,
        );
        $this->set_ldJson($items);
    }

    private function RealEstateAgent() {
        $is_enable = absint($this->get_option('_kseo_agent_profile_enable', '0'));
        if (!$is_enable) {
            return;
        }
        $agent_name = $this->get_option('_kseo_agent_name', esc_attr($this->title));
        if (empty($agent_name)) {
            $agent_name = esc_attr($this->title);
        }
        $agent_description = $this->get_option('_kseo_agent_description', esc_attr($this->description));
        if (empty($agent_description)) {
            $agent_description = esc_attr($this->description);
        }
        $meta_image_id = $this->get_option('_kseo_agent_image');
        $image = $this->get_attachment($meta_image_id);

        $agent_address = $this->get_option('_kseo_agent_address', '');
        $agent_telephone = $this->get_option('_kseo_agent_telephone', '');

        $priceRange = $this->get_option('_kseo_agent_priceRange', '$$$');
        $worstRating = absint($this->get_option('_kseo_agent_worstRating', '1'));
        $ratingCount = absint($this->get_option('_kseo_agent_ratingCount', '1'));

        $items = array(
            "@context" => "http://schema.org",
            "@type" => "RealEstateAgent",
            "name" => $agent_name,
            "description" => $agent_description,
            "address" => $agent_address,
            "telephone" => $agent_telephone,
            "priceRange" => $priceRange,
            "aggregateRating" => array(
                "@type" => "AggregateRating",
                "ratingValue" => "5",
                "bestRating" => "5",
                "worstRating" => $worstRating,
                "ratingCount" => $ratingCount
            )
        );

        if ($image) {
            $items['image'] = $image;
        }

        $this->set_ldJson($items);
    }

    private function LocalBusiness() {
        $priceRange = $this->get_option('_kseo_geo_priceRange', '$$$');
        $worstRating = absint($this->get_option('_kseo_geo_worstRating', '1'));
        $ratingCount = absint($this->get_option('_kseo_geo_ratingCount', '1'));

        if (!$worstRating) {
            $worstRating = 1;
        }
        if (!$ratingCount) {
            $ratingCount = 1;
        }
        $items = array(
            "@context" => "http://schema.org",
            "@type" => "LocalBusiness",
            "name" => esc_attr($this->title),
            "url" => $this->homeURL,
            "description" => $this->description,
            "aggregateRating" => array(
                "@type" => "AggregateRating",
                "ratingValue" => "5",
                "bestRating" => "5",
                "worstRating" => $worstRating,
                "ratingCount" => $ratingCount
            )
        );
        if ($this->logo) {
            $items['image'] = $this->logo;
        }

        if (!empty($priceRange)) {
            $items['priceRange'] = $priceRange;
        }

        $this->telephone = $this->get_option('_kseo_geo_telephone');

        if (!empty($this->telephone)) {
            $items['telephone'] = esc_attr($this->telephone);
        }

        $address = $this->getPostalAddress();
        if ($address) {
            $items['address'] = $address;
        }
        $geo = $this->getGeoCoordinates();
        if ($geo) {
            $items['geo'] = $geo;
        }


        $this->set_ldJson($items);
    }

    private function getGeoCoordinates() {
        $meta_location = $this->get_option('_kseo_geo_location');
        if (!empty($meta_location)) {
            $locations = explode(',', $meta_location);
            if (isset($locations[1])) {
                return array(
                    "@type" => "GeoCoordinates",
                    "latitude" => doubleval($locations[0]),
                    "longitude" => doubleval($locations[1])
                );
            }
        }
        return false;
    }

    private function getPostalAddress() {
        $meta_address = $this->get_option('_kseo_geo_address');
        if (!empty($meta_address)) {
            return array(
                "@type" => "PostalAddress",
                "streetAddress" => $meta_address,
                "addressLocality" => $this->get_option('_kseo_geo_city'),
                "addressRegion" => $this->get_option('_kseo_geo_region'),
                "postalCode" => $this->get_option('_kseo_geo_postalcode'),
                "addressCountry" => $this->get_option('_kseo_geo_country')
            );
        }
        return false;
    }

    /**
     * 
     * @param type $name
     * @param type $default
     * @return type
     */
    private function get_option($name, $default = '') {
        return get_option($name, $default);
    }

    /**
     * 
     * @param type $name
     * @param type $default
     * @return type
     */
    private function get_int_option($name, $default = '0') {
        $value = get_option($name, $default);
        return absint($value);
    }

    private function get_Product_Rates_Woocommerce($product_id) {
        global $wpdb;
        if (!$rates = wp_cache_get('rates_' + $product_id, 'product_rates_woocommerce')) {
            $rating_counts = $wpdb->get_results($wpdb->prepare("SELECT meta_value, COUNT(*) as counted FROM {$wpdb->prefix}commentmeta AS metas LEFT JOIN {$wpdb->prefix}comments AS comments ON metas.comment_id = comments.comment_ID WHERE meta_key = 'rating' AND comment_post_ID = %d GROUP BY meta_value", $product_id));
            $rates = array(
                '5' => array(),
                '4' => array(),
                '3' => array(),
                '2' => array(),
                '1' => array(),
            );
            if (!empty($rating_counts)) {
                foreach ($rating_counts as $item) {
                    if (isset($rates[$item->meta_value])) {
                        $rates[$item->meta_value] = $item;
                    }
                }
            }

            wp_cache_add('rates_' + $product_id, $rates, 'product_rates_woocommerce', DAY_IN_SECONDS);
        }
        return $rates;
    }

    private function related_post($post_id) {
        $args = array('posts_per_page' => 4, 'category__in' => wp_get_post_categories($post_id), 'post__not_in' => array($post_id));
        return query_posts($args);
    }

    private function getSocials() {
        $seo_social = $this->get_option('_kseo_social_profile_links');
        if (!empty($seo_social)) {
            $links = explode("\n", str_replace("\r", "", $seo_social));
            $items = array();
            foreach ($links as $link) {
                if (!empty($link)) {
                    $items[] = trim($link);
                }
            }
            return $items;
        }
        return array();
    }

    private function ishome() {
        return ( is_front_page() && !is_home() ) || ( is_home() && is_front_page());
    }

    private function set_ldJson($items) {
        if (is_array($items)) {
            $datas = $this->ldjson;
            $text = wp_json_encode($items);
            array_push($datas, $text);
            $this->ldjson = $datas;
        }
    }

    private function set_keywords($meta_keywords = '') {
        if (!empty($meta_keywords)) {
            $keywords_string = $meta_keywords;
        } else {
            $keywords_string = $this->get_option('_kseo_general_meta_keywords');
        }
        if (!empty($keywords_string)) {
            $arr_keywords = explode("\n", str_replace("\r", "", $keywords_string));
            $items_keywords = array();
            foreach ($arr_keywords as $link) {
                if (!empty($link)) {
                    $items_keywords[] = trim($link);
                }
            }
            $this->keywords = implode(', ', $items_keywords);
        }
    }

    private function gettext($introtext, $maxchars = 133) {
        $string = $introtext;
        $string = preg_replace('/<[^>]*>/', ' ', $string);
        $string = str_replace("\r", '', $string);
        $string = str_replace("\n", ' ', $string);
        $string = str_replace("\t", ' ', $string);
        $string = trim(preg_replace('/ {2,}/', ' ', $string));
        if (strlen($string) <= $maxchars) {
            $textIntro = $string;
        } else {
            if (strpos($string, " ", $maxchars) > $maxchars) {
                $newmaxchars = strpos($string, " ", $maxchars);
                $newstring = substr($string, 0, $newmaxchars) . "...";
                $textIntro = $newstring;
            } else {
                $newstring = substr($string, 0, $maxchars) . "...";
                $textIntro = $newstring;
            }
        }
        // clean up globals
        return trim($textIntro);
    }

    private function WebSite() {
        $this->title = $this->sitename;
        $items = array(
            '@context' => "http://schema.org",
            "@type" => "WebSite",
            "url" => $this->homeURL,
            "name" => $this->sitename,
            "alternateName" => esc_attr($this->description),
            "potentialAction" => array(
                "@type" => "SearchAction",
                "target" => $this->homeURL . "?s={search_term_string}",
                "query-input" => "required name=search_term_string"
            )
        );
        $this->set_ldJson($items);
    }

    private function Organization() {
        $meta_telephone = $this->get_option('_kseo_geo_telephone');
        if (!empty($meta_telephone)) {
            $contact_type = $this->get_option('_kseo_geo_page_contact_type');
            if (empty($contact_type)) {
                $contact_type = 'customer service';
            }
            $items = array(
                '@context' => "http://schema.org",
                "@type" => "Organization",
                "url" => $this->current_url,
                "logo" => $this->logo,
                "contactPoint" => array(
                    "@type" => "ContactPoint",
                    "telephone" => $meta_telephone,
                    "contactType" => $contact_type
                )
            );
            $this->telephone = $meta_telephone;
            $this->set_ldJson($items);
        }
    }

    private function Person() {
        $socials = $this->getSocials();
        if ($socials) {
            $items = array(
                '@context' => "http://schema.org",
                "@type" => "Person",
                "name" => $this->author,
                "url" => $this->current_url,
                "sameAs" => $socials
            );

            $this->set_ldJson($items);
        }
    }

    public function wp_title() {

        // If it's a 404 page, use a "Page not found" title.
        if (is_404()) {
            $title = __('Page not found');
            return $this->get_404_title_format($title);
            // If it's a search, use a dynamic search results title.
        } elseif (is_search()) {
            /* translators: %s: search phrase */
            $title = sprintf(__('Search Results for &#8220;%s&#8221;'), get_search_query());

            return $this->get_search_title_format($title);
            // If on the front page, use the site title.
        } elseif (is_front_page()) {
            return $this->get_home_title_format($this->bloginfo_name);
            // If on a post type archive, use the post type archive title.
        } elseif (is_post_type_archive()) {
            $title = post_type_archive_title('', false);

            return $this->get_archive_title_format($title);
            // If on a taxonomy archive, use the term title.
        } elseif (is_tax()) {
            $title = single_term_title('', false);

            return $this->get_tax_title_format($title);
            /*
             * If we're on the blog page that is not the homepage or
             * a single post of any post type, use the post title.
             */
        } elseif (is_home()) {

            $title = single_post_title('', false);

            return $this->get_home_title_format($title);
            // If on a category or tag archive, use the term title.
        } elseif (is_page()) {
            $title = single_post_title('', false);


            return $this->get_page_title_format($title);
            // If on a category or tag archive, use the term title.
        } elseif (is_singular()) {

            $title = single_post_title('', false);

            return $this->get_post_title_format($title);
            // If on a category or tag archive, use the term title.
        } elseif (is_category()) {
            $title = single_term_title('', false);

            return $this->get_category_title_format($title);
            // If on an author archive, use the author's display name.
        } elseif (is_tag()) {
            $title = single_term_title('', false);

            return $this->get_tag_title_format($title);
            // If on an author archive, use the author's display name.
        } elseif (is_author() && $author = get_queried_object()) {
            $title = $author->display_name;
            return $this->get_author_title_format($title);
            // If it's a date archive, use the date as the title.
        } elseif (is_day() || is_year() || is_month()) {
            $title = get_the_date();
            return $this->get_date_title_format($title);
        }

        $title = $this->get_default_title($this->bloginfo_name);
        return $this->title_return($title);
    }

    public function get_date_title_format($default = '') {
        $title_format = $this->get_option('_kseo_general_author_title_format', '%date% | %blog_title%');
        if (!empty($title_format)) {
            $titles = explode(' ', $title_format);
            foreach ($titles as $k => $val) {
                switch ($val) {
                    case '%blog_title%':
                        $titles[$k] = $this->bloginfo_name;
                        break;
                    case '%blog_description%':
                        $titles[$k] = $this->bloginfo_description;
                        break;
                    case '%date%':
                        $titles[$k] = get_the_date();
                        break;
                    case '%day%':
                        $titles[$k] = get_the_date(_x('d', 'Day archives date format'));
                        break;
                    case '%month%':
                        $titles[$k] = get_the_date(_x('F Y', 'monthly archives date format'));
                        break;
                    case '%year%':
                        $titles[$k] = get_the_date(_x('Y', 'yearly archives date format'));
                        break;
                    default:
                        break;
                }
            }
            $str = implode(' ', $titles);
            $title = trim($str);
            if (!empty($title)) {
                return $this->title_return($title);
            }
        }
        $title = $this->get_default_title($default);
        return $this->title_return($title);
    }

    public function get_author_title_format($default = '') {
        $title_format = $this->get_option('_kseo_general_author_title_format', '%author% | %blog_title%');
        if (!empty($title_format)) {
            $titles = explode(' ', $title_format);
            foreach ($titles as $k => $val) {
                switch ($val) {
                    case '%blog_title%':
                        $titles[$k] = $this->bloginfo_name;
                        break;
                    case '%blog_description%':
                        $titles[$k] = $this->bloginfo_description;
                        break;
                    case '%author%':
                        $titles[$k] = $default;
                        break;
                    default:
                        break;
                }
            }
            $str = implode(' ', $titles);
            $title = trim($str);
            if (!empty($title)) {
                return $this->title_return($title);
            }
        }
        $title = $this->get_default_title($default);
        return $this->title_return($title);
    }

    public function get_tag_title_format($default = '') {
        $title_format = $this->get_option('_kseo_general_tag_title_format', '%tag% | %blog_title%');
        if (!empty($title_format)) {
            $titles = explode(' ', $title_format);
            foreach ($titles as $k => $val) {
                switch ($val) {
                    case '%blog_title%':
                        $titles[$k] = $this->bloginfo_name;
                        break;
                    case '%blog_description%':
                        $titles[$k] = $this->bloginfo_description;
                        break;
                    case '%tag%':
                        $titles[$k] = $default;
                        break;
                    default:
                        break;
                }
            }
            $str = implode(' ', $titles);
            $title = trim($str);
            if (!empty($title)) {
                return $this->title_return($title);
            }
        }
        $title = $this->get_default_title($default);
        return $this->title_return($title);
    }

    public function get_category_title_format($default = '') {
        $title_format = $this->get_option('_kseo_general_category_title_format', '%category_title% | %blog_title%');
        if (!empty($title_format)) {
            $titles = explode(' ', $title_format);
            foreach ($titles as $k => $val) {
                switch ($val) {
                    case '%blog_title%':
                        $titles[$k] = $this->bloginfo_name;
                        break;
                    case '%blog_description%':
                        $titles[$k] = $this->bloginfo_description;
                        break;
                    case '%category_title%':
                        $titles[$k] = $default;
                        break;
                    case '%category_description%':
                        $desc = term_description();
                        $titles[$k] = $this->gettext($desc, $this->maximum_title_length);
                        break;
                    default:
                        break;
                }
            }
            $str = implode(' ', $titles);
            $title = trim($str);
            if (!empty($title)) {
                return $this->title_return($title);
            }
        }
        $title = $this->get_default_title($default);
        return $this->title_return($title);
    }

    public function get_page_title_format($default = '') {
        global $post;
        if ($post) {
            $title = $this->get_post_meta('_kseo_post_title', $post->ID);
            if (!empty($title)) {
                $default = $title;
            }
        }
        $title_format = $this->get_option('_kseo_general_page_title_format', '%page_title% | %blog_title%');
        if (!empty($title_format)) {
            $titles = explode(' ', $title_format);
            foreach ($titles as $k => $val) {
                switch ($val) {
                    case '%blog_title%':
                        $titles[$k] = $this->bloginfo_name;
                        break;
                    case '%blog_description%':
                        $titles[$k] = $this->bloginfo_description;
                        break;
                    case '%page_title%':
                        $titles[$k] = $default;
                        break;
                    case '%post_date%':
                        $titles[$k] = get_the_date();
                        break;
                    case '%post_year%':
                        $titles[$k] = get_the_date('Y');
                        break;
                    case '%post_month%':
                        $titles[$k] = get_the_date('F');
                        break;
                    default:
                        break;
                }
            }
            $str = implode(' ', $titles);
            $title = trim($str);
            if (!empty($title)) {
                return $this->title_return($title);
            }
        }
        $title = $this->get_default_title($default);
        return $this->title_return($title);
    }

    public function get_post_title_format($default = '') {
        global $post;
        if ($post) {
            $title = $this->get_post_meta('_kseo_post_title', $post->ID);
            if (!empty($title)) {
                $default = $title;
            }
        }
        $title_format = $this->get_option('_kseo_general_post_title_format', '%post_title% | %blog_title%');
        if (!empty($title_format)) {
            $titles = explode(' ', $title_format);
            foreach ($titles as $k => $val) {
                switch ($val) {
                    case '%blog_title%':
                        $titles[$k] = $this->bloginfo_name;
                        break;
                    case '%blog_description%':
                        $titles[$k] = $this->bloginfo_description;
                        break;
                    case '%post_title%':
                        $titles[$k] = $default;
                        break;
                    case '%category_title%':
                        $titles[$k] = get_cat_name(get_query_var('cat'));
                        break;
                    case '%post_date%':
                        $titles[$k] = get_the_date();
                        break;
                    case '%post_year%':
                        $titles[$k] = get_the_date('Y');
                        break;
                    case '%post_month%':
                        $titles[$k] = get_the_date('F');
                        break;
                    default:
                        break;
                }
            }
            $str = implode(' ', $titles);
            $title = trim($str);
            if (!empty($title)) {
                return $this->title_return($title);
            }
        }
        $title = $this->get_default_title($default);
        return $this->title_return($title);
    }

    public function get_tax_title_format($default = '') {
        $title_format = $this->get_option('_kseo_general_tax_title_format', '%tax_title% | %blog_title%');
        if (!empty($title_format)) {
            $titles = explode(' ', $title_format);
            foreach ($titles as $k => $val) {
                switch ($val) {
                    case '%blog_title%':
                        $titles[$k] = $this->bloginfo_name;
                        break;
                    case '%blog_description%':
                        $titles[$k] = $this->bloginfo_description;
                        break;
                    case '%tax_title%':
                        $titles[$k] = $default;
                        break;
                    default:
                        break;
                }
            }
            $str = implode(' ', $titles);
            $title = trim($str);
            if (!empty($title)) {
                return $this->title_return($title);
            }
        }
        $title = $this->get_default_title($default);
        return $this->title_return($title);
    }

    public function get_archive_title_format($default = '') {
        $title_format = $this->get_option('_kseo_general_archive_title_format', '%archive_title% | %blog_title%');
        if (!empty($title_format)) {
            $titles = explode(' ', $title_format);
            foreach ($titles as $k => $val) {
                switch ($val) {
                    case '%blog_title%':
                        $titles[$k] = $this->bloginfo_name;
                        break;
                    case '%blog_description%':
                        $titles[$k] = $this->bloginfo_description;
                        break;
                    case '%archive_title%':
                        $post = get_queried_object();
                        $title_meta = '';
                        if ($post) {
                            $title_meta = $this->get_post_meta('_kseo_post_title', $post->ID);
                        }
                        $titles[$k] = $default;
                        if (!empty($title_meta)) {
                            $titles[$k] = $title_meta;
                        }
                        break;
                    default:
                        break;
                }
            }
            $str = implode(' ', $titles);
            $title = trim($str);
            if (!empty($title)) {
                return $this->title_return($title);
            }
        }
        $title = $this->get_default_title($default);
        return $this->title_return($title);
    }

    public function get_search_title_format($default = '') {
        $title_format = $this->get_option('_kseo_general_search_title_format', '%search% | %blog_title%');
        if (!empty($title_format)) {
            $titles = explode(' ', $title_format);
            foreach ($titles as $k => $val) {
                switch ($val) {
                    case '%blog_title%':
                        $titles[$k] = $this->bloginfo_name;
                        break;
                    case '%blog_description%':
                        $titles[$k] = $this->bloginfo_description;
                        break;
                    case '%search%':
                        $titles[$k] = get_search_query();
                        break;
                    default:
                        break;
                }
            }
            $str = implode(' ', $titles);
            $title = trim($str);
            if (!empty($title)) {
                return $this->title_return($title);
            }
        }
        $title = $this->get_default_title($default);
        return $this->title_return($title);
    }

    public function get_404_title_format($default = '') {
        $title_format = $this->get_option('_kseo_general_404_title_format', 'Nothing found for %request_words%');
        if (!empty($title_format)) {
            $titles = explode(' ', $title_format);
            foreach ($titles as $k => $val) {
                switch ($val) {
                    case '%blog_title%':
                        $titles[$k] = $this->bloginfo_name;
                        break;
                    case '%blog_description%':
                        $titles[$k] = $this->bloginfo_description;
                        break;
                    case '%request_words%':
                        $titles[$k] = $this->request_as_words($_SERVER['REQUEST_URI']);
                        break;
                    case '%404_title%':
                        $titles[$k] = $default;
                        break;
                    case '%request_url%':
                        $titles[$k] = $_SERVER['REQUEST_URI'];
                        break;
                    default:
                        break;
                }
            }
            $str = implode(' ', $titles);
            $title = trim($str);
            if (!empty($title)) {
                return $this->title_return($title);
            }
        }
        $title = $this->get_default_title($default);
        return $this->title_return($title);
    }

    public function get_home_title_format($default = '') {
        $title_format = $this->get_option('_kseo_general_home_title_format', '%page_title% | %blog_description%');

        if (!empty($title_format)) {
            $titles = explode(' ', $title_format);
            foreach ($titles as $k => $val) {
                switch ($val) {
                    case '%blog_title%':
                        $titles[$k] = $this->bloginfo_name;
                        break;
                    case '%blog_description%':
                        $titles[$k] = $this->bloginfo_description;
                        break;
                    case '%page_title%':
                        $titles[$k] = $this->get_home_title();
                        break;
                    default:
                        break;
                }
            }
            $str = implode(' ', $titles);
            $title = trim($str);
            if (!empty($title)) {
                return $this->title_return($title);
            }
        }
        $title = $this->get_default_title($default);
        return $this->title_return($title);
    }

    public function get_default_title($title) {
        $titles = array(
            'title' => $title
        );
        if (is_front_page()) {
            $titles['tagline'] = $this->bloginfo_description;
        } else {
            $titles['site'] = $this->bloginfo_name;
        }
        /**
         * Filters the separator for the document title.
         *
         * @since 4.4.0
         *
         * @param string $sep Document title separator. Default '-'.
         */
        $sep = apply_filters('document_title_separator', '-');

        /**
         * Filters the parts of the document title.
         *
         * @since 4.4.0
         *
         * @param array $title {
         *     The document title parts.
         *
         *     @type string $title   Title of the viewed page.
         *     @type string $page    Optional. Page number if paginated.
         *     @type string $tagline Optional. Site description when on home page.
         *     @type string $site    Optional. Site title when not on home page.
         * }
         */
        $titles = apply_filters('document_title_parts', $titles);

        return implode(" $sep ", array_filter($titles));
    }

    public function get_home_title() {
        $home_title = $this->get_option('_kseo_general_home_title');
        if (get_option('show_on_front') == 'page') {
            if (is_front_page()) {
                $post_id = get_option('page_on_front');
                $title = get_post_meta($post_id, '_kseo_post_title', true);
                if (!empty($title)) {
                    return $title;
                }
            } else {
                $post_id = get_option('page_for_posts');
                $title = get_post_meta($post_id, '_kseo_post_title', true);
                if (!empty($title)) {
                    return $title;
                } else {
                    $post = get_post($post_id);
                    if ($post) {
                        return $post->post_title;
                    }
                }
            }
        }
        if (!empty($home_title)) {
            return $home_title;
        }
        return $this->bloginfo_name;
    }

    public function title_return($title) {
        global $page, $paged;
        // Add a page number if necessary.
        if (( $paged >= 2 || $page >= 2 ) && !is_404()) {
            $paged_format = $this->get_option('_kseo_general_paged_title_format');
            if ($paged_format) {
                $paged_text = str_replace('%page%', '%s', $paged_format);
                $title .= sprintf($paged_text, max($paged, $page));
            } else {
                $title .= sprintf(__(' - Page %s'), max($paged, $page));
            }
        }

        $title = wptexturize($title);
        $title = convert_chars($title);
        $title = esc_html($title);
        $title = capital_P_dangit($title);
        return $title;
    }

    /**
     * @param $request
     *
     * @return User -readable nice words for a given request.
     */
    public function request_as_words($request) {
        $request = htmlspecialchars($request);
        $request = str_replace('.html', ' ', $request);
        $request = str_replace('.htm', ' ', $request);
        $request = str_replace('.', ' ', $request);
        $request = str_replace('/', ' ', $request);
        $request = str_replace('-', ' ', $request);
        $request_a = explode(' ', $request);
        $request_new = array();
        foreach ($request_a as $token) {
            $request_new[] = $this->ucwords(trim($token));
        }
        $request = implode(' ', $request_new);

        return $request;
    }

    public function ucwords($str) {
        static $charset = null;
        if ($charset == null) {
            $charset = get_bloginfo('charset');
        }
        if (function_exists('mb_convert_case')) {
            return mb_convert_case($str, MB_CASE_TITLE, $charset);
        } else {
            return ucwords($str);
        }
    }

    /**
     * 
     * @param type $attachment_id
     * @param type $size
     * @param type $icon
     * @return boolean
     */
    public function get_attachment($attachment_id, $size = 'large', $icon = false) {
        if ($attachment_id) {
            $image = wp_get_attachment_image_src($attachment_id, $size, $icon);
            if ($image) {
                list($src, $width, $height) = $image;
                return array(
                    "@type" => "ImageObject",
                    'url' => $src,
                    'width' => $width,
                    'height' => $height
                );
            }
        }
        return false;
    }

    private function images_url($id, $text = '') {
        $thumbnail_id = get_post_thumbnail_id($id);
        if ($thumbnail_id) {
            return $this->get_attachment($thumbnail_id);
        } else {
            $images = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID'));
            if ($images) {
                foreach ($images as $img) {
                    return $this->get_attachment($img->ID);
                }
            } else {
                return $this->get_image_from_string($text);
            }
        }
        return $this->image;
    }

    public function get_image_from_string($text) {
        $searchTags = array(
            'img' => '/<img[^>]+>/i',
            'input' => '/<input[^>]+type\s?=\s?"image"[^>]+>/i'
        );
        $searchSrc = '/src\s?=\s?"([^"]*)"/i';
        $imagesintro = array();
        foreach ($searchTags as $tag => $regex) {
            preg_match_all($regex, $text, $m);
            if (count($m)) {
                foreach ($m[0] as $htmltag) {
                    preg_match_all($searchSrc, $htmltag, $msrc);
                    if (count($msrc) && isset($msrc[1])) {
                        foreach ($msrc[1] as $src) {
                            array_push($imagesintro, $src);
                        }
                    }
                    $text = str_replace($htmltag, '', $text);
                }
            }
        }

        if ($imagesintro) {
            $width = 500;
            $height = 300;
            $src = $imagesintro[0];
            if (function_exists('getimagesize')) {
                ini_set('allow_url_fopen', 1);
                list($width, $height) = getimagesize($src);
            }
            return array(
                "@type" => "ImageObject",
                'url' => $src,
                'width' => $width,
                'height' => $height
            );
        }
        return false;
    }

    public function run_shortcode($post_id, $content) {

        if (isset(self::$content_shortcode[$post_id])) {
            return self::$content_shortcode[$post_id];
        }
        ob_start();
        echo do_shortcode($content);
        $html = ob_get_clean();
        $return = preg_replace('#<(script|style|option|textarea)[^>]*>.*?</\1>#si', '', $html);
        self::$content_shortcode[$post_id] = $return;
        return $return;
    }

}

$kadSEO = new KadsSeo();

