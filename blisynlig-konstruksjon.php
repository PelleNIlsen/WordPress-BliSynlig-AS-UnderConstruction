<?php
/* 
Plugin Name: BliSynlig AS - Under konstruksjon
Plugin URI: https://www.blisynlig.no
Description: En simpel 'under konstruksjon' side for BliSynlig AS sine nettsider.
Version: 2.14.3
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
        .divider {
            width: 100%;
            height: 1px;
            background-color: black;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .gap100 {
            height: 100px;
        }
        .gap50 {
            height: 50px;
        }
        .gap20 {
            height: 20px;
        }
    </style>
    <div class="wrap">
        <img src="https://i.imgur.com/IMABxA5.png">
        <div class="divider"></div>
        <h1 style="font-weight: bold; font-size:2.5em;">BliSynlig AS - Under Konstruksjon</h1>
        <h3>For lettere HTML redigering, besøk <a href="https://www.tutorialspoint.com/online_html_editor.php" target="_blank">denne nettsiden</a> for en online editor med live oppdatering.</h3>
        <h1><a href="http://tpcg.io/FL1BJ2" target="_blank">BliSynlig AS template</a></h1>
        <?php /* echo blisynligGetIPAddress(); */ ?>
        <form action="options.php" method="post">
            <?php settings_fields('blisynlig-settings-group'); ?>
            <?php do_settings_sections('blisynlig-settings-group'); ?>
            <section>
                <h2><?php _e('Generelle instillinger'); ?></h2>
                <p><?php _e('Blokker brukere fra å se nettstedet ditt ved å aktivere siden under konstruksjon nedenfor. Påloggede brukere kan fortsatt se nettstedet. Bruk hemmelige ord og IP-whitelisting for å gi tilgang til brukere uten å logge på.', 'blisynlig'); ?></p>

                <input type="checkbox" name="blisynlig-enable" value="1"<?php checked( 1 == get_option('blisynlig-enable') ); ?> />
                <span><?php _e("Aktiver 'Under-konstruksjon'- siden", "blisynlig"); ?></span>

                <input type="checkbox" name="blisynlig-enable-homepage" value="1"<?php checked( 1 == get_option('blisynlig-enable-homepage') ); ?> />
                <span><?php _e("Gjør Wordpress sin static hjemmeside synlig: ", "blisynlig"); ?> <?php echo (get_option('page_on_front') != 0) ? sprintf( "<i><a href='%s'>%s</a></i>", get_edit_post_link( get_option('page_on_front'), 'edit'), get_the_title( get_option('page_on_front') ) ) : "Ikke satt"; ?></span>
            
                <h3><?php _e("HTML som vises på 'Under-konstruksjon'- siden", "blisynlig"); ?></h3>
                <textarea name="blisynlig-html" style="width: 100%; max-width: 100%; height: 500px;"><?php echo esc_attr( get_option('blisynlig-html') ); ?></textarea>
            </section>

            <section>
                <h2><?php _e('Hemmelig ord'); ?></h2>
                <p><?php _e("Legg til ditt hemmelige ord for å lage en lenke du kan bruke og omgå siden 'Under-konstruksjon'. En cookie lagres for å huske den nettleseren. Fjern det hemmelige ordet eller fjern merket for aktiveringstillegget for å deaktivere det hemmelige ordet under konstruksjonen. Når du endrer det hemmelige ordet, vil alle tidligere cookies være ubrukelig, og bruker må bruke det nye ordet en gang før det lagres som en cookie igjen.", 'blisynlig'); ?></p>
                <h3><?php _e("Hemmelig ord for å omgå for én nettleser", 'blisynlig'); ?></h3>
                <input type="text" name="blisynlig-secret-word" value="<?php echo esc_attr( get_option('blisynlig-secret-word') ); ?>" /><br />
                <?php if (get_option('blisynlig-secret-word') != "") { ?>
                    <p><i><?php printf(
                        __( "Bruk URL'en %s for å vise nettstedet.", 'blisynlig' ),
                        "<a href='" . get_home_url() . "?" . get_option('blisynlig-secret-word') . "'>" . get_home_url() . "?" . get_option('blisynlig-secret-word') . "</a>");
                    ?></i></p>
                <?php } ?>
                    
                <h3><?php _e('Angi antall dager siden skal huskes av nettleseren.', 'blisynlig'); ?></h3>
                <input type="number" name="blisynlig-cookie-time" min="1" max="365" value="<?php echo esc_attr( get_option('blisynlig-cookie-time') ); ?>" /><br />
                <i><?php _e('Standard er 30 dager hvis ingenting er avgitt. Kan ikke være større enn 365 dager.', 'blisynlig'); ?></i>
            </section>

            <section>
                <h2><?php _e('Whitelist Ord'); ?></h2>

                <p><?php _e("Legg til en bruker-IP til whitelisten, én IP per rad. <br />Kommenter etter IP-en for å huske hvilken bruker eller tjeneste som bruker IP-en. Vi finner den første IP-adressen ved hver nye rad."); ?></p>

                <h3><?php _e('Bruker-IP-adresser til whitelist', 'blisynlig'); ?></h3>
                <textarea name="blisynlig-ip" id="blisynlig-ip" style="width: 100%; max-width: 100%; height: 200px;"><?php echo esc_attr( get_option('blisynlig-ip') ); ?></textarea>
                <p><i>Legg til IP-adressen din på whitelisten. <span style='cursor: pointer; text-decoration: underline;' id='blisynlig-append-link' href='#'><?= blisynligGetIPAddress(); ?></span></i></p>
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
        <div class="gap100"></div>
        <h1 style="font-weight: bold; font-size:2.5em;">Standard Template Editor</h1>
        <h3>Her kan du endre logo, farger og kontakt info på BliSynlig AS Templaten. La feltene stå blankt for standard.</h3>
        <form>
            <section>
                <h3><?php _e("Sett inn link til logo (gjennomsiktig PNG for bedre resultater). Logo med dimensjoner 400x100 fungerer best.", 'blisynlig'); ?></h3>
                <input type="url" id="urlToLogo">
                <p><?php _e("Standard logo er BliSynlig AS sin SVG logo.", 'blisynlig'); ?></p>
            </section>
            <section>
                <h3><?php _e("Sett inn en custom tekst til under logoen. (Legg til <br> for å hoppe til neste linje)", 'blisynlig'); ?></h3>
                <input type="url" id="textToText">
                <p><?php _e("Standard tekst er: Dette nettsteder er for øyeblikket under bearbeidelse. /n For henvendelser, kontakt oss her:", 'blisynlig'); ?></p>
            </section>
            <section>
                <h3><?php _e("Velg bakgrunnsfarge (En lysere farge enn logo fargen fungerer bra).", 'blisynlig'); ?></h3>
                <input type="color" id="backgroundColor">
                <p><?php _e("Standard bakgrunnsfarge BliSynlig AS sin lyse grønne bakgrunnsfarge.", 'blisynlig'); ?></p>
            </section>
            <section>
                <h3><?php _e("Velg tekst farge til kontakt info (Mørkere farge som logo funker bra).", 'blisynlig'); ?></h3>
                <input type="color" id="contactInfoColor">
                <p><?php _e("Standard kontakt info farge er en mørkere grønn som passer til BliSynlig AS sin logo.", 'blisynlig'); ?></p>
            </section>
            <section>
                <h3><?php _e("Velg tekst farge til kontakt info:hover (Lik eller lysere farge som logo funker bra).", 'blisynlig'); ?></h3>
                <input type="color" id="contactInfoColorHover">
                <p><?php _e("Standard hover farge til kontakt info er lik BliSynlig AS sin grønn farge i logoen.", 'blisynlig'); ?></p>
            </section>
            <section>
                <h3><?php _e("Klikk på knappen nedenfor for å få HTML koden.", 'blisynlig'); ?></h3>
                <button class="button button-primary" id="createHTML">Få HTML</button>
                <script>
                    document.getElementById('createHTML').addEventListener('click', function (ev) {
                        ev.preventDefault();
                        let urlToLogo = "https://www.blisynlig.no/wp-content/uploads/2021/12/BliSynlig-logo-original.svg";
                        if (document.getElementById("urlToLogo").value != '') {
                            urlToLogo = document.getElementById("urlToLogo").value;
                        }
                        let textToText = "Dette nettsteder er for øyeblikket under bearbeidelse.<br>For henvendelser, kontakt oss her:";
                        if (document.getElementById("textToText").value != '') {
                            textToText = document.getElementById("textToText").value;
                        }
                        let backgroundColor = "#bfdac6";
                        if (document.getElementById("backgroundColor").value != '#000000') {
                            backgroundColor = document.getElementById("backgroundColor").value;
                        }
                        let contactInfoColor = "#2C5B32";
                        if (document.getElementById("contactInfoColor").value != '#000000') {
                            contactInfoColor = document.getElementById("contactInfoColor").value;
                        }
                        let contactInfoColorHover = "#59B463";
                        if (document.getElementById("contactInfoColorHover").value != '#000000') {
                            contactInfoColorHover = document.getElementById("contactInfoColorHover").value;
                        }
                        // console.log(urlToLogo, backgroundColor, contactInfoColor, contactInfoColorHover);
                        let html = `<style>
                                        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

                                        * {
                                            text-align: center;
                                        }

                                        .main {
                                            width: max-content;
                                            height: min-content;
                                            padding: 50px;
                                            position: absolute;
                                        }

                                        * {
                                            margin: 0;
                                            padding: 0;
                                        }

                                        body {
                                            display: flex;
                                            flex-direction: column;
                                            background-color: ${backgroundColor};
                                            justify-content: center;
                                            align-items: center;
                                            font-family: 'Poppins', sans-serif;
                                        }

                                        a {
                                            text-decoration: none;
                                            color: ${contactInfoColor};
                                            line-height: 24px;
                                            transition: .3s;
                                        }

                                        a:hover {
                                            color: ${contactInfoColorHover};
                                            transform: scale(1.2);
                                            font-size: 18px;
                                            transition: .3s;
                                        }

                                        h3 {
                                            font-size: 18.72px;
                                            line-height: 24px;
                                        }
                                    </style>

                                    <div class="main">
                                        <img src="${urlToLogo}" style="width:400px" draggable="false">
                                        <br><br>
                                        <h3>${textToText}</h3>
                                        <br>
                                        <div class="contact">
                                            <img src="https://cdn-icons-png.flaticon.com/512/561/561127.png" style="height:50px" draggable="false">
                                            <br>
                                            <a href="#">eksempelmail@blisynlig.no</a>
                                            <br><br><br>
                                            <img src="https://cdn2.iconfinder.com/data/icons/font-awesome/1792/phone-512.png" style="height:50px" draggable="false">
                                            <br>
                                            <a href="#">123 45 678</a>
                                        </div>
                                    </div>
                                    <br><br>`;

                        document.getElementById('htmlCode').value = html;
                    });            
                </script>
            </section>
            <section>
                <h3><?php _e("Kopier HTML koden og lim inn i en side eller post.", 'blisynlig'); ?></h3>
                <textarea id="htmlCode" style="width: 100%; max-width: 100%; height: 200px;"></textarea>
                <button class="button button-primary" id="copyHTML">Kopier HTML</button>
                <script>
                    document.getElementById('copyHTML').addEventListener('click', function (ev) {
                        ev.preventDefault();
                        document.getElementById('htmlCode').select();
                        document.execCommand('copy');
                    });            
                </script>
            </section>
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
