<?php

function disable_emoji_feature()
{
    // Prevent Emoji from loading on the front-end
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');

    // Remove from admin area also
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');

    // Remove from RSS feeds also
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');

    // Remove from Embeds
    remove_filter('embed_head', 'print_emoji_detection_script');

    // Remove from emails
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // Disable from TinyMCE editor. Currently disabled in block editor by default
    add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');

    /**
     * Finally, prevent character conversion too
     * without this, emojis still work 
     * if it is available on the user's device
     */
    add_filter('option_use_smilies', '__return_false');
}

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar()
{
    if (is_user_in_role(array('client'))) {
        show_admin_bar(false);
    }
}

add_action(
    'wp_enqueue_scripts', function () {
        my_remove_class_action('wp_footer', 'WP_Members', 'do_loginout_script', 50);
    }, 20
);


add_filter(
    'after_setup_theme', function () {
        my_remove_class_action('init', 'AcceptStripePayments_Blocks', 'register_block');
    }
);

/**
 * Make sure the function does not exist before defining it
 */
if (!function_exists('remove_class_filter')) {
    /**
     * Remove Class Filter Without Access to Class Object
     *
     * In order to use the core WordPress remove_filter() on a filter added with the callback
     * to a class, you either have to have access to that class object, or it has to be a call
     * to a static method.  This method allows you to remove filters with a callback to a class
     * you don't have access to.
     *
     * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
     * Updated 2-27-2017 to use internal WordPress removal for 4.7+ (to prevent PHP warnings output)
     *
     * @param string $tag         Filter to remove
     * @param string $class_name  Class name for the filter's callback
     * @param string $method_name Method name for the filter's callback
     * @param int    $priority    Priority of the filter (default 10)
     *
     * @return bool Whether the function is removed.
     */
    function remove_class_filter($tag, $class_name = '', $method_name = '', $priority = 10)
    {
        global $wp_filter;
        // Check that filter actually exists first
        if (!isset($wp_filter[$tag])) {
            return false;
        }
        /**
         * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
         * a simple array, rather it is an object that implements the ArrayAccess interface.
         *
         * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
         *
         * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
         */
        $callbacks = [];
        if (is_object($wp_filter[$tag]) && isset($wp_filter[$tag]->callbacks)) {
            // Create $fob object from filter tag, to use below
            $fob       = $wp_filter[$tag];
            $callbacks = &$wp_filter[$tag]->callbacks;
        } else {
            $callbacks = &$wp_filter[$tag];
        }
        // Exit if there aren't any callbacks for specified priority
        if (!isset($callbacks[$priority]) || empty($callbacks[$priority])) {
            return false;
        }
        // Loop through each filter for the specified priority, looking for our class & method
        foreach ((array) $callbacks[$priority] as $filter_id => $filter) {
            // Filter should always be an array - array( $this, 'method' ), if not goto next
            if (!isset($filter['function']) || !is_array($filter['function'])) {
                continue;
            }
            // If first value in array is not an object, it can't be a class
            if (!is_object($filter['function'][0])) {
                continue;
            }
            // Method doesn't match the one we're looking for, goto next
            if ($filter['function'][1] !== $method_name) {
                continue;
            }
            // Method matched, now let's check the Class
            if (get_class($filter['function'][0]) === $class_name) {
                // WordPress 4.7+ use core remove_filter() since we found the class object
                if (isset($fob)) {
                    // Handles removing filter, reseting callback priority keys mid-iteration, etc.
                    $fob->remove_filter($tag, $filter['function'], $priority);
                } else {
                    // Use legacy removal process (pre 4.7)
                    unset($callbacks[$priority][$filter_id]);
                    // and if it was the only filter in that priority, unset that priority
                    if (empty($callbacks[$priority])) {
                        unset($callbacks[$priority]);
                    }
                    // and if the only filter for that tag, set the tag to an empty array
                    if (empty($callbacks)) {
                        $callbacks = array();
                    }
                    // Remove this filter from merged_filters, which specifies if filters have been sorted
                    unset($GLOBALS['merged_filters'][$tag]);
                }
                return true;
            }
        }
        return false;
    }
}
/**
 * Make sure the function does not exist before defining it
 */
if (!function_exists('remove_class_action')) {
    /**
     * Remove Class Action Without Access to Class Object
     *
     * In order to use the core WordPress remove_action() on an action added with the callback
     * to a class, you either have to have access to that class object, or it has to be a call
     * to a static method.  This method allows you to remove actions with a callback to a class
     * you don't have access to.
     *
     * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
     *
     * @param string $tag         Action to remove
     * @param string $class_name  Class name for the action's callback
     * @param string $method_name Method name for the action's callback
     * @param int    $priority    Priority of the action (default 10)
     *
     * @return bool               Whether the function is removed.
     */
    function remove_class_action($tag, $class_name = '', $method_name = '', $priority = 10)
    {
        return remove_class_filter($tag, $class_name, $method_name, $priority);
    }
}

