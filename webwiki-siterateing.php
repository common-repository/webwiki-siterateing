<?php
/*
  Plugin Name: Webwiki Rating and Reviews
  Plugin URI: http://www.webwiki.de
  Description: Shows your sites Webwiki rating and offers users to give feedback on you site.
  Version: 1.0
  Author: GF Consulting
  Author URI: http://www.webwiki.de
  License: GNU LESSER GENERAL PUBLIC LICENSE (http://www.gnu.org/copyleft/lesser.html)
 */

function webwiki_siterating_sidebar_init() {
    $widgetid = 'webwiki_siterating';
    $name = 'Webwiki Rating and Reviews';

    function webwiki_siterating_getDomain() {
        $domain = get_option("webwiki_siterating_domain");
        if (empty($domain)) {
            if (isset($_SERVER['HTTP_HOST']) === TRUE) {
                $domain = $_SERVER['HTTP_HOST'];
            }
        }
        return $domain;
    }

    function webwiki_siterating_getRateingtext() {
        $text = get_option('webwiki_siterating_ratingtext');
        if (empty($text)) {
            $text = 'Rate ' . webwiki_siterating_getDomain();
        }
        return $text;
    }

    if (!function_exists('register_sidebar_widget')) {
        return;
    }

    function webwiki_siterating_controll() {
        $instances = array('de', 'com');
        $newtab = get_option('webwiki_siterating_newtab');

        if (count($_POST) > 0) {
            if (isset($_POST['webwiki_siterating_domain']) === TRUE) {
                update_option('webwiki_siterating_domain', htmlspecialchars($_POST['webwiki_siterating_domain']));
            }
            if (isset($_POST['webwiki_siterating_ratingtext']) === TRUE) {
                update_option('webwiki_siterating_ratingtext', htmlspecialchars($_POST['webwiki_siterating_ratingtext']));
            }
            if (isset($_POST['webwiki_siterating_newtab']) === TRUE) {
                $newtab = (int) $_POST['webwiki_siterating_newtab'];
                if ($newtab !== 1) {
                    $newtab = 0;
                }
                update_option('webwiki_siterating_newtab', $newtab);
            }
            if (isset($_POST['webwiki_siterating_instance']) === TRUE) {
                if (in_array($_POST['webwiki_siterating_instance'], $instances) === TRUE) {
                    update_option('webwiki_siterating_instance', $_POST['webwiki_siterating_instance']);
                }
            }
        }
        ?>
        <p>
            <label for="webwiki_siterating_ratingtext">Ratingtext: </label>
            <input type="text" id="webwiki_siterating_ratingtext" name="webwiki_siterating_ratingtext" value="<?php echo webwiki_siterating_getRateingtext(); ?>">
            <label for="webwiki_siterating_domain">Domain: </label>
            <input type="text" id="webwiki_siterating_domain" name="webwiki_siterating_domain" value="<?php echo webwiki_siterating_getDomain(); ?>">
            <br>If your domain is not availible yet, please add it at <a href="http://www.webwiki.de/info/website-eintragen.html">webwiki.de</a> or <a href="http://www.webwiki.com/info/add-website.html">webwiki.com</a>.<br>
            <label for="webwiki_siterating_instance">Choose Server: </label>
            <select name="webwiki_siterating_instance" id="webwiki_siterating_instance">
                <?php
                $choseninstance = get_option('webwiki_siterating_instance');
                foreach ($instances as $i) {
                    if ($i == $choseninstance) {
                        echo '<option selected="selected" ';
                    } else {
                        echo '<option ';
                    }
                    echo 'value="' . $i . '">webwiki.' . $i . '</option>';
                }
                ?>
            </select>
            <br>Webwiki servers host website information by language, please choose the fitting.<br>
            <input type="radio" name="webwiki_siterating_newtab" id="webwiki_siterating_newtab1" value="1" <?php
            if ($newtab == 1) {
                echo 'checked="checked"';
            }
            ?>>
            Open in New tab<br>
            <input type="radio" name="webwiki_siterating_newtab" id="webwiki_siterating_newtab0" value="0" <?php
                   if ($newtab != 1) {
                       echo 'checked="checked"';
                   }
                   ?>>
            Open in same window
        </p>
        <?php
    }

    function webwiki_siterating_sidebar() {
        $newtab = get_option('webwiki_siterating_newtab');
        $ratingtext = webwiki_siterating_getRateingtext();
        $newtabtext = '';
        if ($newtab == 1) {
            $newtabtext = ' target="_blank" ';
        }
        $format = '<div id="webwiki-siterating-widget"><a %s title="%s" href="http://www.webwiki.%s/%s">
            <img src="http://www.webwiki.%s/etc/rating/widget/%s/%s-bewertung-150.png" alt="%s">
            </a></div>';

        $domain = webwiki_siterating_getDomain();
        $dasheddomain = str_replace('.', '-', $domain);
        $choseninstance = get_option('webwiki_siterating_instance');
        echo sprintf($format, $newtabtext, $ratingtext, $choseninstance, $domain, $choseninstance, $domain, $dasheddomain, $ratingtext);
    }

    wp_register_sidebar_widget($widgetid, $name, 'webwiki_siterating_sidebar');
    wp_register_widget_control($widgetid, $name, 'webwiki_siterating_controll');
}

add_action('plugins_loaded', 'webwiki_siterating_sidebar_init');


register_uninstall_hook(__FILE__, 'webwiki_siterating_uninstall');
load_plugin_textdomain('webwiki_siterating', false, basename(dirname(__FILE__)) . '/languages');

function webwiki_siterating_uninstall() {
    delete_option('webwiki_siterating_domain');
    delete_option('webwiki_siterating_ratingtext');
    delete_option('webwiki_siterating_newtab');
    delete_option('webwiki_siterating_instance');
}