<?php
/* 
Plugin Name: BliSynlig AS - Under konstruksjon
Plugin URI: https://www.blisynlig.no
Description: En simpel 'under konstruksjon' side for BliSynlig AS sine nettsider.
Version: 2.14
Requires at least: 5.2
Requires PHP: 7.2
Author: BliSynlig AS
Author URI: https://www.blisynlig.no
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: blisynlig-konstruksjon
Domain Path: /languages
*/

/* If this file is called directly, abort. */
defined( 'ABSPATH' ) or die( '' );

/* ADD INIT ACTION */
add_action( 'plugins_loaded', 'blisynlig_init' );

/* INIT FUNCTION */
function blisynlig_init() {
    /* enable textdomain */
    load_plugin_textdomain( 'blisynlig', false, basename( dirname( __FILE__ ) ) . '/languages' );

    /* If setting isn't enabled, user is logged in, it's an API call or other settings active,
       Do not run code */
    if (1 != get_option('blisynlig-enable')) { return; } // if enabled NOT is checked
    if (is_user_logged_in()) { return; } // if logged in
    if ((strpos(wp_login_url(), $GLOBALS['pagenow']) !== false) || ($GLOBALS['PHP_SELF'] == '/wp-admin/') ) { return; } // if login page
    if (isset($_REQUEST['wc-api']) || (strpos($GLOBALS['PHP_SELF'], '/wp-json/') !== false)) { return; } // if api call

    if (1 == get_option('blisynlig-enable-homepage') && $_SERVER['REQUEST_URI'] == '/') { return; } // if enable-homepage i enabled and is_front_page

    /* Check if IP is in whitelist */
    $user_ip = blisynligGetIPAddress();
    $ip_list = get_option('blisynlig-ip');
    if (!empty($ip_list) && !empty($user_ip)) {
        /* Get whitelist array */
        $ip_array = explode("\n", $ip_list);
        $clean_ip_array = [];
        foreach ($ip_array as $ip) {
            if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $ip, $ip_match)) {
                $clean_ip_array[] = $ip_match[0];
            }
        }
        
        /* Check if user IP is in whitelist array */
        if (in_array($user_ip, $clean_ip_array)) {
            return; // if user is whitelisted
        }
    }

    /* if secret word is set, check if it's in the URL */
    $secretword = get_option('blisynlig-secret-word');
    if ($secretword != '') {
        $blisynlig_cookie_name = 'blisynlig_cookie';
        $user_url_blisynlig_secret = isset($_GET[$secretword]) ? true : false;

        /* Check cookie values */
        $trusted_user = false;
        /* blisynlig_cookie_value = get_option('blisynlig-secret-word') */
        if (isset($_COOKIE[$blisynlig_cookie_name]) && $_COOKIE[$blisynlig_cookie_name] == $secretword) {
            $trusted_user = true;
        }

        /* Check users URL argument */
        else if ($user_url_blisynlig_secret) {
            $trusted_user = true;
            /* Set cookie */
            $cookie_time = 30;
            $option_cookie_time = get_option('blisynlig-cookie-time');
            if (isset($option_cookie_time) && is_numeric($option_cookie_time) && $option_cookie_time > 0 && $option_cookie_time < 366) {
                $cookie_time = $option_cookie_time;
            }
            setcookie($blisynlig_cookie_name, $secretword, time() + (86400 * $cookie_time), "/"); /* 86400 = 1 day */
        }

        /* If wrong secret word and wrong cookie word is used, die */
        if (!$trusted_user) {
            die(get_option('blisynlig-html'));
        }
    }

    /* If no secret word is set, die */
    else {
        die(get_option('blisynlig-html'));
    }
}

