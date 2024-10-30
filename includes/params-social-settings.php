<?php
function kseo_params_social_settings() {
    return array(
        'kseo-group-all' => array(
            'label' => __('SEO Setting For All Page', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_social_profile_links' => array(
                    'label' => __('Social Profile Links:', 'kseo'),
                    'default' => '',
                    'type' => 'textarea',
                    'desc' => 'Add URLs for your website\'s social profiles here (Facebook, Twitter, Google+, Instagram, LinkedIn), one per line.'
                ),
                '_kseo_social_profile_google' => array(
                    'label' => __('Google Profile:', 'kseo'),
                    'default' => '',
                    'type' => 'text'
                ),
                '_kseo_social_profile_facebook' => array(
                    'label' => __('Facebook Profile:', 'kseo'),
                    'default' => '',
                    'type' => 'text'
                ),
                '_kseo_social_profile_twitter' => array(
                    'label' => __('Twitter Profile:', 'kseo'),
                    'default' => '',
                    'type' => 'text'
                ),
            )
        ),
        'kseo-group-real-estate-agent' => array(
            'label' => __('SEO Setting For Real Estate Agent', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_agent_profile_enable' => array(
                    'label' => __('Show Real Estate Agent Infomation:', 'kseo'),
                    'default' => '0',
                    'type' => 'yesno',
                    'desc' => __('Show Real Estate Agent Infomation.', 'kseo')
                ),
                '_kseo_agent_name' => array(
                    'label' => __('Agent Name:', 'kseo'),
                    'default' => '',
                    'type' => 'text'
                ),
                '_kseo_agent_description' => array(
                    'label' => __('Agent Description:', 'kseo'),
                    'default' => '',
                    'type' => 'textarea',
                    'desc' => ''
                ),
                '_kseo_agent_image' => array(
                    'label' => __('Agent Image', 'kseo'),
                    'default' => '',
                    'type' => 'image'
                ),
                '_kseo_agent_address' => array(
                    'label' => __('Agent Address:', 'kseo'),
                    'default' => '',
                    'type' => 'text'
                ),
                '_kseo_agent_telephone' => array(
                    'label' => __('Agent Telephone:', 'kseo'),
                    'default' => '',
                    'type' => 'text'
                ),
                '_kseo_agent_priceRange' => array(
                    'label' => __('Price Range:', 'kseo'),
                    'default' => '$$$',
                    'type' => 'text',
                    'desc' => 'Price'
                ),
                '_kseo_agent_worstRating' => array(
                    'label' => __('Worst Rating:', 'kseo'),
                    'default' => '1',
                    'type' => 'text',
                    'desc' => 'Worst Rating '
                ),
                '_kseo_agent_ratingCount' => array(
                    'label' => __('Rating Count:', 'kseo'),
                    'default' => '1',
                    'type' => 'text',
                    'desc' => 'Rating Count '
                ),
            )
        ),
        'kseo-group-geo' => array(
            'label' => __('GEO Settings', 'kseo'),
            'type' => 'group',
            'items' => array(
                '_kseo_geo_page_contact' => array(
                    'label' => __('Page Contact', 'kseo'),
                    'default' => '',
                    'type' => 'page',
                    'desc' => __('Page Contact for setting contact infomation', 'kseo')
                ),
                '_kseo_geo_page_contact_type' => array(
                    'label' => __('Contact Type:', 'kseo'),
                    'default' => 'customer support',
                    'options' => array(
                        '' => __(' - Choose Contact Type - ', 'kseo'),
                        'customer support' => __('Customer Support', 'kseo'),
                        'technical support' => __('Technical Support', 'kseo'),
                        'billing support' => __('Billing Support', 'kseo'),
                        'bill payment' => __('Bill Payment', 'kseo'),
                        'sales' => __('Sales', 'kseo'),
                        'reservations' => __('Reservations', 'kseo'),
                        'credit card support' => __('Credit Card Support', 'kseo'),
                        'emergency' => __('Emergency', 'kseo'),
                        'baggage tracking' => __('Baggage Tracking', 'kseo'),
                        'roadside assistance' => __('Roadside Assistance', 'kseo'),
                        'package tracking' => __('Package Tracking', 'kseo')
                    ),
                    'type' => 'select',
                    'desc' => __('One of the following values, not case sensitive. (Additional contact types may be supported later.', 'kseo')
                    . '<br><a target="_blank" href="https://developers.google.com/search/docs/data-types/corporate-contact">' . __('See More At', 'kseo') . '</a>'
                ),
                '_kseo_geo_contact_type' => array(
                    'label' => __('Person Or Organization:', 'kseo'),
                    'default' => 'person',
                    'options' => array(
                        'person' => __('Person', 'kseo'),
                        'organization' => __('Organization', 'kseo')
                    ),
                    'type' => 'radio'
                ),
                '_kseo_geo_author_name' => array(
                    'label' => __('Author Name', 'kseo'),
                    'default' => '',
                    'type' => 'text'
                ),
                '_kseo_geo_priceRange' => array(
                    'label' => __('Price Range:', 'kseo'),
                    'default' => '$$$',
                    'type' => 'text',
                    'desc' => 'Price Range for Contact Point and Customer Service'
                ),
                '_kseo_geo_worstRating' => array(
                    'label' => __('Worst Rating:', 'kseo'),
                    'default' => '1',
                    'type' => 'text',
                    'desc' => 'Worst Rating for Contact Point and Customer Service'
                ),
                '_kseo_geo_ratingCount' => array(
                    'label' => __('Rating Count:', 'kseo'),
                    'default' => '1',
                    'type' => 'text',
                    'desc' => 'Worst Rating for Contact Point and Customer Service'
                ),
                '_kseo_geo_location' => array(
                    'label' => __('Location:', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'desc' => 'Set Geo location in form LATITUDE,LONGITUDE.'
                ),
                '_kseo_geo_address' => array(
                    'label' => __('Address:', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'desc' => ''
                ),
                '_kseo_geo_city' => array(
                    'label' => __('City:', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_geo_region' => array(
                    'label' => __('Region:', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'desc' => ''
                ),
                '_kseo_geo_postalcode' => array(
                    'label' => __('PostalCode:', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                ),
                '_kseo_geo_country' => array(
                    'label' => __('Country:', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'desc' => ''
                ),
                '_kseo_geo_telephone' => array(
                    'label' => __('Telephone:', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'desc' => 'Telephone for Contact Point and Customer Service'
                ),
                '_kseo_geo_email' => array(
                    'label' => __('Email:', 'kseo'),
                    'default' => '',
                    'type' => 'text',
                    'desc' => 'Email for Contact Point and Customer Service'
                ),
            )
        ),
    );
}
