<?php

class WPCareLabShortcodes
{
    public function __construct()
    {
        add_shortcode('post_excerpt', array(&$this, 'post_excerpt'));
        add_shortcode('post_title', array(&$this, 'post_title'));
        add_shortcode('post_permalink', array(&$this, 'post_permalink'));
        add_shortcode('post_price', array(&$this, 'post_price'));

        // Enable shortcodes in text widgets
        add_filter('widget_text', 'do_shortcode');
    }

    public function post_excerpt($id) {
        return preg_split('/\<!\-\-snippet\-\-\>/', get_post_field('post_content', $id[0]), 2)[0];
    }

    public function post_title($id) {
        return get_the_title($id[0]);
    }

    public function post_permalink($id) {
        return get_permalink($id[0]);
    }

    public function post_price($id) {
        //let's apply filter so addons can change price
        $item_tags = array( 'price' => get_post_meta( $id[0], 'asp_product_price', true ) );

        $item_tags = apply_filters( 'asp_product_tpl_tags_arr', $item_tags, $id[0] );
        return str_replace("/month", "<small class=\"text-muted fw-light\">/mo</small>", $item_tags['price']);
    }

}

new WPCareLabShortcodes();