/* ADD ADMIN PAGE */
add_action('admin_menu', 'blisynlig_menu');
function blisynlig_menu() {
    add_submenu_page('options-general.php',
        'BliSynlig AS - Under konstruksjon',
        'BliSynlig AS - Under konstruksjon',
        'manage_options',
        'blisynlig-submenu-page',
        'blisynlig_submenu_page_callback');
    
    /* call register settings function */
    add_action('admin_init', 'blisynlig_plugin_settings');
}

/* REGISTER SETTINGS */
function blisynlig_plugin_settings() {
    /* register our settings */
    register_setting('blisynlig-settings-group', 'blisynlig-enable');
    register_setting('blisynlig-settings-group', 'blisynlig-enable-homepage');
    register_setting('blisynlig-settings-group', 'blisynlig-secret-word');
    register_setting('blisynlig-settings-group', 'blisynlig-cookie-time');
    register_setting('blisynlig-settings-group', 'blisynlig-html');
    register_setting('blisynlig-settings-group', 'blisynlig-ip');
}

/* ADD ADMIN PAGE CONTENT */
function blisynlig_submenu_page_callback() {
?>
    <style>
        .wrap {
            margin: 20px;
        }
        form {
            max-width: 800px;
            padding: 20px;
            border-left: 1px solid black
        }
        body {
            background-color: #bfdac6;
        }
        section {
            margin-bottom: 60px;
        }
        h3 {
            margin-top: 20px;
            font-size: 18px;
        }
        input[type='text'],
        input[type='number'] {
            margin-bottom: 10px;
        }
        p, span {
            font-size: 16px;
        }
        .mute {
            font-style: italic;
            color: #666;
        }
        .button-primary {
            width: 100%;
        }
    </style>
    <div class="wrap">
        <img src="https://i.imgur.com/IMABxA5.png">
        <h1 style="font-weight: bold; font-size:2.5em;">BliSynlig AS - Under Konstruksjon</h1>
        <h3>For lettere HTML redigering, bes??k <a href="https://www.tutorialspoint.com/online_html_editor.php" target="_blank">denne nettsiden</a> for en online editor med live oppdatering.</h3>
        <h1><a href="http://tpcg.io/FL1BJ2" target="_blank">BliSynlig AS template</a></h1>

        <form action="options.php" method="post">
            <?php settings_fields('blisynlig-settings-group'); ?>
            <?php do_settings_sections('blisynlig-settings-group'); ?>
            <section>
                <h2><?php _e('Generelle instillinger'); ?></h2>
                <p><?php _e('Blokker brukere fra ?? se nettstedet ditt ved ?? aktivere siden under konstruksjon nedenfor. P??loggede brukere kan fortsatt se nettstedet. Bruk hemmelige ord og IP-whitelisting for ?? gi tilgang til brukere uten ?? logge p??.', 'blisynlig'); ?></p>

                <input type="checkbox" name="blisynlig-enable" value="1"<?php checked( 1 == get_option('blisynlig-enable') ); ?> />
                <span><?php _e("Aktiver 'Under-konstruksjon'- siden", "blisynlig"); ?></span>

                <input type="checkbox" name="blisynlig-enable-homepage" value="1"<?php checked( 1 == get_option('blisynlig-enable-homepage') ); ?> />
                <span><?php _e("Gj??r Wordpress sin static hjemmeside synlig: ", "blisynlig"); ?> <?php echo (get_option('page_on_front') != 0) ? sprintf( "<i><a href='%s'>%s</a></i>", get_edit_post_link( get_option('page_on_front'), 'edit'), get_the_title( get_option('page_on_front') ) ) : "Ikke satt"; ?></span>
            
                <h3><?php _e("HTML som vises p?? 'Under-konstruksjon'- siden", "blisynlig"); ?></h3>
                <textarea name="blisynlig-html" style="width: 100%; max-width: 100%; height: 500px;"><?php echo esc_attr( get_option('blisynlig-html') ); ?></textarea>
            </section>

            <section>
                <h2><?php _e('Hemmelig ord'); ?></h2>
                <p><?php _e("Legg til ditt hemmelige ord for ?? lage en lenke du kan bruke og omg?? siden 'Under-konstruksjon'. En cookie lagres for ?? huske den nettleseren. Fjern det hemmelige ordet eller fjern merket for aktiveringstillegget for ?? deaktivere det hemmelige ordet under konstruksjonen. N??r du endrer det hemmelige ordet, vil alle tidligere cookies v??re ubrukelig, og bruker m?? bruke det nye ordet en gang f??r det lagres som en cookie igjen.", 'blisynlig'); ?></p>
                <h3><?php _e("Hemmelig ord for ?? omg?? for ??n nettleser", 'blisynlig'); ?></h3>
                <input type="text" name="blisynlig-secret-word" value="<?php echo esc_attr( get_option('blisynlig-secret-word') ); ?>" /><br />
                <?php if (get_option('blisynlig-secret-word') != "") { ?>
                    <p><i><?php printf(
                        __( "Bruk URL'en %s for ?? vise nettstedet.", 'blisynlig' ),
                        "<a href='" . get_home_url() . "?" . get_option('blisynlig-secret-word') . "'>" . get_home_url() . "?" . get_option('blisynlig-secret-word') . "</a>");
                    ?></i></p>
                <?php } ?>
                    
                <h3><?php _e('Angi antall dager siden skal huskes av nettleseren.', 'blisynlig'); ?></h3>
                <input type="number" name="blisynlig-cookie-time" value="<?php echo esc_attr( get_option('blisynlig-cookie-time') ); ?>" /><br />
                <i><?php _e('Standard er 30 dager hvis ingenting er avgitt. Kan ikke v??re st??rre enn 365 dager.', 'blisynlig'); ?></i>
            </section>

            <section>
                <h2><?php _e('Whitelist Ord'); ?></h2>

                <p><?php _e("Legg til en bruker-IP til whitelisten, ??n IP per rad. <br />Kommenter etter IP-en for ?? huske hvilken bruker eller tjeneste som bruker IP-en. Vi finner den f??rste IP-adressen ved hver nye rad."); ?></p>

                <h3><?php _e('Bruker-IP-adresser til whitelist', 'blisynlig'); ?></h3>
                <textarea name="blisynlig-ip" id="blisynlig-ip" style="width: 100%; max-width: 100%; height: 200px;"><?php echo esc_attr( get_option('blisynlig-ip') ); ?></textarea>
                <p><i>Legg til IP-adressen din p?? whitelisten. <span style='cursor: pointer; text-decoration: underline;' id='blisynlig-append-link' href='#'><?= blisynligGetIPAddress(); ?></span></i></p>
                <?php 
                ?>
                <script>
                    document.getElementById('blisynlig-append-link').addEventListener('click', function (ev) {
                        ev.preventDefault();
                        new_line = '';
                        if (document.getElementById("blisynlig-ip").value != '') {
                            new_line = '\n';
                        }
                        document.getElementById("blisynlig-ip").value += new_line + '<?= blisynligGetIPAddress() ?> // min ip'
                    });            
                </script>
            </section>
            <?php submit_button(); ?>
            <p class="mute">Laget av BliSynlig AS</p>
        </form>
    </div>
<?php }

/* Get users IP adress */
function blisynligGetIPAddress() {
    /* Whether IP is from the share internet */
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    /* Whether IP is from the proxy */
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    /* Whether IP is from the remote address */
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/* Add settingslink in plugin-list */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'blisynlig_settings_link' );
function blisynlig_settings_link( $links ) {
    /* Build and escape the URL */
    $url = esc_url( add_query_arg(
        'page',
        'blisynlig-submenu-page',
        get_admin_url() . 'options-general.php'
    ) );
    /* Create the link */
    $settings_link = "<a href='$url'>" . __( 'Instillinger' ) . '</a>';
    /* Adds the link to the end of the array */
    array_push(
        $links,
        $settings_link
    );
    return $links;
}
