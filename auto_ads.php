<?php
/*
Plugin Name: Automated Ads Integrator
Plugin URI: http://www.automatedtraffic.com/auto_ads
Description: Allows customers of http://www.automatedtraffic.com/auto_ads to display ads from our site on their WordPress blogs.
Version: 1.0
Author: automatedtraffic integration
Author URI: http://www.automatedtraffic.com/auto_ads
*/

register_activation_hook(__FILE__, 'install_auto_ads');
register_deactivation_hook( __FILE__, 'remove_auto_ads' );
function install_auto_ads() {
    add_option('auto_ads_code_snippet', 'Paste your code here!', '', 'yes');
    // Should equal 'banner' or 'foursquare':
    add_option('auto_ads_mode', 'banner', '', 'yes');
    // Should equal 'yes' or 'no':
    add_option('auto_ads_display_link', 'yes', '', 'yes');
}
function remove_auto_ads() {
    delete_option('auto_ads_code_snippet');
    delete_option('auto_ads_mode');
    delete_option('auto_ads_display_link');
}

add_filter('the_content', 'add_auto_ads_footer');
function add_auto_ads_footer($content) {
    if (get_option('auto_ads_mode') != 'banner') return $content;
    usleep(300000);
    $code = get_option('auto_ads_code_snippet');
    if (get_option('auto_ads_display_link') == 'yes') $code = str_replace("?", "?displayLink=yes&", $code);
    return $content . $code . $displayLink;
}

add_filter('plugin_action_links', 'auto_ads_plugin_action_links', 10, 2);
function auto_ads_plugin_action_links($links, $file) {
    if (strstr($file, 'auto_ads')) {
        $settings_link = "<a href='options-general.php?page=auto_ads.php'>Settings</a>";
        array_unshift($links, $settings_link);
    }
    return $links;
}

if (is_admin()) {
    add_action('admin_menu', 'auto_ads_admin_menu');
    function auto_ads_admin_menu() {
        add_options_page('Automated Ads', 'Automated Ads', 8, 'auto_ads', 'auto_ads_html');
    }
}

function widget_myHelloWorld() {

    if (get_option('auto_ads_display_link') == 'yes') $displayLink = "&displayLink=yes";

    $code = get_option('auto_ads_code_snippet');
    $code = str_replace("preview.php", "preview_foursquare.php", $code);
    $code = strstr($code, 'http');
    $index = strpos($code, "'");
    $url = substr($code, 0, $index) . $displayLink;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    echo curl_exec($ch);
}

add_action("plugins_loaded", "auto_ads_init");
function auto_ads_init() {
    if (get_option('auto_ads_mode') != 'foursquare') return;
    register_sidebar_widget('Automated Ads', 'widget_myHelloWorld');
}

function auto_ads_html() {
    ?>
        <div>
            <h2>Automated Ads Options</h2>

            <form method="post" action="options.php">

                <?php wp_nonce_field('update-options');?>

                <b>Would you like to display a link with your ClickBank ID to Automated Traffic? Displaying the link will earn you additional commissions from Automated Traffic!</b>
                <table>
                    <tr>
                        <td>
                            <input type="radio" name="auto_ads_display_link" value="yes" <?php if (get_option('auto_ads_display_link') == 'yes') echo 'checked';?>/>
                        </td>
                        <td>
                            Yes
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="radio" name="auto_ads_display_link" value="no" <?php if (get_option('auto_ads_display_link') == 'no') echo 'checked';?>/>
                        </td>
                        <td>
                            No
                        </td>
                    </tr>
                </table>

                <p />

                Paste your JavaScript code snippet below:
                
                <p />

                <textarea name="auto_ads_code_snippet" id="auto_ads_code_snippet" rows="5" cols="30" onclick="document.getElementById('auto_ads_code_snippet').select();"><?php echo get_option('auto_ads_code_snippet'); ?></textarea>

                <p />

                <b>Ad mode:</b>
                <table>
                    <tr>
                        <td>
                            <input type="radio" name="auto_ads_mode" value="banner" <?php if (get_option('auto_ads_mode') == 'banner') echo 'checked';?>/>
                        </td>
                        <td>
                            Banner
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="radio" name="auto_ads_mode" value="foursquare" <?php if (get_option('auto_ads_mode') == 'foursquare') echo 'checked';?>/>
                        </td>
                        <td>
                            Foursquare
                        </td>
                    </tr>
                </table>

                <p />

                Please note that if you choose "Foursquare", you must drag the "Automated Ads" widget to your sidebar.
                Click "Dashboard", then "Widgets" to access your widgets.
  
                <p />

                Also note that if you choose "Foursquare", we recommend that you choose small banners of equal size
                (125x125, for example) on the Automated Ads website.

                <p />

                <input type="submit" value="Save Changes" />

                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="auto_ads_code_snippet,auto_ads_mode,auto_ads_display_link" />

            </form>

        </div>
    <?php
}

?>