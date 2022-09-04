<?php
/*
Plugin Name:  OptiPic images optimization
Plugin URI:  https://optipic.io/en/webp/wordpress/
Description:  OptiPic.io - image optimization via smart CDN. The module automates the process of optimizing and compressing all images on the site according to the recommendations of Google PageSpeed Insights.
Version:  1.27.2
Author:  OptiPic.io
Author URI:  https://optipic.io/en/
*/

require_once 'includes/functions.php';

register_activation_hook(__FILE__,  'optipic_activate');
function optipic_activate(){
// делаем то,  что нужно
}

register_deactivation_hook(__FILE__,  'optipic_deactivate');
function optipic_deactivate(){
// делаем то,  что нужно
}

// создаем произвольное меню для плагина
add_action('admin_menu',  'optipic_create_menu');
function optipic_create_menu(){
    // создаем новое меню верхнего уровня
    add_menu_page(
        'OptiPic plugin settings page', 'OptiPic', 'manage_options',
        'optipic_main_menu', 'optipic_main_settings_page',
        plugins_url('/images/op_logo.png', __FILE__)
    );
    // вызываем функцию для регистрации настроек
    add_action('admin_init', 'optipic_register_settings');
}

function optipic_register_settings()  {
// регистрируем настройки
    register_setting(  'op-settings-group',  'optipic_options',
        'optipic_sanitize_options'  );
}

function optipic_sanitize_options($input){
    $input['cdn_autoreplace_active'] = sanitize_text_field($input['cdn_autoreplace_active']);
    $input['cdn_site_id'] = sanitize_text_field($input['cdn_site_id']);
    $input['domains'] = sanitize_textarea_field($input['domains']);
    $input['exclusions_url'] = sanitize_textarea_field($input['exclusions_url']);
    $input['whitelist_img_urls'] = sanitize_textarea_field($input['whitelist_img_urls']);
    $input['srcset_attrs'] = sanitize_textarea_field($input['srcset_attrs']);
    return $input;
}

function optipic_main_settings_page(){
    require_once 'includes/settings.php';
}

//add_filter( 'template_include', 'optipic_load_template' );
function optipic_load_template( $template ) {
    define('OPTIPIC_LOAD_TEMPLATE_PATH', $template);
    $template = dirname(__FILE__) . '/includes/template_loader.php';
    return $template;
}


add_action( 'plugins_loaded', 'optipic_init' );
function optipic_init(){
	load_plugin_textdomain( 'optipic', false, dirname( plugin_basename( __FILE__ ) ) ); 
}

function optipic_shutdown_action() {
    $final = '';

    // We'll need to get the number of ob levels we're in, so that we can iterate over each, collecting
    // that buffer's output into the final output.
    $levels = ob_get_level();

    for ($i = 0; $i < $levels; $i++) {
        $final .= ob_get_clean();
    }

    // Apply any filters to the final output
    //echo apply_filters('final_output', $final);
    echo optipic_change_content($final);
}

// https://stackoverflow.com/questions/772510/wordpress-filter-to-modify-final-html-output
if(!is_admin() && !defined('DOING_CRON')) {
    ob_start();
    add_action('shutdown', 'optipic_shutdown_action', 0);
}

function optipic_admin_print_footer_scripts_plugins() {
    if (!empty($GLOBALS['pagenow']) && $GLOBALS['pagenow']=='plugins.php') {
        $settings = optipic_get_settings();
        ?>
        <script src="https://optipic.io/api/cp/stat?domain=<?=$_SERVER["HTTP_HOST"]?>&sid<?=$settings['site_id']?>&cms=wordpress&stype=cdn&mode=dont-remove-plugin&version=<?=optipic_version()?>"></script>
        <?php
    }
}
add_action("admin_print_footer_scripts", 'optipic_admin_print_footer_scripts_plugins', 0);

// Integrate with "WP Fastest Cache" plugin
// https://ru.wordpress.org/plugins/wp-fastest-cache/
add_filter('wpfc_buffer_callback_filter', 'optipic_wpfc_buffer_callback_filter', 10, 2);
function optipic_wpfc_buffer_callback_filter($buffer, $extension) {
    if(in_array($extension, array("html", "css"))) {
        $buffer = optipic_change_content($buffer);
    }
    return $buffer;
}

/*add_filter( 'the_content', 'optipic_change_content', 999999 );*/


/*function optipic_the_content($content) {
    require_once OPTIPIC_LOAD_TEMPLATE_PATH;
}*/

//add_filter( 'woocommerce_after_single_product', 'optipic_woocommerce_after_single_product' );

/*add_filter( 'woocommerce_product_thumbnails', 'optipic_ob_change_content' );
add_filter( 'woocommerce_after_single_product_summary', 'optipic_ob_change_content' );
add_filter( 'woocommerce_after_single_product', 'optipic_ob_change_content' );
add_filter( 'theme_mod_storefront_sticky_add_to_cart', 'optipic_ob_change_content' );
add_filter( 'woocommerce_product_thumbnails', 'optipic_ob_change_content' );
add_filter( 'woocommerce_product_thumbnails', 'optipic_ob_change_content' );


function optipic_ob_change_content() {
    //die("Q");
    //ob_start();
    $content = ob_get_contents();
    ob_end_clean();
    
    //die("Q");
    //var_dump($content);
    //exit;

    //change content
    $content = optipic_change_content($content);
    //$content = str_replace('<head>', '<head test="test">', $content);

    echo $content;
}

function optipic_woocommerce_after_single_product() {
    //die("Q");
    //ob_start();
    $content = ob_get_contents();
    ob_end_clean();
    
    //die("Q");
    var_dump($content);
    exit;

    //change content
    $content = optipic_change_content($content);
    //$content = str_replace('<head>', '<head test="test">', $content);

    echo $content;
}*/
?>