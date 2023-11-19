<?php
/*
Plugin Name: BlackList User-Agent
Plugin URI: https://github.com/8Mi-Tech/yourls-ban-useragent
Description: A simple plugin to blacklist User-Agent from shortening URLs.
Version: 1.0
Author: 8Mi-Tech
Author URI: https://8mi.tech
*/
if ( !defined( 'YOURLS_ABSPATH' ) ) die();
// Hook our custom function into the 'pre_redirect' action
yourls_add_action( 'pre_redirect', 'ban_useragent' );

// Our custom function that will be triggered when the action is called

function ban_useragent() {

    // Get the user agent of the current request
    $user_agent = $_SERVER[ 'HTTP_USER_AGENT' ];

    // Get the list of banned user agents from the options page
    $banned_useragents = yourls_get_option( 'banned_useragents' );
    #if (empty($banned_useragents)) {
    #$banned_useragents = array('#WeChat#', '#QQTheme#');
    #}

if(preg_match('#QQTheme#i', $user_agent, $matches)|preg_match('#WeChat#i', $user_agent, $matches)){
	#$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
	#$ym = $http_type . $_SERVER['HTTP_HOST'];
	#$y_url = $ym.'/'.$_GET['uid'];
    include("pls-use-other-ua.php");
    die();
    }
    // Check if the user agent is in the list of banned user agents
    #if ( in_array( $user_agent, $banned_useragents ) ) {

        // Include a file with a message for banned user agents
        #include 'pls-use-other-ua.php';

        // Stop execution of the script
       #die();

    #}
}

// Register our plugin admin page
yourls_add_action( 'plugins_loaded', 'ban_useragent_add_page' );

function ban_useragent_add_page() {

    yourls_register_plugin_page( 'ban-useragent', 'BlackList User-Agent', 'ban_useragent_do_page' );

}

// Display admin page

function ban_useragent_do_page() {

    // Check if a form was submitted
    if ( isset( $_POST[ 'banned-useragents' ] ) ) {

        // Check nonce
        yourls_verify_nonce( 'ban-useragent' );

        // Process form data
        $banned = $_POST[ 'banned-useragents' ];

        // Update option in database
        yourls_update_option( 'banned_useragents', $banned );

        echo '<div class="updated"><p>User Agents successfully updated.</p></div>';

    }

    // Get value from database
    $banned = yourls_get_option( 'banned_useragents' );

    // Create nonce
    $nonce = yourls_create_nonce( 'ban-useragent' );

    ?>

    <h2>Ban UserAgent</h2>

    <form method = 'post'>

    <?php echo $nonce;
    ?>

    <p><label for = 'banned-useragents'>Banned User Agents:</label><br/>
    <textarea name = 'banned-useragents' id = 'banned-useragents' cols = '50' rows = '10'><?php echo $banned;
    ?></textarea></p>

    <p><input type = 'submit' value = 'Save' /></p>

    </form>

    <?php
}