function my_remove_class_filter($tag, $class_name = '', $method_name = '', $priority = 10)
{
    if (!remove_class_filter($tag, $class_name, $method_name, $priority) && defined('LOG_UNKNOWN_REMOVALS') && LOG_UNKNOWN_REMOVALS) {
        log_error('unknown-filter', 'removing unknown filter', compact(explode(' ', 'tag class_name method_name priority')));
    }
}

function my_remove_class_action($tag, $class_name = '', $method_name = '', $priority = 10)
{
    my_remove_class_filter($tag, $class_name, $method_name, $priority);
}

function my_dequeue_script($handle)
{
    global $wp_scripts;
    foreach ($wp_scripts->queue as $script) :
        if ($wp_scripts->registered[$script]->handle == $handle) {
            return wp_dequeue_script($handle);
        }
    endforeach;

    if (defined('LOG_UNKNOWN_REMOVALS') && LOG_UNKNOWN_REMOVALS) {
        log_error('unknown-style', 'dequeue unknown style', $handle);
    }
}

function my_dequeue_style($handle)
{
    global $wp_styles;
    foreach ($wp_styles->queue as $style) :
        if ($wp_styles->registered[$style]->handle == $handle) {
            return wp_dequeue_style($handle);
        }
    endforeach;

    if (defined('LOG_UNKNOWN_REMOVALS') && LOG_UNKNOWN_REMOVALS) {
        log_error('unknown-style', 'dequeue unknown style', $handle);
    }
}

function disable_emojis_tinymce($plugins)
{
    if (is_array($plugins)) {
        $plugins = array_diff($plugins, array('wpemoji'));
    }
    return $plugins;
}
add_action('init', 'disable_emoji_feature');

function my_deregister_styles()
{
    if (is_admin() || is_customize_preview()) {
        return;
    }

    my_dequeue_style('wp-block-library-theme');
    my_dequeue_style('wc-block-style'); // Remove WooCommerce block CSS
    my_dequeue_style('twentynineteen-print-style');
    my_dequeue_style('wp-members');
    my_dequeue_style('stripe-handler-ng-style');
}

// remove jquery migrate
add_action(
    'wp_default_scripts', function ($scripts) {
        if (is_admin() || is_customize_preview()) {
            return;
        }

        if (!empty($scripts->registered['jquery'])) {
            $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
        }
    }
);

function my_deregister_scripts()
{
    if (is_admin() || is_customize_preview()) {
        return;
    }

    // remove default jquery and add our custom-built jquery
    //wp_deregister_script('jquery');
    //wp_enqueue_script('jquery', CHILD_THEME_URI . 'assets/js/jquery.min.js', array(), false, false);
    wp_deregister_script('jquery-migrate');
    //wp_enqueue_script('astra-child', CHILD_THEME_URI . 'assets/js/index.js', array('jquery'), false, true);
    wp_deregister_script('wp-embed');
    wp_deregister_script('comment');
    my_dequeue_script('jquery-pep');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    
    // Only load stripe scripts when necessary
	if (!preg_match('/asp-products/', $_SERVER['REQUEST_URI'])) {
        my_remove_class_action('wp_enqueue_scripts', 'AcceptStripePayments', 'enqueue_frontend_scripts_styles', 10);
    }

    remove_action('embed_head', 'print_emoji_detection_script');
}

function print_styles_handler()
{
    if (is_admin() || is_customize_preview()) {
        return;
    }

    my_dequeue_style('wp-block-library');
    my_dequeue_style('wp-members');
}

function print_scripts_handler()
{

}

add_action('wp_enqueue_styles', 'my_deregister_styles', 5);
add_action('wp_enqueue_scripts', 'my_deregister_scripts', 5);
add_action('wp_print_styles', 'print_styles_handler', 1);
add_action('wp_head', 'print_scripts_handler', 5);
add_action(
    'wp_head', function () {
        //echo '<link rel="preload" href="/wp-content/themes/astra/assets/fonts/astra.woff" as="font" type="font/woff2" crossorigin="anonymous">';
    }, 1, 0
);
add_action(
    'wp_print_footer_scripts', function () {

        my_dequeue_script('wpforms-maskedinput');
        my_dequeue_script('wpforms-full');
        //wp_dequeue_style('wpforms-full');
        my_dequeue_script('wpforms');
        my_dequeue_script('wpforms-validation');
        my_dequeue_script('wpforms-text-limit');
        // Print all loaded Scripts if in debug
        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY) {
            global $wp_scripts;
            foreach ($wp_scripts->queue as $script) :
                echo '<pre>' . print_r($wp_scripts->registered[$script]->handle, true) . '.js</pre>';
            endforeach;
            //
            // Print all loaded Styles (CSS)
            global $wp_styles;
            foreach ($wp_styles->queue as $style) :
                echo '<pre>' . print_r($wp_styles->registered[$style]->handle, true) . '.css</pre>';
            endforeach;
        }
    }, 5
);

add_action('wp_print_footer_scripts', 'print_queries');
add_action('admin_print_footer_scripts', 'print_queries');

//add_filter('all', 'show_all_hooks');
/*function show_all_hooks($hook)
{
    global $wp_actions;
    if (isset($wp_actions[$hook])) {
        //log_info($hook);
    } else {
        print_r($hook);
    }
}*/
