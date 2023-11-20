<?php
/*
Plugin Name: Advanced Telegram Notifier
Description: Sends notifications to Telegram for different actions
Version: 1.3
Author: Anton Panov
*/

// Register admin page
yourls_add_action('plugins_loaded', 'advanced_telegram_notifier_admin_page');
function advanced_telegram_notifier_admin_page()
{
    yourls_register_plugin_page('advanced_telegram_notifier', 'Advanced Telegram Notifier', 'advanced_telegram_notifier_do_page');
}

// Display admin page
function advanced_telegram_notifier_do_page()
{
    // Check if a form was submitted
    if (isset($_POST['submit'])) {
        // Process form and save options
        yourls_verify_nonce('advanced_telegram_notifier_nonce');
        yourls_update_option('advanced_telegram_notifier_token', trim($_POST['token']));
        yourls_update_option('advanced_telegram_notifier_chat_id', trim($_POST['chat_id']));

        $options = [
            'notify_new_link' => isset($_POST['notify_new_link']) ? 'true' : 'false',
            'notify_redirect' => isset($_POST['notify_redirect']) ? 'true' : 'false'
        ];
        yourls_update_option('advanced_telegram_notifier_options', $options);
    }

    // Get current options
    $token = yourls_get_option('advanced_telegram_notifier_token');
    $chat_id = yourls_get_option('advanced_telegram_notifier_chat_id');
    $current_options = yourls_get_option('advanced_telegram_notifier_options', ['notify_new_link' => 'false', 'notify_redirect' => 'false']);

    // Create nonce
    $nonce = yourls_create_nonce('advanced_telegram_notifier_nonce');

    // Build and display the admin page form
    echo '<h2>Advanced Telegram Notifier Settings</h2>';
    echo '<form method="post">';
    echo '<input type="hidden" name="nonce" value="' . $nonce . '" />';
    echo '<p><label for="token">Telegram Bot Token: </label> <input type="text" id="token" name="token" value="' . htmlspecialchars($token) . '" /></p>';
    echo '<p><label for="chat_id">Telegram Chat ID: </label> <input type="text" id="chat_id" name="chat_id" value="' . htmlspecialchars($chat_id) . '" /></p>';
    echo '<p><input type="checkbox" id="notify_new_link" name="notify_new_link" ' . ($current_options['notify_new_link'] === 'true' ? 'checked' : '') . '> <label for="notify_new_link">Notify on new link creation</label></p>';
    echo '<p><input type="checkbox" id="notify_redirect" name="notify_redirect" ' . ($current_options['notify_redirect'] === 'true' ? 'checked' : '') . '> <label for="notify_redirect">Notify on link redirection</label></p>';
    echo '<p><input type="submit" name="submit" value="Save" /></p>';
    echo '</form>';
}

// Function to send notification
function telegram_send_notification($message)
{
    $token = yourls_get_option('advanced_telegram_notifier_token');
    $chat_id = yourls_get_option('advanced_telegram_notifier_chat_id');

    $telegram_api_url = "https://api.telegram.org/bot" . $token . "/sendMessage";
    $content = [
        'chat_id' => $chat_id,
        'text' => $message
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegram_api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($content));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!empty($response)) {
        error_log('Telegram notification failed: ' . $response);
    }
}

function get_real_user_ip()
{
    $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            return trim($ips[0]);
        }
    }
    return 'Не удалось определить IP';
}


// Function to handle new link creation notification
function advanced_telegram_notifier_on_new_link($args)
{
    $options = yourls_get_option('advanced_telegram_notifier_options');
    if ($options['notify_new_link'] === 'true') {
        $url = $args[1];
        $shorturl = $args[2];
        $message = "New link created: $url (Short URL: $shorturl)";
        telegram_send_notification($message);
    }
}

// Function to handle link redirection notification
function advanced_telegram_notifier_on_redirect($args)
{
    $options = yourls_get_option('advanced_telegram_notifier_options');
    if ($options['notify_redirect'] === 'true') {
        $shorturl = $args[0];
        $url = $args[1];
        $ip = get_real_user_ip(); // Получаем реальный IP адрес пользователя
        $message = "Short URL redirect: $shorturl to $url, IP: $ip";
        telegram_send_notification($message);
    }
}

// Add actions
yourls_add_action('insert_link', 'advanced_telegram_notifier_on_new_link');
yourls_add_action('redirect_shorturl', 'advanced_telegram_notifier_on_redirect');
