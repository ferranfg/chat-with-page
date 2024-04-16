<?php
/**
 * Plugin Name: Chat with Page
 * Plugin URI: https://f13o.com/
 * Description: By embedding a small script on your site, Chat with Page displays a chat box that lets users interact with your content, ask questions, and receive detailed information on the topics they are interested in.
 * Version: 1.0.0
 * Author: Ferran Figueredo
 * Author URI: https://ferranfigueredo.com
 * License: MIT
 * Text Domain: f13o
 * Domain Path: /languages
 */

add_action('wp_enqueue_scripts', function ()
{
    wp_enqueue_script('f13o-widget', 'https://f13o.com/api/widget.js', array(), null, true);
});

add_filter('the_content', function ($content)
{
    if ( ! is_single()) return $content;

    $locale = substr(get_locale(), 0, 2);
    $widget = '<div class="assistant-root"><assistant-widget locale="' . $locale . '"></assistant-widget><br /></div>';

    return $widget . $content;
});