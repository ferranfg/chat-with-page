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

// Function to display the settings page
function f13o_settings_page()
{
    ?>
    <div class="wrap">
        <h2>✨ <b>Chat with Page</b>. AI Post Assistant.</h2>
        <hr />
        <form method="post" action="options.php">
            <?php
            settings_fields('f13o_options_group');
            do_settings_sections('f13o');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function f13o_section_text()
{
    echo '<p>The widget accepts the following options. All of them are optional, but it\'s recommended to fill them to provide a better user experience.<br />To get more details about what you can do, visit <a href="https://f13o.com/docs" target="_blank">our documentation</a> page.</p>';
}

// Dynamic settings field creation based on an array
function f13o_create_settings_field($args)
{
    $field_id = $args['id'];
    $field_value = f13o_options_value($field_id);

    echo "<input id='f13o_{$field_id}' name='f13o_options[$field_id]' size='40' type='text' value='$field_value' />";
}

function f13o_options_value($id)
{
    $options = get_option('f13o_options');

    return isset($options[$id]) ? $options[$id] : '';
}

function f13o_options_validate($input)
{
    $validated = array();

    foreach ($input as $key => $value)
    {
        $validated[$key] = sanitize_text_field($value);
    }

    return $validated;
}

// Hooks
add_action('wp_enqueue_scripts', function ()
{
    wp_enqueue_script('f13o-widget', 'https://f13o.com/api/widget.js', array(), null, true);
});

add_filter('the_content', function ($content)
{
    if ( ! is_single()) return $content;

    $widget = '<div class="assistant-root">
        <assistant-widget
            banner="' . f13o_options_value('banner') . '"
            trigger="' . f13o_options_value('trigger') . '"
            locale="' . substr(get_locale(), 0, 2) . '"
            webhook="' . f13o_options_value('webhook') . '">
        </assistant-widget>
        <br />
    </div>';

    return $widget . $content;
});

add_action('admin_menu', function ()
{
    add_menu_page('Chat with Page', 'Chat with Page', 'manage_options', 'f13o', 'f13o_settings_page', 'dashicons-format-chat');
});

add_action('admin_init', function ()
{
    register_setting('f13o_options_group', 'f13o_options', 'f13o_options_validate');
    add_settings_section('f13o_main', 'Configuration Options', 'f13o_section_text', 'f13o');

    // Array of fields
    $fields = [
        ['id' => 'api_key', 'title' => 'API Key'],
        ['id' => 'banner', 'title' => 'Banner'],
        ['id' => 'trigger', 'title' => 'Trigger'],
        ['id' => 'webhook', 'title' => 'Webhook URL'],
    ];

    foreach ($fields as $field)
    {
        add_settings_field("f13o_{$field['id']}", $field['title'], 'f13o_create_settings_field', 'f13o', 'f13o_main', ['id' => $field['id']]);
    }
});