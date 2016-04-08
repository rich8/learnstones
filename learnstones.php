<?php
/*
Plugin Name: Learnstones
Plugin URI: http://learnstones.com/
Description: Includes pdf print out (tcpdf included as plugin).
Author: Richard Drake and Raymond Francis 
Version: 0.9
*/

//http://localhost/wordpress/?ls_lesson=whatstrto

require_once( ABSPATH . 'wp-admin/includes/template.php');
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
require_once('simple_html_dom.php');
require_once('http_build_url.php');
require_once('google-api-php-client-master/src/Google/Client.php');
require_once( ABSPATH . 'wp-admin/includes/plugin.php');
define('TCPDF_PLUGIN', "tcpdf/tcpdf.php");
if(is_plugin_active(TCPDF_PLUGIN))
{
    require_once('include/lspdf.php');
}
class Learnstones_Plugin
{

	const LS_DB_VERSION = "0.71";

	const LS_STYLES = "ls_menu0 ls_menu1 ls_menu2 ls_menu3 ls_menu4";

	const LS_COOKIE = "lscookie";

    const LS_TOK_INPUT = "%input%";

	const LS_SCRIPT = "ls_script";
	const LS_SCRIPT_HIGHLIGHT = "ls_highlight";
	const LS_SCRIPT_HEAD = "ls_head";
	const LS_SCRIPT_FOOT = "ls_foot";

	const LS_STYLE = "ls_style";
	const LS_STYLE_EDIT = "ls_style_edit";
	const LS_STYLE_BESPOKE = "ls_style_bespoke";
	const LS_STYLE_ALL = "ls_style_all";
    const LS_STYLE_HIGHLIGHT = "ls_style_highlight";

	const LS_TYPE_LESSON = "ls_lesson";
	const LS_TYPE_COURSE = "ls_course";
	const LS_TYPE_CLASS = "ls_class";

    // Options page stuff
	const LS_OPT_SESSION_DURATION = "duration";
	const LS_OPT_SESSION_PURGE = "purge";
  	const LS_OPT_SLIDE = "slide";
  	const LS_OPT_CLASSID_SIZE = "clsidsize";
  	const LS_OPT_CLASSID_CHARS = "clsidchars";
  	const LS_OPT_INPUT_DISP = "inputdisp";
  	const LS_OPT_GOOGLE_ENABLE = "genable";
  	const LS_OPT_GOOGLE_CLIENT_ID = "gclient";
  	const LS_OPT_GOOGLE_CLIENT_SECRET = "gsecret";
  	const LS_OPT_GOOGLE_KEY = "gkey";
    const LS_OPT_DOMAINS_ENABLE = "domainsenable";
    const LS_OPT_DOMAINS = "domains";
    const LS_OPT_MESSAGE = "message";
    const LS_OPT_LIGHTS = "lights";
    const LS_OPT_PDF_PLUGIN = "pdfplugin";
    const LS_OPT_PDF_LIGHTS = "pdflights";

    // Website options
	const LS_OPT_DB_VERSION = "ls_db_version";
  	const LS_OPT_SEARCH = "ls_search";
  	const LS_OPT_CLASS = "ls_class";
  	const LS_OPT_LOGIN_WARN = "ls_loginw";
  	const LS_OPT_LOGIN_WARNU = "ls_loginwu";
    const LS_OPT_LOGIN_WARN_DEFAULT = 1800; //30 * 60;
  	const LS_OPT_INPUT_DISP_SEL = "ls_inputdispsel";

	const LS_SLIDESHOW_FOLDER = "slideshows";

	const LS_TBL_SESSION = "ls_session";
	const LS_TBL_RESPONSES = "ls_responses";
	const LS_TBL_LEARNSTONES = "ls_learnstones";
	const LS_TBL_COURSES = "ls_courses";
	const LS_TBL_CLASSES = "ls_classes";
	const LS_TBL_INPUTS = "ls_inputs";

    const LS_TBL_SESSION_SFX = "_uv";
    const LS_MAX_TITLE_SIZE = 128;

    const LS_SPLITTER = "<hr />";

    const LS_NONCE = "ls_submission_nonce";

    const LS_CUSTOM_STATUS = "_ls_status";

    const LS_FLD_CONFIRMED = "ls_confirmed";
    const LS_FLD_CLASS = "ls_classid";
    const LS_FLD_CLASS_NO = "ls_classno";
    const LS_FLD_CLASS_REGEN = 'ls_clsregen';
    const LS_FLD_LEARNSTONE = 'ls_ls';
    const LS_FLD_UVNAME = 'ls_name';
    const LS_FLD_DASH = "ls_db";
    const LS_FLD_PDF = "ls_pdf";
    const LS_FLD_SEARCH_TYPE = "ls_search_in";
    const LS_FLD_TITLE = "ls_title";
    const LS_FLD_POST_ID = "post_id";
    const LS_FLD_LIMIT = "ls_limit";
    const LS_FLD_CLEAR = "ls_clear";
    const LS_FLD_NEW_CLASS_NAME = "ls_newclass";
    const LS_FLD_GOOGLE_LI = "ls_glogin";
    const LS_FLD_STONE = "ls_stone";
    const LS_FLD_LOGGEDOUT = "ls_loggedout";
    const LS_FLD_VIEW_CLASS = "ls_cview";
    const LS_FLD_CHANGE_CLASS = "ls_cchange";
    const LS_FLD_LOGOUT = "ls_logout";

    const LS_VAL_PAGE_SIZE = 10;

    const LS_WID_CLASS = "ls_wid_class";

    const LS_TAX_SUBJECT = "subject";

    const LS_STATUS_OK = 0;
    const LS_STATUS_NO_TITLE = 1;
    const LS_STATUS_DUPLICATE = 2;
    const LS_STATUS_RESOLVE = 3;
    const LS_STATUS_TITLE_TOO_LONG = 4;
    const LS_STATUS_BAD_RESOLVE = 5;
    const LS_STATUS_INVALID_SC = 6;

    const LS_MODE_LESSON_VIEW = 0;
    const LS_MODE_LESSON_EDIT = 1;
    const LS_MODE_COURSE_EDIT = 2;
    const LS_MODE_DASHBOARD_VIEW = 3;
    const LS_MODE_CLASS_EDIT = 4;
    const LS_MODE_CLASS_VIEW = 5;
    const LS_MODE_SETTINGS = 6;
    const LS_MODE_LOGIN = 7;
    const LS_MODE_DEFAULT = 8;

    const LS_SC_LIGHTS = 'lights';
    const LS_SC_SYS_LIGHTS = '_lights';
    const LS_SC_SYS_DISPLAY_OPTS = '_display';
    const LS_SC_CLASSID = 'classid';
    const LS_SC_INPUT = 'input';

    const LS_CLS_CHARS = "ABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
    const LS_CLS_CODE_SIZE = 6;

    const LS_PDF_LIGHTS_DEFAULT = '<p class="lights">Lights image and text</p>';

    const LS_NAME_UNVERIFIED = "(Unverified)";

    const LS_MSG_LOGIN = "<p>Welcome to Learnstones.  <b>Do not</b> enter your username or password here, if you are logging in with Learnstones validated Google account.  Otherwise use your Learnstones log in details and click the alternative log in button.</p>";

    const LS_SVC_WORDPRESS = 0;
    const LS_SVC_GOOGLE = 1;

    const LS_OPT_MAIN = 'ls_options';
    const LS_OPT_GOOGLE = 'ls_options_google';
    const LS_OPT_PDF = 'ls_options_pdf';
    private $LS_OPT_SECTIONS = array(self::LS_OPT_MAIN=>'Main Settings', self::LS_OPT_GOOGLE=>'Google Settings',self::LS_OPT_PDF=>'PDF Settings');

    private $LS_LIGHTS_DEFAULT = array(
                            "l1" => "I don't understand",
                            "l2" => "I'm not sure",
                            "l3" => "I get it"
                        );

    private $LS_LIGHTS_DEFAULT_AUTOMARK = array(
                            "l4" => "Ready to move on"
                        );


	private $session_id = -1;
    private $session_class;
    private $session_name = "";
    private $session_uvinput = 0;
    private $session_state = "";
    private $session_valid = TRUE;
    private $session_service = self::LS_SVC_WORDPRESS;
    private $session_stone = 0;
    private $lesson_data = 0;
    private $lights;
    private $shortcode_error;
    private $shortcode_validation;
    private $shortcode_fields = array();
    private $shortcode_ls = 0;
    private $valid_class = TRUE;
    private $select_learnstone = 0;
    private $dashboard = FALSE;
    private $pdf = FALSE;
    private $max_width = 640;
    private $settings_page;
    private $login_redirect;
    private $auto_answers = array();

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_logout', array( $this, 'wp_logout' ) );
		add_action( 'wp_login', array($this, 'wp_login'), 10, 2 );
		add_action( 'wp_ajax_ls_submission', array($this, 'wp_ajax_ls_submission'));
		add_action( 'wp_ajax_nopriv_ls_submission', array($this, 'wp_ajax_nopriv_ls_submission'));
		add_action( 'plugins_loaded', array($this, 'plugins_loaded') );
		add_action( 'admin_menu', array($this, 'admin_menu') );
		add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'), 99);
        add_action( 'login_enqueue_scripts', array($this,'login_enqueue_scripts'));
        add_action( 'admin_enqueue_scripts', array($this,'admin_enqueue_scripts'),10,1);
        add_action( 'save_post', array( $this, 'save_post' ), 20, 2 );
        add_action( 'admin_notices', array( $this, 'admin_notices'));
        add_action( 'add_meta_boxes_' . self::LS_TYPE_LESSON, array($this, 'add_meta_boxes_ls_lesson'));
        add_action( 'add_meta_boxes_' . self::LS_TYPE_COURSE, array($this, 'add_meta_boxes_ls_course'));
        add_action( 'add_meta_boxes_' . self::LS_TYPE_CLASS, array($this, 'add_meta_boxes_ls_class'));
        add_action( 'edit_form_after_title', array($this, 'edit_form_after_title'));
        add_action( 'wp_dashboard_setup', array($this, 'wp_dashboard_setup'));
        add_action( 'show_user_profile', array($this, 'show_user_profile'));
        add_action( 'edit_user_profile', array($this, 'edit_user_profile'));
        add_action( 'personal_options_update', array($this, 'personal_options_update'));
        add_action( 'edit_user_profile_update', array($this, 'edit_user_profile_update'));
        add_action( 'set_logged_in_cookie', array($this, 'set_logged_in_cookie'));
        add_action( 'wp', array( $this, 'wp' ) );
        add_action( 'manage_' . self::LS_TYPE_CLASS . '_posts_custom_column', array($this, 'manage_ls_class_posts_custom_column'), 10, 2);
        add_action( 'wp_authenticate', array($this, 'wp_authenticate'), 10, 2);
        add_action( 'login_form', array($this, 'login_form'));
        add_action( 'login_message', array($this, 'login_message'));
        add_action( 'wp_footer', array($this, 'wp_footer'));

        //add_action( 'pre_get_posts', array($this, 'goto_learnstone') );

		//add_filter( 'map_meta_cap', array( $this, 'map_meta_cap'), 10, 4 );
		add_filter( 'login_redirect', array($this, 'login_redirect'), 10, 3 );
		add_filter( 'post_row_actions', array($this, 'post_row_actions'), 10, 2 );
		add_filter( 'the_content', array($this, 'the_content' ), 10 );
		add_filter( 'show_admin_bar', array($this, 'show_admin_bar') );
		add_filter( 'request', array($this, 'request') );
		add_filter( 'query_vars', array($this, 'query_vars') );
        add_filter( 'style_loader_tag', array($this, 'style_loader_tag'), 10, 2);
        add_filter( 'posts_where', array($this, 'posts_where'), 10, 2);
        add_filter( 'manage_' . self::LS_TYPE_CLASS . '_posts_columns', array($this, 'manage_ls_class_posts_columns'));
        add_filter( 'authenticate', array($this, 'authenticate'), 10, 3);
        add_filter( 'next_post_rel_link', array($this, 'next_post_rel_link'));

        add_shortcode( self::LS_SC_LIGHTS, array($this, 'shortcode_lights') );
        add_shortcode( self::LS_SC_SYS_LIGHTS, array($this, 'shortcode_system_lights') );
        add_shortcode( self::LS_SC_CLASSID, array($this, 'shortcode_classid') );
        add_shortcode( self::LS_SC_INPUT, array($this, 'shortcode_input') );
        
        register_activation_hook( __FILE__, array($this, 'activation'), 10, 3 );
	}

    // This prevents firefox prefetching
    function next_post_rel_link($link)
    {
        return FALSE;
    }

    function wp( )
    {
        if(isset($_POST['post_id']))
        {
            $post_id = intval($_POST['post_id']);
            $this->get_session();
            $userOrSession = $this->session_id;
            if(is_user_logged_in())
            {
                $userOrSession = get_current_user_id();
            }
            $time = current_time("mysql");

            foreach($_POST as $key => $input)
            {
                if(strpos($key, 'lsi_') === 0)
                {
                    $this->set_lesson_data($post_id, $key, array($time => stripslashes($input)), $userOrSession, is_user_logged_in());
                }
            }
            if(isset($_POST[self::LS_FLD_VIEW_CLASS]))
            {
                wp_redirect(get_permalink($this->session_class));
                exit();
            }
            else if(isset($_POST[self::LS_FLD_GOOGLE_LI]))
            {
                wp_signon('','');
            }
            else if(isset($_POST[self::LS_FLD_CHANGE_CLASS]))
            {
                if(is_user_logged_in())
                {
                    wp_redirect(get_dashboard_url(get_current_user_id()));                          
                }
                else
                {
                    wp_redirect(home_url());
                }
                exit();
            }
            else if(isset($_POST[self::LS_FLD_LOGOUT]))
            {
                wp_logout();
            }
        }

        if(is_singular(self::LS_TYPE_LESSON))
        {
            if(isset($_GET[self::LS_FLD_PDF]))
            {   
                $this->pdf = TRUE;
                ob_start();
            }
            else
            {
                // This fis a copy nocache_headers, with no-store added, awaiting wp fix

	            $headers = wp_get_nocache_headers();

                $headers['Cache-Control'] = 'no-cache, no-store, must-revalidate, max-age=0';

	            unset( $headers['Last-Modified'] );

	            // In PHP 5.3+, make sure we are not sending a Last-Modified header.
	            if ( function_exists( 'header_remove' ) ) {
		            @header_remove( 'Last-Modified' );
	            } else {
		            // In PHP 5.2, send an empty Last-Modified header, but only as a
		            // last resort to override a header already sent. #WP23021
		            foreach ( headers_list() as $header ) {
			            if ( 0 === stripos( $header, 'Last-Modified' ) ) {
				            $headers['Last-Modified'] = '';
				            break;
			            }
		            }
	            }

	            foreach( $headers as $name => $field_value )
		            @header("{$name}: {$field_value}");
            }
        }
    }

    function wp_authenticate(&$username, &$password)
    {
        //Google login if state set
        global $wpdb;
        $this->get_session();
        unset($this->session_user);
        $service = $this->session_service;
        $name = $this->session_name;
        if(isset($_POST[self::LS_FLD_GOOGLE_LI]))
        {
            $options = get_option(self::LS_OPT_MAIN);

            $state = md5(rand());

            $stone = 0;
            if(isset($_POST[self::LS_FLD_STONE]))
            {
                $stone = intval($_POST[self::LS_FLD_STONE]);
            }

            $class = 0;
            if (!isset($_POST[self::LS_FLD_CLEAR]))
            {
                $class = $this->session_class;                        
            }
            if(isset($_POST[self::LS_FLD_CLASS]) && !empty($_POST[self::LS_FLD_CLASS]))
            {
                $class = $this->get_class_id($_POST[self::LS_FLD_CLASS]);
            }

            if (isset($_POST[self::LS_FLD_CLEAR]))
            {
                $this->clear_session(0, FALSE, $this->session_id, TRUE, TRUE, $state, $class, $stone);    
            }
            else
            {
                $table_name = $wpdb->prefix . self::LS_TBL_SESSION;
                $wpdb->update(
			        $table_name,
			        array(
                        'classid' => $class, //glogin
                        'state' => $state,
                        'stone' => $stone
			        ),
			        array(
				        'id' => $this->session_id
			        ),
			        array(
                        "%d",
                        "%s",
                        "%d"
			        ),
			        array(
				        "%d"
			        )
                );
            }
            $wp = wp_login_url();
            $client_id =  $options[self::LS_OPT_GOOGLE_CLIENT_ID];
            $url = "https://accounts.google.com/o/oauth2/auth?scope=https://www.googleapis.com/auth/plus.me%20https://www.googleapis.com/auth/plus.login%20email&state=$state&redirect_uri=$wp&response_type=code&client_id=$client_id&access_type=offline";
            wp_redirect($url);
            exit();
        }
        elseif (isset($_GET['state']))
        {
            $this->get_session();


            if (isset   ($this->sessionstate) && $this->sessionstate != $_GET['state']) {
        		$this->session_user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
            }
            else
            {
                $client = new Google_Client();
                $client->setApplicationName('learnstones');

                $options = get_option(self::LS_OPT_GOOGLE);
                $gclientId = "";
                if(isset($options[self::LS_OPT_GOOGLE_CLIENT_ID]))
                {
                    $gclientId = $options[self::LS_OPT_GOOGLE_CLIENT_ID];
                }
                $gclientSec = "";
                if(isset($options[self::LS_OPT_GOOGLE_CLIENT_SECRET]))
                {
                    $gclientSec = $options[self::LS_OPT_GOOGLE_CLIENT_SECRET];
                }
                $gkey = "";
                if(isset($options[self::LS_OPT_GOOGLE_KEY]))
                {
                    $gkey = $options[self::LS_OPT_GOOGLE_KEY];
                }

                $client->setClientId($gclientId);
                $client->setClientSecret($gclientSec);
                $client->setDeveloperKey($gkey);
                $client->setRedirectUri(wp_login_url());

                if(!empty($gclientId) && !empty($gclientSec) && !empty($gkey))
                {
                    if (isset($_GET['code'])) {
                        $client->authenticate($_GET['code']);
                        // Get your access and refresh tokens, which are both contained in the
                        // following response, which is in a JSON structure:
                        $jsonTokens = $client->getAccessToken();
                        $toks = json_decode($jsonTokens, TRUE);
                        $tok = $toks['access_token'];                  

                        $csession = curl_init();
                        $url = "https://www.googleapis.com/plus/v1/people/me?access_token={$tok}&fields=displayName%2Cemails&key=$gkey";
                        curl_setopt($csession, CURLOPT_URL, $url);
                        curl_setopt($csession, CURLOPT_SSL_VERIFYPEER, FALSE); 
                        // 1 is CURL_SSLVERSION_TLSv1, which is not always defined in PHP. 
                        //curl_setopt($csession, CURLOPT_SSLVERSION, 1); 

                        curl_setopt($csession, CURLOPT_RETURNTRANSFER, 1);

                        $output = curl_exec($csession);

                        if($output === FALSE)
                        {
                            $this->session_user = new WP_Error('authentication_failed', curl_error($csession));
                        }
                        else
                        {
                            $ret = json_decode($output, TRUE);
                            if(isset($ret['error']))
                            {
                                $this->session_user = new WP_Error('authentication_failed', $ret['error']['message']);                             
                            }
                            else
                            {
                                $domains = array();
                                if(isset($options[self::LS_OPT_DOMAINS_ENABLE]) && !empty($options[self::LS_OPT_DOMAINS_ENABLE]) && isset($options[self::LS_OPT_DOMAINS]))
                                {
                                    $domains = explode(',', $options[self::LS_OPT_DOMAINS]);
                                    if(empty($domains[0]) && count($domains) == 1)
                                    {
                                        $domains = array();
                                    }
                                }
                                $userEmail = "";
                                foreach($ret['emails'] as $email)
                                {
                                    $e = strtolower($email['value']);
                                    $check = TRUE;
                                    if(count($domains) > 0)
                                    {
                                        $check = FALSE;
                                        foreach($domains as $domain)
                                        {
                                            $d = strtolower("@" . trim($domain));
                                            if(strrpos($e, $d, -strlen($d)) !== FALSE)
                                            {
                                                $check = TRUE;
                                            }
                                        }
                                    }
                                    if($check)
                                    {
                                        if(empty($userEmail)){
                                            $userEmail = $e;
                                        }
                                        else if($email['type'] == 'account') {
                                            $userEmail = $e;
                                        }
                                    }
                                }

                                if(empty($userEmail))
                                {
                                    $this->session_user = new WP_Error('authentication_failed', 'Invalid Google account.  You will need to log out of Google and log in with a valid account');                             
                                }
                                else
                                {
                                    $user= get_user_by('email', $userEmail);
                                    if($user === FALSE)
                                    {
                                        $randomPw = wp_generate_password(20, FALSE);
                                        $uid = wp_create_user($userEmail, $randomPw, $userEmail);
                                        wp_update_user(array('ID' => $uid, 'user_nicename' => sanitize_title($ret['displayName']),'display_name' => $ret['displayName']));
                                        $this->session_user = new WP_User($uid);
                                        $service = self::LS_SVC_GOOGLE;
                                        $name = $ret['displayName'];
                                    }
                                    else
                                    {
                                        $service = self::LS_SVC_GOOGLE;
                                        $name = $ret['displayName'];
                                        $this->session_user = $user;                                    
                                    }
                                }
                            }
                        }
                        curl_close($csession);
                    }
                }
            }
        }

        if($service != $this->session_service || $name != $this->session_name)
        {
            $this->session_service = $service;
            $this->session_name = $name;
            $table_name = $wpdb->prefix . self::LS_TBL_SESSION;
            $wpdb->update($table_name, array('uvname' => $this->session_name, 'service' => $this->session_service, 'time'=>get_current_time('mysql')), array('id' => $this->session_id), array("%s", "%d", "%s"), array("%d"));
        }
    }

    function authenticate($user, $username, $password)
    {
        $this->get_session();

        if(isset($this->session_user))
        {
            if(!is_wp_error($this->session_user))
            {
                if($this->session_stone != 0)
                {
                    $this->login_redirect =  home_url("/stone/" . $this->session_stone);                    
                }
                elseif ($this->session_class != 0)
                {
                    $this->login_redirect = get_permalink($this->session_class);
                }
            }
            return $this->session_user;
        }
        else {
            return $user;
        }
    }

    function query_vars( $qvars )
    {
        $qvars[] = self::LS_FLD_LEARNSTONE;
        $qvars[] = self::LS_FLD_CLASS;
        $qvars[] = self::LS_FLD_DASH;
        $qvars[] = self::LS_FLD_TITLE;
        $qvars[] = self::LS_FLD_UVNAME;
        $qvars[] = self::LS_FLD_CLEAR;
        return $qvars;
    }

	function request( $qvars ) {
        global $wpdb;
        global $pagenow;

        $update_session = FALSE;
        $update_session_time = FALSE;
        $this->get_session();

        if (isset($qvars[self::LS_FLD_CLEAR]))
        {
            if(is_user_logged_in())
            {
                $this->clear_session(get_current_user_id(), FALSE, $this->session_id, TRUE, TRUE);                
            }
            else
            {
                $this->clear_session(0, FALSE, $this->session_id, TRUE, TRUE);    
            }
        }
        elseif (!$this->session_valid)
        {
            $qvars = array('p' => get_option('page_on_front'), 'post_type' => 'page ');            
        }


        if(isset($qvars[self::LS_FLD_CLASS]))
        {
            $classname = $qvars[self::LS_FLD_CLASS];
        } elseif(isset($qvars[self::LS_TYPE_CLASS]) && $pagenow=='index.php')
        {       
            $classname = $qvars[self::LS_TYPE_CLASS];
        }

        if(isset($qvars[self::LS_FLD_UVNAME]))
        {
            $n = stripslashes($qvars[self::LS_FLD_UVNAME]);
            if($this->session_name != $n)
            {
                $this->session_name = $n;
                $update_session = TRUE;
                $update_session_time = TRUE;
            }
        }

        if(isset($classname))
        {
            $class = $this->get_class_id($classname);
            if($class)
            {
                $qvars = array('name' => $classname);
                $qvars['post_type'] = self::LS_TYPE_CLASS;
		        if($this->session_id > 0) {
                    $this->session_class = $class;
                    $update_session = TRUE;
                }

                $suffix = "";
                if(is_user_logged_in())
                {
                    $current_user = wp_get_current_user();
                    $user = $current_user->ID;
                    update_user_option($user, self::LS_OPT_CLASS, $class);
                }
                else
                {
                    $user = $this->session_id;
                    $suffix = self::LS_TBL_SESSION_SFX;
                }

                $table_name = $wpdb->prefix . self::LS_TBL_CLASSES . $suffix;
                $sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE user=%d AND classid=%d", array($user, $class) );
                $row = $wpdb->get_row( $sql );
                if($row == NULL || $row->member == 0)
                {
                    $this->session_class = $class;
                    if($row == NULL)
                    {
			            $wpdb->insert( $table_name,
					            array(
                                    'user' => $user,
						            'classid' => $class,
                                    'status' => 0,
                                    'member' =>1,
                                    'dbupdate' => current_time('mysql')
					            ),
					            array(
						                "%d",
						                "%d",
						                "%d",
						                "%d",
                                        "%s"
					            )
			            );
                    }
                    else
                    {
                        $wpdb->update($table_name, array('member' => 1, 'dbupdate' => current_time('mysql')),
                                       array('user' => $user, 'classid' => $class),
                                       array("%d", "%s"),
                                       array("%d", "%d"));
                    }

                    //Get all lessons from class
                    
                    $list = array();
                    $this->get_lessons( $class, 0, $list, 10000 );
                    $inData = "";
                    $conj = "";
                    foreach($list as $lesson)
                    {
                        if($lesson['type'] == self::LS_TYPE_LESSON)
                        {
                            $inData .= $conj . $lesson['id'];
                            $conj = ",";
                        }
                    }

                    if(!empty($inData))
                    {
                        $time = current_time('mysql');

                        $table_name = $wpdb->prefix . self::LS_TBL_RESPONSES . $suffix;
                        $wpdb->query("UPDATE $table_name SET dbupdate='$time' WHERE user=$user AND post IN ($inData)");

                        $table_name = $wpdb->prefix . self::LS_TBL_INPUTS . $suffix;
                        $wpdb->query("UPDATE $table_name SET dbupdate='$time' WHERE user=$user AND post IN ($inData)");
                    }

                    //Class change???
                    if(!is_user_logged_in())
                    {
                        $this->mark_uv_input($this->session_id);
                    }
                }       
                else
                {
                    // Check if not member and then...
                    // Should check status to see if user is blocked etc
                    // Check that class is available to unverified users
                }         
            }   
            else if(empty($classname) && is_user_logged_in() && get_user_option(self::LS_OPT_CLASS) !== FALSE)
            {
                $qvars = array('p' => get_user_option(self::LS_OPT_CLASS), 'post_type' => self::LS_TYP_CLASS);
            }
            else
            {
                $this->valid_class = FALSE;
                $qvars = array('p' => get_option('page_on_front'), 'post_type' => 'page');
            }
        }
        elseif(isset($qvars[self::LS_FLD_LEARNSTONE]))
        {
            $table_name = $wpdb->prefix . self::LS_TBL_LEARNSTONES;
            $sql = $wpdb->prepare("SELECT id,post,lsorder FROM $table_name WHERE id=%d", $qvars[self::LS_FLD_LEARNSTONE]);
            $row = $wpdb->get_row($sql, OBJECT);
            if($row != NULL)
            {
                $qvars = array('p' => $row->post);
                $qvars['post_type'] = self::LS_TYPE_LESSON;
                $this->select_learnstone = $row->lsorder;   
            }
        }

        if($update_session)
        {        
            $table_name = $wpdb->prefix . self::LS_TBL_SESSION;
            $time = current_time('mysql');
            $update_fields = array(
					'classid' => $this->session_class,
                    'uvname' => $this->session_name,
                    'dbupdate' => $time,
                    'valid' => 1
				);
            $update_format = array(
                        "%d",
						"%s",
                        "%s",
                        "%s"
				);
            if($update_session_time)
            {
                $update_fields['time'] = $time;
                $update_format[] = "%s";
            }
            
        	$rows = $wpdb->update( $table_name,
				$update_fields,
				array(
					'id' => $this->session_id
				),
				$update_format,
				array(
					"%d"
				)
			);
        }
        
        if(isset($qvars[self::LS_FLD_DASH]))
        {
            if(is_user_logged_in())
            {
                $this->dashboard = current_user_can('edit_posts');
            }
            else
            {
                $qvars = array('p' => get_option('page_on_front'), 'post_type' => 'page');                
            }
        }
		return $qvars;
	}


	function init() {
                                       
		global $wpdb;

		$caps = 	array(
					'publish_posts' => 'publish_lessons',
					'edit_posts' => 'edit_lessons',
					'edit_others_posts' => 'edit_others_lessons',
					'delete_posts' => 'delete_lessons',
					'delete_others_posts' => 'delete_others_lessons',
					'read_private_posts' => 'read_private_lessons',
					'edit_post' => 'edit_lesson',
					'delete_post' => 'delete_lesson',
					'read_post' => 'read_lesson',
				);


        $labels =	array(
			'name' => 'Classes',
			'singular_name' => 'Class',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Class',
			'edit_item' => 'Edit Class',
			'new_item' => 'New Class',
			'all_items' => 'All Classes',
			'view_item' => 'View Class',
			'search_items' => 'Search Classes',
			'not_found' =>  'No classes found',
			'not_found_in_trash' => 'No classes found in Trash', 
			'parent_item_colon' => '',
			'menu_name' => 'Classes' ,
		);

		register_post_type( 	self::LS_TYPE_CLASS,
					array(
						'labels' => $labels,
						'public' => true,
						'has_archive' => true,
						'show_in_menu' => true,
                        'menu_position' => 5,
                        'rewrite' => array('slug' => 'classes'),
                        'menu_icon' => 'dashicons-groups'
					)

		);

		$labels =	array(
					'name' => 'Courses',
					'singular_name' => 'Course',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New Course',
					'edit_item' => 'Edit Course',
					'new_item' => 'New Course',
					'all_items' => 'All Courses',
					'view_item' => 'View Course',
					'search_items' => 'Search Courses',
					'not_found' =>  'No courses found',
					'not_found_in_trash' => 'No courses found in Trash', 
					'parent_item_colon' => '',
					'menu_name' => 'Courses' ,
				);

		register_post_type( 	self::LS_TYPE_COURSE,
					array(
						'labels' => $labels,
						'public' => true,
						'has_archive' => true,
						'show_in_menu' => true,
                        'menu_position' => 6,
                        'supports' => array('title'),
                        'rewrite' => array('slug' => 'courses'),
                        'menu_icon' => 'dashicons-book-alt'
					)

		);

        register_taxonomy_for_object_type( 'post_tag', self::LS_TYPE_COURSE);


		$labels =	array(
					'name' => 'Lessons',
					'singular_name' => 'Lesson',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New Lesson',
					'edit_item' => 'Edit Lesson',
					'new_item' => 'New Lesson',
					'all_items' => 'All Lessons',
					'view_item' => 'View Lesson',
					'search_items' => 'Search Lessons',
					'not_found' =>  'No lessons found',
					'not_found_in_trash' => 'No lessons found in Trash', 
					'parent_item_colon' => '',
					'menu_name' => 'Lessons' ,
				);

		register_post_type( 	self::LS_TYPE_LESSON,
					array(
						'labels' => $labels,
						'public' => true,
						'has_archive' => true,
						'show_in_menu' => true,
                        'menu_position' => 7,
                        'rewrite' => array('slug' => 'lessons'),
                        'menu_icon' => 'dashicons-welcome-learn-more'
						//'capability_type' => 'lessons',
						//'capabilities' => $caps,
						//'map_meta_cap' => true,
					)

		);
        register_taxonomy_for_object_type( 'post_tag', self::LS_TYPE_LESSON);

        $labels = array(
            'name' => __('Subjects'),
            'singular_name' => __('Subject'),
            'search_items' => __('Search Subjects'),
            'popular_items' => __('Popular Subjects'),
            'all_items' => __('All Subjects'),
            'parent_item' => __('Parent Subject'),
            'edit_item' => __('Edit Subject'),
            'update_item' => __('Update Subject'),
            'add_new_item' => __('Add New Subject'),
            'new_item_name' => __('New Subject'),
            'separate_items_with_commas' => __('Separate Subjects with commas'),
            'add_or_remove_items' => __('Add or remove Subjects'),
            'choose_from_most_used' => __('Choose from most used Subjects'),
            'not_found' => __('No Subjects found')
        );

        $args = array(
            'labels' => $labels,
            'public' => TRUE,
            'hierarchical' => TRUE,
            'show_ui' => TRUE,
            'show_in_nav_menus' => TRUE,
            'query_var' => TRUE
        );

        register_taxonomy(self::LS_TAX_SUBJECT, array(self::LS_TYPE_LESSON,self::LS_TYPE_COURSE), $args);

        add_rewrite_rule('^stone/([^/]*)/?', 'index.php?' . self::LS_FLD_LEARNSTONE . '=$matches[1]', 'top');

		$this->get_session();
	}

    function wp_dashboard_setup()
    {
        wp_add_dashboard_widget(self::LS_WID_CLASS, "Classes", array($this, 'widget_display'));
    }

    function widget_display()
    {
        global $wpdb;
        $current_user = wp_get_current_user();
		$user = $current_user->ID;
        $table_name = $wpdb->prefix . self::LS_TBL_CLASSES;
        $classes = $wpdb->get_results("SELECT classid,post_title,post_name FROM $table_name LEFT JOIN {$wpdb->posts} ON {$wpdb->posts}.ID=$table_name.classid WHERE $table_name.user=$user AND $table_name.member=1");
        echo "<div>";
        echo "<h4>Join Class</h4>";
        echo "<div>";
        echo $this->shortcode_classid(array('widget' => TRUE));
        echo "</div>";
        echo "<hr />";
        echo "<div>";
        echo "<h4>Classes Joined</h4>";
        foreach($classes as $class)
        {
            echo "<h5><a href='" . get_permalink($class->classid) . "'>" . strtoupper($class->post_name) . "</a></h5><p>" . $class->post_title . "</p>";            
        }
        if(count($classes) == 0)
        {
            echo "<p>None</p>";
        }
        echo("</div>");
        echo("</div>");
    }

    function manage_ls_class_posts_columns($defaults)
    {
        $index = 0;
        $ret = array();
        foreach($defaults as $key => $val)
        {
            $ret[$key] = $val;
            if($index == 1)
            {
                $ret['author'] = "Author";        
                $ret['post_name'] = "Class Id";        
            }
            $index ++;
        }
        return $ret;
    }

    function manage_ls_class_posts_custom_column($column_name, $post_id)
    {
        if($column_name == "post_name")
        {
            $post = get_post($post_id);
            echo(strtoupper($post->post_name));       
        }
    }

    function add_meta_boxes_ls_lesson()
    {
        $status = $this->get_lesson_error();
        if($status == self::LS_STATUS_RESOLVE || $status == self::LS_STATUS_BAD_RESOLVE)
        {
            add_meta_box('ls_resolve', __('Resolve'),  array($this, 'add_meta_box_ls_lesson'), self::LS_TYPE_LESSON, "normal" );
        }
    }

    function add_meta_boxes_ls_course()
    {
        add_meta_box('ls_editor', __('Content Editor (top level only)'),  array($this, 'add_meta_box_ls_editor'), self::LS_TYPE_COURSE, "normal" );
    }

    function add_meta_boxes_ls_class()
    {
        add_meta_box('ls_editor', __('Content Editor (top level only)'),  array($this, 'add_meta_box_ls_editor'), self::LS_TYPE_CLASS, "normal" );
        add_meta_box('ls_users', __('Students'),  array($this, 'add_meta_box_ls_students'), self::LS_TYPE_CLASS, "normal" );
    }

    function add_meta_box_ls_students()
    {
        echo($this->get_students_list(get_the_ID(), FALSE));
    }

    function add_meta_box_ls_editor()
    {
        $options = get_user_option(self::LS_OPT_SEARCH);
        if($options === FALSE)
        {
            $options = array(self::LS_FLD_SEARCH_TYPE => 3,
                             self::LS_FLD_POST_ID => get_the_ID(),
                             'author' => 0,
                             'orderby' => 'date',
                             'tag_id' => 0,
                             self::LS_TAX_SUBJECT => 0,
                             'ls_insert_mode' => 'after',
                             'ls_title' => ''
                             );
            add_user_meta(get_current_user_id(), self::LS_OPT_SEARCH, $options, TRUE);
        }
        ?>
        <h4>Search</h4>
        <div class="ls_course_inner">
            <div class="ls_content">
                <p>
                    <label>Search On:
                        <select name='<?php echo self::LS_FLD_SEARCH_TYPE; ?>' id='<?php echo self::LS_FLD_SEARCH_TYPE; ?>'>
                            <option value='3' <?php echo ($options[self::LS_FLD_SEARCH_TYPE] == "3" ? "selected" : ""); ?>>Courses and Lessons</option>
                            <option value='1' <?php echo ($options[self::LS_FLD_SEARCH_TYPE] == "1" ? "selected" : ""); ?>>Courses Only</option>
                            <option value='2' <?php echo ($options[self::LS_FLD_SEARCH_TYPE] == "2" ? "selected" : ""); ?>>Lessons Only</option>
                        </select>
                    </label>
                </p>
                <p>
                    <label>Order By:
                        <select name='orderby' id='orderby'>
                            <option value="date" <?php echo ($options['orderby'] == "date" ? "selected" : ""); ?>>Date</option>
                            <option value="title" <?php echo ($options['orderby'] == "title" ? "selected" : ""); ?>>Title</option>
                        </select>

                        <input name='<?php echo self::LS_FLD_LIMIT; ?>' id='<?php echo self::LS_FLD_LIMIT; ?>' type='hidden' value='<?php echo self::LS_VAL_PAGE_SIZE; ?>' />
                    </label>
                </p>
                <p>
                    <label>Author: <?php
                        $args = array(  'show_option_all' => '(Any)',
                            'orderby' => 'name',
                            'name' => 'author',
                            'selected' => ($options['author'] == 0 ? FALSE : $options['author']),
                            'who' => 'authors'
                            );
                        wp_dropdown_users($args); ?>
                    </label>
                </p>
                <p>
                    <label>Tags:<?php
                            $args = array( 'taxonomy' => 'post_tag',
                            'show_option_all' => '(Any)',
                            'orderby' => 'name',
                            'name' => 'tag_id',
                            'selected' => ($options['tag_id'] == 0 ? FALSE : $options['tag_id'])
                            );
                            wp_dropdown_categories($args);?>
                    </label>
                </p>
                <p>
                    <label>Subject:<?php
                        $args = array( 'taxonomy' => self::LS_TAX_SUBJECT,
                            'show_option_all' => '(Any)',
                            'hierarchical' => 1,
                            'orderby' => 'name',
                            'name' => self::LS_TAX_SUBJECT,
                            'selected' => ($options[self::LS_TAX_SUBJECT] == 0 ? FALSE : $options[self::LS_TAX_SUBJECT])
                            );
                        wp_dropdown_categories($args);?>
                    </label>
                </p>
                <p>
                    <label>Title:<input name='<?php echo self::LS_FLD_TITLE; ?>' id='<?php echo self::LS_FLD_TITLE; ?>' type='text' value='<?php echo esc_attr($options[self::LS_FLD_TITLE]) ?>' /></label>
                </p>
            </div>
            <div class="ls_sidebar">
                <p>
                    <?php submit_button(__('Search'), 'secondary', 'ls_search_sub', FALSE, array('id' => 'ls_search_sub') ); ?>
                </p>
                <hr />
                <p>
                    Insert Mode:<br />
                    Before:<input type='radio' name='ls_insert_mode' value='before' <?php echo ($options['ls_insert_mode'] == 'before' ? "checked='checked'" : ""); ?> /><br />
                    After:<input id="ls_insert_mode" type='radio' name='ls_insert_mode' value='after' <?php echo ($options['ls_insert_mode'] == 'after' ? "checked='checked'" : ""); ?> />
                </p>
            </div>
        </div>
        <h4>Results</h4><?php
            $list = $this->get_course_list(self::LS_MODE_CLASS_EDIT, get_the_ID(), FALSE); ?>
        <div id='ls_results'><?php echo($this->get_search_html($options));  ?></div>
        <h4>Content</h4>
        <div class="ls_course_inner">
            <div id="ls_content" class="ls_content"><?php                             
                echo($list); ?>
            </div>
            <div class="ls_sidebar">
                <p><?php submit_button(__('Move Up'), 'secondary', 'ls_move_up', FALSE );?></p>
                <p><?php submit_button(__('Move Down'), 'secondary', 'ls_move_down', FALSE );?></p>
                <p><?php submit_button(__('Delete'), 'secondary', 'ls_delete', FALSE ); ?></p>
                <hr />
                <p><?php
                    submit_button(__('Switch To Bulk Actions'), 'secondary', 'ls_bulk', FALSE ) ?>
                </p>
            </div>
        </div>
    <?php
    }


    function add_meta_box_ls_lesson()
    {
        // This is put in an element with id normal-sortables
        // The edit container is put in element id post-body-container

        global $wpdb;
        $table_name = $wpdb->prefix . self::LS_TBL_LEARNSTONES;
        $id = get_the_ID();

        $ret = $this->get_new_order($id, array(), TRUE);

        if($ret[0] == self::LS_STATUS_RESOLVE || $ret[0] == self::LS_STATUS_BAD_RESOLVE)
        {
            $neworder = $ret[1];
            $oldlss = $ret[2];

            echo '<p>Resolve the following Learnstones to their previous name, or resolve as &quot;(New Learnstone)&quot;</p>';
            foreach($neworder as $key => $value)
            {
                if($value[0]==-1)
                {
                    $options = "<option value='-1'>(New Learnstone)</option>";
                    foreach($oldlss as $oldls)
                    {
                        similar_text(strtolower($oldls->name),strtolower($value[1]), $pc);
                        $selected = '';
                        if($pc > 50.0)
                        {
                            $selected='selected="true" ';
                        }
                        $options .= "<option $selected value='" . $oldls->name . "'>Renamed from &quot;" . $oldls->name . "&quot;</option>";
                    }

                    echo("<p><label class='ls_meta_box_label' for='" . self::LS_FLD_CONFIRMED . "$key'>" . $value[1] . "</label>");
                    echo("<select id='" . self::LS_FLD_CONFIRMED . "$key' name='" . self::LS_FLD_CONFIRMED . "[$key]'>$options</select></p>");
                }
            }
        }
    }

    function edit_form_after_title()
    {
        global $current_screen;
        global $post;
        global $post_type;
        if(get_post_type() == self::LS_TYPE_CLASS ) { ?>
            <div id='titlediv1'>
                <div id='titlewrap'>
                    <label class='screen-reader-text-' id='title-prompt-text' ><?php  echo __('Class Id') ?>:</label><?php 
                    $title = "New";
                    if($current_screen->action == 'add') 
                    {
                        $title = $this->get_new_class_id();?>
                        <input type="hidden" name="<?php echo(self::LS_FLD_NEW_CLASS_NAME); ?>" value="<?php echo(esc_attr($title))?>" /><?php 
                    }
                    else
                    {
    	                $title = $post->post_name;
                    } ?>
                    <label><?php echo esc_html( strtoupper( $title ) ) ?></label><?php
                        if($current_screen->action != 'add')
                        {
                            submit_button(__('Regenerate'), 'small', self::LS_FLD_CLASS_REGEN, FALSE);
                        }
                    ?>
                </div>
            </div><?php 
        }        
    }

    function get_lesson_error()
    {
        $status = get_post_meta(get_the_ID(), self::LS_CUSTOM_STATUS, TRUE);
        if($status !== FALSE && $status != self::LS_STATUS_OK)
        {
            return $status;
        }
        return self::LS_STATUS_OK;
    }

    function admin_notices()
    {
        global $pagenow;
        if ( ('post-new.php' == $pagenow || 'post.php' == $pagenow) && get_post_type() == self::LS_TYPE_LESSON ) {
            $status = $this->get_lesson_error();
            if($status != self::LS_STATUS_OK)
            {
                $error = "";
                if($status == self::LS_STATUS_NO_TITLE)
                {
                    $error = "One of your Learnstones does not have a title";
                }
                else if($status == self::LS_STATUS_DUPLICATE)
                {
                    $error = "Two of your Learnstones have the same title";
                }
                else if($status == self::LS_STATUS_TITLE_TOO_LONG)
                {
                    $error = "One of your Learnstones has a title greater than " . self::LS_MAX_TITLE_SIZE . " characters";
                }
                else if($status == self::LS_STATUS_BAD_RESOLVE)
                {
                    $error = "Two of your Learnstones were resolved to the same historic Learnstone.  Learnstones need to be resolved again";
                }
                else if($status == self::LS_STATUS_INVALID_SC)
                {
                    $error = "The lesson contains an invalid short code syntax";
                }
                else
                {
                    $error = "Learnstones have been renamed or created and require resolution";                                
                }
                echo "<div class='error'>$error</div>";
            }
        }
    }

    function admin_enqueue_scripts($hook)
    {
        if ( ('post-new.php' == $hook || 'post.php' == $hook) ) {
            if(get_post_type() == self::LS_TYPE_LESSON ) {
                $this->enqueue_scripts(self::LS_MODE_LESSON_EDIT);
            }
            else if(get_post_type() == self::LS_TYPE_COURSE) {
                $this->enqueue_scripts(self::LS_MODE_COURSE_EDIT);
            }
            else if(get_post_type() == self::LS_TYPE_CLASS) {
                $this->enqueue_scripts(self::LS_MODE_CLASS_EDIT);
            }
        }
        else if($this->settings_page == $hook) {
            $this->enqueue_scripts(self::LS_MODE_SETTINGS);                        
        }
    }

    function show_admin_bar()
    {
        global $show_admin_bar;
        if( is_singular() ) {
            if(get_post_type() == self::LS_TYPE_LESSON) {
                return FALSE;
            }
        }
        return $show_admin_bar;
    }

    function login_enqueue_scripts()
    {
       $this->enqueue_scripts(self::LS_MODE_LOGIN);         
    }

    function wp_enqueue_scripts()
    {
        if($this->dashboard)
        {
            $this->enqueue_scripts(self::LS_MODE_DASHBOARD_VIEW); 
        }               
        elseif ( is_singular() ) {
            if(get_post_type() == self::LS_TYPE_LESSON) {
                $this->enqueue_scripts(self::LS_MODE_LESSON_VIEW);                    
            }
            elseif (get_post_type() == self::LS_TYPE_CLASS) {
                $this->enqueue_scripts(self::LS_MODE_CLASS_VIEW);                    
            } else {
                $this->enqueue_scripts(self::LS_MODE_DEFAULT);
            }
        }
    }

    function enqueue_scripts($viewMode)
    {
        global $wpdb;
        global $current_user;
        global $post;

        $deps = array();
        $deps[] = "jquery";
        $lsAjaxDat = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'LS_STYLES' => self::LS_STYLES, 'nonce' => wp_create_nonce(self::LS_NONCE), self::LS_FLD_POST_ID => get_the_ID(), 'view_mode' => $viewMode);
        $lsDep = array();
        $mainScript = 'js/ls.js';
        if($viewMode == self::LS_MODE_LESSON_VIEW)
        {
            $postId = get_the_ID();
            $table_name = $wpdb->prefix . self::LS_TBL_LEARNSTONES;
            $lss = $wpdb->get_results("SELECT id FROM $table_name WHERE post=$postId ORDER BY lsorder", OBJECT);
            $lsad = array();
            foreach($lss as $ls)
            {
                $lsad[] = $ls->id;
            }
            $lsAjaxDat['lss'] = $lsad;
            $lsAjaxDat['select'] = $this->select_learnstone;
            $options = get_option(self::LS_OPT_MAIN);
		    $slide = "default";
		    if(isset($options[self::LS_OPT_SLIDE]))
		    {
			    $slide = $options[self::LS_OPT_SLIDE];
		    }
            $slidefolder = plugin_dir_path(__FILE__) . self::LS_SLIDESHOW_FOLDER . "/";
		    if(!file_exists($slidefolder . $slide))
		    {
			    $slideshows = scandir(plugin_dir_path(self::LS_SLIDESHOW_FOLDER));
			    foreach($slideshows as $slideshow)
			    {
				    if(strpos($slideshow, ".") !== 0)
				    {
					    $slide = $slideshow;
					    $options[self::LS_OPT_SLIDE] = $slideshow;
					    update_option(self::LS_OPT_MAIN, $options);
					    break;
				    }
			    }
		    }
            
            $slidefolder = $slidefolder . $slide;

		    $files = scandir($slidefolder . "/js");
		    $cnt = 0;
		    $dep = "";
		    foreach($files as $file)
		    {
			    if(strpos($file, ".js") !== FALSE)
			    {
				    $jsFile = 'ls_theme_script_' . $cnt;
				    if($cnt > 0)
				    {
					    wp_register_script( $jsFile , plugins_url( self::LS_SLIDESHOW_FOLDER . "/$slide/js/" . $file, __FILE__), array('jquery', $dep), FALSE, TRUE );
				    }
				    else
				    {
					    wp_register_script( $jsFile , plugins_url(self::LS_SLIDESHOW_FOLDER . "/$slide/js/" . $file, __FILE__), array('jquery'), FALSE, TRUE );
				    }
				    $dep = $jsFile;
                    $lsDep = array($dep);
					    wp_register_script( $jsFile , plugins_url( self::LS_SLIDESHOW_FOLDER . "/$slide/js/" . $file, __FILE__), array('jquery', $dep), FALSE, TRUE );
				    wp_enqueue_script( $jsFile, array (self::LS_SCRIPT), TRUE );
				    $cnt++;
			    }
		    }
            // Just for convenience register jquery ui
            wp_enqueue_script( 'jquery-ui-core' );


		    $files = scandir($slidefolder . "/css");
		    $cnt = 0;
		    $dep = "";
		    $deps = array();
		    foreach($files as $file)
		    {
			    if(strpos($file, ".css") !== FALSE)
			    {
				    $style = 'ls_slideshow_style_' . $cnt;
				    if($cnt > 0)
				    {
					    wp_register_style( $style, plugins_url( self::LS_SLIDESHOW_FOLDER . "/$slide/css/" . $file, __FILE__), array( $dep ));
				    }
				    else
				    {
					    wp_register_style( $style, plugins_url( self::LS_SLIDESHOW_FOLDER . "/$slide/css/" . $file, __FILE__));
				    }
				    wp_enqueue_style( $style );
				    $dep = $style;
				    $deps[] = $style;
				    $cnt++;
			    }
		    }

            $this->get_session();
            $val = -1;
            $login_post = 0;
            if($this->session_class != 0)
            {
                $login_post = $this->session_class;
            }
            //else
            //{
            //   $login_post = get_the_ID(); 
            //}
            if($login_post != 0)
            {
                $owner = get_post_field("post_author", $login_post );
                if($owner != 0)
                {
                    $val = get_user_option(self::LS_OPT_LOGIN_WARNU, $owner);
                }
            }

            if($val === FALSE || $val == -1)
            {
                if(isset($options[self::LS_OPT_LOGIN_WARN]))
                {
                    $val = $options[self::LS_OPT_LOGIN_WARN];
                }
                else
                {
                    $val = self::LS_OPT_LOGIN_WARN_DEFAULT;
                }
            }
            $lsAjaxDat['login_warn'] = $val * 1000;
            $lsAjaxDat['auto_answers'] = $this->auto_answers;

		    wp_register_style( 'ls_theme_style', plugins_url('css/slide.css', __FILE__), $deps );
            $this->max_width = 100 * count($lss);
		    wp_register_style( 'ls_theme_style_small', plugins_url('css/slide_small.css', __FILE__), array( 'ls_theme_style' ) );
		    wp_enqueue_style( 'ls_theme_style' );
		    wp_enqueue_style( 'ls_theme_style_small' );
        }
        elseif($viewMode == self::LS_MODE_CLASS_VIEW)
        {
            // Not sure what prev and next class present
            if($current_user->ID != $post->post_author)
            {
                wp_register_style( self::LS_STYLE, plugins_url('css/ls_class.css', __FILE__));
	            wp_enqueue_style( self::LS_STYLE ); 
            }           
        }
        elseif($viewMode == self::LS_MODE_SETTINGS)
        {
            wp_register_style( self::LS_STYLE, plugins_url('css/ls.css', __FILE__));
	        wp_enqueue_style( self::LS_STYLE );
            $mainScript = 'js/ls_settings.js';
        }
        elseif($viewMode == self::LS_MODE_DEFAULT)
        {
            wp_register_style( self::LS_STYLE, plugins_url('css/ls.css', __FILE__));
	        wp_enqueue_style( self::LS_STYLE );
            $mainScript = '';
        }
        else {
            wp_register_style( self::LS_STYLE, plugins_url('css/ls.css', __FILE__));
	        wp_enqueue_style( self::LS_STYLE );
            if($viewMode == self::LS_MODE_LESSON_EDIT)
            {
                $status = $this->get_lesson_error();
                if($status == self::LS_STATUS_RESOLVE || $status == self::LS_STATUS_BAD_RESOLVE)
                {
                    wp_register_style( self::LS_STYLE_EDIT, plugins_url('css/ls_edit.css', __FILE__));
	                wp_enqueue_style( self::LS_STYLE_EDIT );
                }
            }
            else
            {
                $lsAjaxDat['ls_page'] = self::LS_VAL_PAGE_SIZE;
            }
        }
        if(!empty($mainScript))
        {
            wp_register_script( self::LS_SCRIPT, plugins_url($mainScript, __FILE__), $lsDep, FALSE, TRUE );
		    wp_enqueue_script( self::LS_SCRIPT );
            wp_register_style( self::LS_STYLE_HIGHLIGHT, plugins_url('highlight/styles/default.css', __FILE__));
	        wp_enqueue_style( self::LS_STYLE_HIGHLIGHT );
            wp_register_script( self::LS_SCRIPT_HIGHLIGHT, plugins_url('highlight/highlight.pack.js', __FILE__));
            wp_enqueue_script( self::LS_SCRIPT_HIGHLIGHT );
            wp_register_script( self::LS_SCRIPT_HEAD, plugins_url('js/ls_head.js', __FILE__), array('jquery'));
		    wp_localize_script( self::LS_SCRIPT_HEAD, 'lsAjax', $lsAjaxDat);
		    wp_enqueue_script( self::LS_SCRIPT_HEAD );
            wp_register_script( self::LS_SCRIPT_FOOT, plugins_url('js/ls_foot.js', __FILE__), array(), FALSE, TRUE );
        }
        wp_register_style( self::LS_STYLE_ALL, plugins_url('css/ls_all.css', __FILE__));
	    wp_enqueue_style( self::LS_STYLE_ALL );
        wp_register_style( self::LS_STYLE_BESPOKE, plugins_url('css/ls_bespoke.css', __FILE__));
	    wp_enqueue_style( self::LS_STYLE_BESPOKE );
    }

    function style_loader_tag( $html, $handle )
    {
        if($handle !== 'ls_theme_style_small')
        {
            return $html;
        }
        return str_replace( "media='all'", "media='all and (max-width: " . $this->max_width . "px)'", $html );
    }

	function post_row_actions($actions, $post)
	{
		if ($post->post_type == self::LS_TYPE_LESSON){
	             $actions['dashboard'] = "<a title='" . esc_attr(__('Dashboard')) . "' href='" . add_query_arg( array(self::LS_FLD_DASH => "1"), get_permalink( $post )) . "'>" . __('Dashboard') . "</a>";
	             if(is_plugin_active(TCPDF_PLUGIN))
                 {
                     $actions['pdf'] = "<a title='" . esc_attr(__('PDF')) . "' href='" . add_query_arg( array(self::LS_FLD_PDF => "1"), get_permalink( $post )) . "'>" . __('PDF') . "</a>";
	             }
    	}
		return $actions;
	}

	function admin_menu() {
	    global $menu;
        $menu[9] = $menu[5];
        unset($menu[5]);
    	$this->settings_page = add_options_page('Learnstones', 'Learnstones', 'administrator', __FILE__, array($this, 'build_options_page' ) );
	}

	function build_options_page() {
        $current_tab = isset($_GET['tab']) ? $_GET['tab'] : self::LS_OPT_MAIN;
		?>
            <h2 class="nav-tab-wrapper"><?php
                foreach($this->LS_OPT_SECTIONS as $t => $caption )
                {
                    echo ("<a class='nav-tab " . ($current_tab == $t ? 'nav-tab-active' : '') . "' href='?page=" . __FILE__ . "&tab=$t'>$caption</a>");
                } ?>
            </h2>
			<div id="wrap">
				<div class="icon32" id="icon-tools"> <br /> </div>
				<h2>Learnstones Options</h2>
				<form method="post" action="options.php">
					<?php settings_fields($current_tab); ?>
					<?php do_settings_sections($current_tab); ?>
					<?php submit_button(); ?>
				</form>
			</div>
		<?php
	}

	function admin_init() {
        foreach($this->LS_OPT_SECTIONS as $setting => $caption)
        {
    		register_setting($setting, $setting);
   	    	add_settings_section($setting . "section", $caption, array($this, 'add_settings_section'), $setting);                        
        }
		$this->add_settings_field_to_tab(self::LS_OPT_SESSION_DURATION, 'Learnstones Session Duration:', self::LS_OPT_MAIN, 0);
		$this->add_settings_field_to_tab(self::LS_OPT_SESSION_PURGE, 'Purge Sessions from Database:', self::LS_OPT_MAIN, 0);
  		$this->add_settings_field_to_tab(self::LS_OPT_SLIDE, 'Slideshow:', self::LS_OPT_MAIN, "oconner");
  		$this->add_settings_field_to_tab(self::LS_OPT_LOGIN_WARN, 'Login Warning:', self::LS_OPT_MAIN, self::LS_OPT_LOGIN_WARN_DEFAULT);
  		$this->add_settings_field_to_tab(self::LS_OPT_CLASSID_SIZE, 'Class Id Size:', self::LS_OPT_MAIN, self::LS_CLS_CODE_SIZE);
  		$this->add_settings_field_to_tab(self::LS_OPT_CLASSID_CHARS, 'Class Id Chars:', self::LS_OPT_MAIN, self::LS_CLS_CHARS);
  		$this->add_settings_field_to_tab(self::LS_OPT_INPUT_DISP, 'Input Dashboard Formats:', self::LS_OPT_MAIN, array());
  		$this->add_settings_field_to_tab(self::LS_OPT_GOOGLE_ENABLE, 'Google Login:', self::LS_OPT_GOOGLE);
  		$this->add_settings_field_to_tab(self::LS_OPT_GOOGLE_CLIENT_ID, 'Google Client Id:', self::LS_OPT_GOOGLE);
  		$this->add_settings_field_to_tab(self::LS_OPT_GOOGLE_CLIENT_SECRET, 'Google Client Secret:', self::LS_OPT_GOOGLE);
  		$this->add_settings_field_to_tab(self::LS_OPT_GOOGLE_KEY, 'Google Server Key:', self::LS_OPT_GOOGLE);
  		$this->add_settings_field_to_tab(self::LS_OPT_DOMAINS_ENABLE, 'Filter Domains:', self::LS_OPT_GOOGLE);
  		$this->add_settings_field_to_tab(self::LS_OPT_DOMAINS, 'Domains:', self::LS_OPT_GOOGLE);
  		$this->add_settings_field_to_tab(self::LS_OPT_MESSAGE, 'Login Message:', self::LS_OPT_MAIN, self::LS_MSG_LOGIN);
  		$this->add_settings_field_to_tab(self::LS_OPT_LIGHTS, 'Lights:', self::LS_OPT_MAIN, $this->LS_LIGHTS_DEFAULT);
  		$this->add_settings_field_to_tab(self::LS_OPT_DOMAINS, 'Domains:', self::LS_OPT_GOOGLE);
  		$this->add_settings_field_to_tab(self::LS_OPT_PDF_PLUGIN, 'PDF Plugin Active:', self::LS_OPT_PDF);
  		$this->add_settings_field_to_tab(self::LS_OPT_PDF_LIGHTS, 'PDF Lights Text:', self::LS_OPT_PDF, self::LS_PDF_LIGHTS_DEFAULT);
	}

    function add_settings_field_to_tab($option, $caption, $tab, $default = "")
    {
        add_settings_field($option, $caption, array($this, 'add_settings_field'), $tab, $tab . "section", array($option, $tab, $default));
    }

	function add_settings_section() {
	}

	function add_settings_field($args) {
        $current_tab = isset($_GET['tab']) ? $_GET['tab'] : self::LS_OPT_MAIN;

		$options = get_option($current_tab);
        $arg = $args[0];
        if(isset($options[$arg]))
        {
            $val = $options[$arg];
        }
        else
        {
            $val = $args[2];
        } 

		if($arg == self::LS_OPT_SESSION_DURATION)
		{ ?>
			<select name='<?php echo $current_tab . "[" . $arg . "]" ?>' >
				<option value='0'		<?php if($val == 0) { echo "selected='true'"; } ?>>Browser Session</option>
				<option value='86400'		<?php if($val == 86400) { echo "selected='true'"; } ?>>1 Day</option>
				<option value='2592000'		<?php if($val == 2592000) { echo "selected='true'"; } ?>>30 Days</option>
				<option value='31536000'	<?php if($val == 31536000) { echo "selected='true'"; } ?>>1 Year</option>
			</select> <?php
		}
		elseif($arg == self::LS_OPT_SESSION_PURGE)
		{ ?>
			<select name='<?php echo $current_tab . "[" . $arg . "]" ?>' >
				<option value='0'		<?php if($val == 0) { echo "selected='true'"; } ?>>Never</option>
				<option value='86400'		<?php if($val == 86400) { echo "selected='true'"; } ?>>1 Day</option>
				<option value='2592000'		<?php if($val == 2592000) { echo "selected='true'"; } ?>>30 Days</option>
				<option value='31536000'	<?php if($val == 31536000) { echo "selected='true'"; } ?>>1 Year</option>
			</select> <?php
		}
		elseif($arg == self::LS_OPT_INPUT_DISP) {
            ?>
            <table class="wp-list-table widefat"  id="ls_input_disp">
                <thead><tr><th class="check-column ls_setting_th">Name</th><th  class="check-column ls_setting_th">Format</th><th class="check-column ls_setting_th">Link?</th><th class="check-column ls_setting_th">Delete?</th></tr></thead><tbody><?php
                $index = 0;
                $optprefix = $current_tab . "[" . self::LS_OPT_INPUT_DISP . "]";
                foreach($val as $row)
                {
                    if(!isset($row['deleted'])
                        && (!empty($row['name']) || !empty($row['format']))
                        ) {?>
                        <tr><td><input class='ls_ph' placeholder="New Input Format Name" name="<?php echo($optprefix . "[" . $index . "][name]") ?>" type="text" value="<?php echo($row['name'])?>" /></td><td><input class="ls_setting ls_ph" name="<?php echo($optprefix . "[" . $index . "][format]") ?>" type="text" value="<?php echo(esc_attr($row['format']))?>" placeholder="New Format" /><?php if(strpos(strtolower($row['format']), self::LS_TOK_INPUT) === FALSE){echo "<br />No " . self::LS_TOK_INPUT . " token"; }?></td><td><input  name="<?php echo($optprefix . "[" . $index . "][url]") ?>" type="checkbox" <?php if(isset($row['url'])) { echo "checked"; }?> /></td><td><input  name="<?php echo($optprefix . "[" . $index . "][deleted]") ?>" type="checkbox" <?php if(isset($row['deleted'])) { echo "checked"; }?> /></td></tr><?php 
                        $index++;
                    }
                }?>
                <tr><td><input class='ls_ph' placeholder="New Input Format Name" name="<?php echo($optprefix . "[" . $index . "][name]") ?>" type="text" value="" /></td><td><input class="ls_setting ls_ph" placeholder="New Format" name="<?php echo($optprefix . "[" . $index . "][format]") ?>" type="text" value="" /></td><td><input  name="<?php echo($optprefix . "[" . $index . "][url]") ?>" type="checkbox" /></td></tr>
            </tbody></table><?php 
        }
        elseif($arg == self::LS_OPT_LIGHTS) {
            ?>
            <table class="wp-list-table widefat" id="ls_lights_captions"><thead>
                <tr><th class="check-column ls_setting_th">Light</th><th class="check-column ls_setting_th">Caption</th></tr></thead><tbody><?php
                foreach($val as $light => $caption)
                {
                     $optprefix = $current_tab . "[" . self::LS_OPT_LIGHTS . "][$light]";?>
                     <tr><td><?php echo($light); ?></td><td><input class='ls_setting ls_ph' placeholder="New Light Caption" name="<?php echo($optprefix) ?>" type="text" value="<?php echo($caption)?>" /></td></tr><?php 
                }?></tbody>
            </table><?php 
        }
		elseif($arg == self::LS_OPT_CLASSID_SIZE) {?>
			<select name='<?php echo $current_tab . "[" . $arg . "]" ?>' ><?php
                for($loop = 1; $loop <=12; $loop++)
                {?>                                   ?>
				    <option value='<?php echo($loop); ?>'<?php if($val == $loop) { echo " selected='true'"; } ?>><?php echo($loop); ?></option><?php                     
                }?>
            </select><?php 
        }
		elseif($arg == self::LS_OPT_GOOGLE_ENABLE || $arg == self::LS_OPT_DOMAINS_ENABLE) { ?>
			<input type="checkbox" name='<?php echo $current_tab . "[" . $arg . "]" ?>' <?php if(!empty($val)) { echo "checked"; }?> /><?php  
        }
		elseif($arg == self::LS_OPT_GOOGLE_CLIENT_ID || $arg == self::LS_OPT_GOOGLE_CLIENT_SECRET || $arg == self::LS_OPT_GOOGLE_KEY) { ?>
			<input type="text" class="ls_setting" name='<?php echo $current_tab . "[" . $arg . "]" ?>' value="<?php echo(esc_attr($val)) ?>"/><?php  
        }
		elseif($arg == self::LS_OPT_CLASSID_CHARS) { ?>
			<input type="text" class="ls_setting" name='<?php echo $current_tab . "[" . $arg . "]" ?>' value="<?php echo(esc_attr($val)) ?>"/><?php  
        }
		elseif($arg == self::LS_OPT_DOMAINS) { ?>
            <textarea name='<?php echo $current_tab . "[" . $arg . "]" ?>'  class="ls_domains"><?php echo($val)?></textarea><p class="ls_domains">Comma delimited list of domains</p><?php
        }
		elseif($arg == self::LS_OPT_PDF_PLUGIN) {
                if(is_plugin_active(TCPDF_PLUGIN))
            {
                echo("Plugin active.");
            }
            else
            {
                echo("Please install or activate TCPDF plugin to allow PDF generation.");             
            }?>
		    <input type="hidden" name='<?php echo $current_tab . "[" . $arg . "]" ?>' value=""/><?php  
        }
		elseif($arg == self::LS_OPT_MESSAGE ||$arg == self::LS_OPT_PDF_LIGHTS) { ?>
            <textarea name='<?php echo $current_tab . "[" . $arg . "]" ?>'  class="ls_loginmsg"><?php echo($val)?></textarea><?php
        }
		elseif($arg == self::LS_OPT_LOGIN_WARN) { ?>
			<select name='<?php echo $current_tab . "[" . $arg . "]" ?>' >
				<option value='0'		<?php if($val == 0) { echo "selected='true'"; } ?>>Never</option>
				<option value='60'		<?php if($val == 60) { echo "selected='true'"; } ?>>1 Minute</option>
				<option value='300'		<?php if($val == 300) { echo "selected='true'"; } ?>>5 Minutes</option>
				<option value='600'		<?php if($val == 600) { echo "selected='true'"; } ?>>10 Minutes</option>
				<option value='1800'	<?php if($val == 1800) { echo "selected='true'"; } ?>>30 Minutes</option>
				<option value='3600'	<?php if($val == 3600) { echo "selected='true'"; } ?>>60 Minutes</option>
			</select> <?php
		}
        else
        {
            $slideshows = scandir(plugin_dir_path(__FILE__) . self::LS_SLIDESHOW_FOLDER); ?>
			<select name='<?php echo $current_tab . "[" . $arg . "]" ?>' ><?php
				foreach($slideshows as $slideshow)
				{
					if(strpos($slideshow, ".") !== 0)
					{?>
						<option value='<?php echo $slideshow; ?>' <?php if($val == $slideshow) { echo "selected='true'"; } ?>><?php count($slideshows); ?><?php echo $slideshow; ?></option><?php
					}
				} ?>
			</select> <?php
        }
	}

    function show_user_profile()
    {
        $this->show_learnstone_fields();
    }

    function edit_user_profile()
    {
        $this->show_learnstone_fields();
    }

    function show_learnstone_fields()
    {
        global $current_user;
        get_currentuserinfo();
        if(!user_can($current_user, 'subscriber'))
        {
            $val = get_user_option(self::LS_OPT_LOGIN_WARNU);
            if($val === FALSE)
            {
                $val = -1;
            }
            ?>
            <h3>Learnstones Options</h3>
            <table class="form-table">  
                <tr><th><label for="ls_loginwu">Login Warning</label></th>
                    <td>
	        	        <select name='ls_loginwu' id="ls_loginwu" >
			                <option value='-1'		<?php if($val == -1) { echo "selected='true'"; } ?>>(Default)</option>
			                <option value='0'		<?php if($val == 0)  { echo "selected='true'"; } ?>>Never</option>
         			        <option value='60'		<?php if($val == 60) { echo "selected='true'"; } ?>>1 Minute</option>
		        	        <option value='300'		<?php if($val == 300) { echo "selected='true'"; } ?>>5 Minutes</option>
        			        <option value='600'		<?php if($val == 600) { echo "selected='true'"; } ?>>10 Minutes</option>
        			        <option value='1800'	<?php if($val == 1800) { echo "selected='true'"; } ?>>30 Minutes</option>
        			        <option value='3600'	<?php if($val == 3600) { echo "selected='true'"; } ?>>60 Minutes</option>
        		        </select>
                    </td>
                </tr>
            </table> <?php
        }
    }

    function personal_options_update($user_id)
    {
        $this->update_learnstone_fields($user_id);
    }

    function edit_user_profile_update($user_id)
    {
        $this->update_learnstone_fields($user_id);
    }

    function update_learnstone_fields($user_id)
    {
        if(!current_user_can('edit_user', $user_id))
        {
            return FALSE;
        }

        $val = 0;
        if(isset($_POST['ls_loginwu']))
        {
            if(is_int($_POST['ls_loginwu']))
            {
                $val = $_POST['ls_loginwu'];
            }
        }   

        update_user_option($user_id, self::LS_OPT_LOGIN_WARNU, $val);
    }

	function map_meta_cap( $caps, $cap, $user_id, $args ) {

		/* If editing, deleting, or reading a lesson, get the post and post type object. */
		if ( 'edit_lesson' == $cap || 'delete_lesson' == $cap || 'read_lesson' == $cap ) {
			$post = get_post( $args[0] );
			$post_type = get_post_type_object( $post->post_type );

			/* Set an empty array for the caps. */
			$caps = array();
		}

		/* If editing a lesson, assign the required capability. */
		if ( 'edit_lesson' == $cap ) {
			if ( $user_id == $post->post_author )
				$caps[] = $post_type->cap->edit_posts;
			else
				$caps[] = $post_type->cap->edit_others_posts;
		}

		/* If deleting a lesson, assign the required capability. */
		elseif ( 'delete_lesson' == $cap ) {
			if ( $user_id == $post->post_author )
				$caps[] = $post_type->cap->delete_posts;
			else
				$caps[] = $post_type->cap->delete_others_posts;
		}

		/* If reading a private lesson, assign the required capability. */
		elseif ( 'read_lesson' == $cap ) {

			if ( 'private' != $post->post_status )
				$caps[] = 'read';
			elseif ( $user_id == $post->post_author )
				$caps[] = 'read';
			else
				$caps[] = $post_type->cap->read_private_posts;
		}

		/* Return the capabilities required by the user. */
		return $caps;
	}

    function posts_where( $where, &$wp_query)
    {
        global $wpdb;
        if($srch_title = $wp_query->get(self::LS_FLD_TITLE))
        {
           $where .= " AND {$wpdb->posts}.post_title LIKE '%" . like_escape($srch_title) . "%'";
        }
        return $where;
    }

   	function wp_ajax_nopriv_ls_submission()
	{
        global $wpdb;

  		if($_POST['type'] == "login")
        {
            $info = array();
            $info['user_login'] = $_POST['username'];
            $info['user_password'] = $_POST['password'];
            $info['remember'] = TRUE;

            $user_signon = wp_signon($info, FALSE);
            if(is_wp_error($user_signon))
            {
                $result = array('loggedin' => FALSE, 'message' => __('Bad Username or Password'));
            }
            else
            {
                $result = array();
				$classes = explode(" ", self::LS_STYLES);
                $current_user = wp_get_current_user();
			    $user = $current_user->ID;
	   		    $table_name = $wpdb->prefix . self::LS_TBL_RESPONSES;
                $post_id = $_POST[self::LS_FLD_POST_ID];
                $sql = $wpdb->prepare("SELECT stone, value FROM $table_name WHERE post = %d AND user = %d ORDER BY stone, time", array($post_id, $user));
			    $data = $wpdb->get_results($sql, OBJECT);
                foreach ($data as $lss)
				{
				    $result['ls_' . $lss->stone] = $classes[$lss->value];
				}
	   		    $table_name = $wpdb->prefix . self::LS_TBL_INPUTS;
                $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE post = %d AND user = %d", array($post_id, $user));
			    $data = $wpdb->get_results($sql, OBJECT);
                $result['inputs'] = array();
                foreach ($data as $inp)
                {
                    $result['inputs']['lsi_' . $inp->field] = $inp->value;
                }
                $result = array('loggedin' => TRUE, 'lss' => $result, 'nonce' => wp_create_nonce(self::LS_NONCE), 'logouturl' => wp_logout_url(), 'dburl' => get_dashboard_url($user), 'name' => $current_user->display_name, 'token' => wp_get_session_token(), 'tick' => wp_nonce_tick());
            }
        }
		elseif ($_POST['type'] == "mark")
		{
            $this->get_session();
            if(!$this->session_valid)
            {
                $result['response'] = "Session invalid due to merge!";
            }
            elseif (isset($_POST['response']))
            {
                $post_id = $_POST[self::LS_FLD_POST_ID];
                $result['response'] = "ok";
                $res = intval($_POST['response']);
                $marked = intval($_POST['marked']);

                $userOrSession = $this->session_id;
                $isUser = FALSE;
                if(is_user_logged_in())
		        {
			        $current_user = wp_get_current_user();
			        $userOrSession = $current_user->ID;
                    $isUser = TRUE;
                }

                if($res >= 0)
                {
    			    $this->set_learnstone_data($post_id, $_POST['learnstone'], $res, $marked, 0, $userOrSession, $isUser);
			    }
                if(isset($_POST['inputs']))
                {
                    $time = current_time('mysql');
                    foreach($_POST['inputs'] as $key => $input)
                    {
                        $this->set_lesson_data($post_id, $key, array($time => stripslashes($input)), $userOrSession, $isUser);
                    }
                }
            }
 		}
        else
        {
			$result['response'] = "Unauthorised accessz";
        }
    	die(json_encode($result));
	}

	function wp_ajax_ls_submission()
	{
        global $wpdb;

		if ( is_user_logged_in() && !wp_verify_nonce( $_POST['nonce'], self::LS_NONCE)) {
			$result['response'] = "Unauthorised access nonce";
		}
        else
        {
		    if ($_POST['type'] == "search")
            {
                echo($this->get_search_html($_POST));
                die();
            }
		    else
            {
                $post_id = $_POST[self::LS_FLD_POST_ID];
                if(!is_numeric($post_id))
                {
			        $result['response'] = "Unauthorised access:" . $post_id;                    
                }
                else if ($_POST['type'] == "merge")
                {
                    $result['response'] = 'ok';
                    if(isset($_POST['merges']))
                    {
                        $user = 0;
                        $sessions = array();
                        foreach($_POST['merges'] as $merge)
                        {
                            if(strpos($merge, 'session') === 0)
                            {
                                $session = intval(substr($merge, 7));
                                $sessions[] = $session;
                            }
                            elseif(strpos($merge, 'user') === 0)
                            {
                                if($user === 0)
                                {
                                    $user = intval(substr($merge, 4));
                                }
                                else
                                {
                                    $result['response'] = 'Multiple users!';
                                    break;
                                }
                            }
                        }
                        if($result['response'] === 'ok')
                        {
                            if($user === 0 && count($sessions) == 1)
                            {   
                                $result['response'] = 'Select more than 1 session to merge!';
                            }
                            else
                            {
                                $isUser = TRUE;
                                if($user === 0)
                                {
                                    //get latest session
                                    $table_name = $wpdb->prefix . self::LS_TBL_SESSION;

                                    $sql = "SELECT id FROM $table_name WHERE id IN (" . implode(',', array_fill(0, count($sessions), '%d')) . ") ORDER BY time DESC LIMIT 1";
                                    $sql = call_user_func_array(array($wpdb, 'prepare'), array_merge(array($sql), $sessions));
                                    $row = $wpdb->get_row($sql, OBJECT);
                                    $user = $row->id;
                                    $isUser = FALSE;
                                }
                           
                                foreach($sessions as $session)
                                {
                                    if($isUser || ($session != $user))
                                    {
                                        $this->merge_learnstone_data($session, $user, $isUser, FALSE);
                                        $this->clear_session(0, TRUE, $session, FALSE, FALSE);
                                    }
                                }
                                $result = $this->get_json_for($post_id, '', $user, $isUser);
                            }
                        }
                    }
                }
		        elseif($_POST['type'] == "dashboard")
                {
                    $latest = $_POST['from'];
                    $result = $this->get_json_for($post_id, $latest);
                }
		        elseif ($_POST['type'] == "format") {
		            if(isset($_POST['field']) && isset($_POST['format']))
                    {
                        $options = get_user_option(self::LS_OPT_INPUT_DISP_SEL);
                        if($options === FALSE)
                        {
                            $options = array();
                        }
                        $fld = substr($_POST['field'], 4);
                        $options['l0'][$fld] = $_POST['format'];
                        $options['l' . $post_id][$fld] = $_POST['format'];
                        update_user_option(get_current_user_id(), self::LS_OPT_INPUT_DISP_SEL, $options);
                    }
		        }
		        elseif ($_POST['type'] == "remove" || $_POST['type'] == "purge") {
                    $class = intval($_POST['classid']);
                    $canremove = FALSE;
                    $purge = ($_POST['type'] == "purge");
                    if($class == -1)
                    {
                        $canremove = TRUE;
                    }
                    else
                    {
                        $canremove = (get_post_field("post_author", $class ) == get_current_user_id());                
                    }

                    $info = "";
                    if($canremove)
                    {
                        $classes = array();
                        $this->get_classes( $post_id, $classes );

                        $inClass = "";
                        $otherClass = "";
                        $conj = "";
                        $conj2 = "";
                        foreach($classes as $classi)
                        {
                            if($classi[1])
                            {
                                $inClass .= $conj . $classi[0];
                                $conj = ",";
                            }
                            else
                            {
                                $otherClass .= $conj2 . $classi[0];
                                $conj2 = ",";
                            }
                        }

                        $time = current_time('mysql');
                        foreach($_POST['removes'] as $remove)
                        {
                            $isSession = FALSE;
                            $session = 0;
                            $uvinput = 0;
                            $suffix = "";
                            $table_name = $wpdb->prefix . self::LS_TBL_SESSION;
                            if(strpos($remove, "session") === 0)
                            {
                                $isSession = TRUE;
                                $session = intval(substr($remove, 7));
                                $suffix = self::LS_TBL_SESSION_SFX;
                            }
                            elseif (strpos($remove, "user") === 0)
                            {
                                $session = intval(substr($remove, 4));
                                if(($class == -1) || (get_user_option(self::LS_OPT_CLASS, $session) == $class))
                                {
                                    update_user_option($session, self::LS_OPT_CLASS, 0);
                                }
                            }

                            
                            $table_name = $wpdb->prefix . self::LS_TBL_CLASSES . $suffix;
                            $sql = "";
                            if($class != -1)
                            {
                                $sql = $wpdb->prepare("UPDATE $table_name SET member=0, dbupdate='$time' WHERE user=%d AND classid=%d", array($session, $class));
                            }
                            elseif(!empty($inClass))
                            {
                                $sql = $wpdb->prepare("UPDATE $table_name SET member=0, dbupdate='$time' WHERE user=%d AND classid IN ($inClass)", array($session));
                            }
                            if(!empty($sql))
                            {
                                $wpdb->query($sql);
                            }

                            if($purge)
                            {
                                $purgeAct = TRUE;
                                if(!empty($otherClass))
                                {
                                    $table_name = $wpdb->prefix . self::LS_TBL_CLASSES . $suffix;
                                    $sql3 = $wpdb->prepare("SELECT * FROM $table_name WHERE user=%d AND classid IN ($otherClass) AND member=1 LIMIT 1", array($session));
                                    if($wpdb->get_row($sql3) != NULL)
                                    {
                                        $purgeAct = FALSE;
                                    }
                                }


                                // See if lesson is in any classes
                                if($purgeAct)
                                {
                                    $table_name = $wpdb->prefix . self::LS_TBL_RESPONSES . $suffix;
                                    $sql = $wpdb->prepare("DELETE FROM $table_name WHERE post=%d AND user=%d", array($post_id, $session));
                                    $wpdb->query($sql);                                

                                    $table_name = $wpdb->prefix . self::LS_TBL_INPUTS . $suffix;
                                    $sql = $wpdb->prepare("DELETE FROM $table_name WHERE post=%d AND user=%d", array($post_id, $session));
                                    $wpdb->query($sql);                               
                                }
                            }
                            else
                            {
                                $table_name = $wpdb->prefix . self::LS_TBL_RESPONSES . $suffix;
                                $sql = $wpdb->prepare("UPDATE $table_name SET dbupdate='$time' WHERE user=%d AND post=%d", array($session, $post_id));
                                $wpdb->query($sql);

                                $table_name = $wpdb->prefix . self::LS_TBL_INPUTS . $suffix;
                                $sql = $wpdb->prepare("UPDATE $table_name SET dbupdate='$time' WHERE user=%d AND post=%d", array($session, $post_id));
                                $wpdb->query($sql);
                            }

                            
                            $table_name = $wpdb->prefix . self::LS_TBL_RESPONSES . $suffix;
                            $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE user=%d LIMIT 1", array($session));
                            $uvinput_row = $wpdb->get_row($sql);
                                
                            if($uvinput_row != NULL)
                            {
                                $uvinput = 1;
                            }
                            else
                            {
                                $table_name = $wpdb->prefix . self::LS_TBL_INPUTS . $suffix;
                                $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE user=%d LIMIT 1", array($session));
                                $uvinput_row = $wpdb->get_row($sql);

                                if($uvinput_row != NULL)
                                {
                                    $uvinput = 1;
                                }                                    
                            }

                            if($uvinput == 0)
                            {
                                $table_name = $wpdb->prefix . self::LS_TBL_CLASSES . $suffix;
                                $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE user=%d LIMIT 1", array($session));
                                $uvinput_row = $wpdb->get_row($sql);
                                if($uvinput_row != NULL)
                                {
                                    $uvinput = 1;
                                }
                            }

                                
                            $table_name = $wpdb->prefix . self::LS_TBL_SESSION;
                            if($isSession)
                            {
                                $wpdb->update($table_name, array('classid' => 0), array('id' => $session, 'classid' => $class), array("%d"), array("%d","%d"));
                                $wpdb->update($table_name, array('dbupdate' => $time, 'uvinput' => $uvinput), array('id' => $session), array("%s","%d"), array("%d"));
                            }
                            else
                            {
                                $wpdb->update($table_name, array('classid' => 0), array('user' => $session, 'classid' => $class), array("%d"), array("%d","%d"));
                                $wpdb->update($table_name, array('dbupdate' => $time, 'uvinput' => $uvinput), array('user' => $session), array("%s"), array("%d"));
                            }                             
                        }
                        $latest = $_POST['from'];
                        $result = $this->get_json_for($post_id, $latest);
                    }
                    else
                    {
                        $result['response'] = 'Remove failed because you are not class owner';
                    }
                }
		        elseif($_POST['type'] == "sub")
		        {
			        $author_id = get_post_field("post_author", $post_id );
			        $title = get_post_field("post_title", $post_id );
			        $email = get_the_author_meta( "user_email", $author_id );
			        // Fill array with responses
                    mail($email, $title . " response", print_r(array(), true));
			        $result['response'] = $email;
		        } 
            }
        }
		
        if(isset($result))
        {
    		echo json_encode($result);
    		die();
        }
        else
        {
            $this->wp_ajax_nopriv_ls_submission();
        }

	}

    function get_search_html( $vars )
    {
        global $wpdb;
        $html = "";
        
        $newopts = get_user_option(self::LS_OPT_SEARCH);
        foreach($vars as $key => $value)
        {
            $newopts[$key] = $value;
        }
        if(get_the_ID() === FALSE)
        {
            unset($newopts[self::LS_FLD_POST_ID]);
        }
        else
        {
            $newopts[self::LS_FLD_POST_ID] = get_the_ID();
        }
        update_user_option(get_current_user_id(), self::LS_OPT_SEARCH, $newopts);

        $args = array('post_status' => 'publish');
        $lim = self::LS_VAL_PAGE_SIZE;
        foreach($newopts as $key => $value)
        {
            if($key == self::LS_FLD_SEARCH_TYPE)
            {
                $v = intval($value);
                $post_types = array();
                if($v & 1)
                {
                    $post_types[] = self::LS_TYPE_COURSE;
                }
                if($v & 2)
                {
                    $post_types[] = self::LS_TYPE_LESSON;
                }
                $args['post_type'] = $post_types;
            }
            elseif($key == self::LS_TAX_SUBJECT)
            {
                if(!empty($value))
                {
                   $args['tax_query'] = array(array('taxonomy' => self::LS_TAX_SUBJECT,
                                            'terms' => $value));
                }
            }
            elseif($key == self::LS_FLD_POST_ID)
            {
                $args['post__not_in'] = array($value);
            }
            elseif($key == self::LS_FLD_LIMIT)
            {
                $lim = intval($value);
                $args['posts_per_page'] = $lim + 1;
            }
            else
            {
                $args[$key] = $value;
                if($key == 'orderby' && $value=='title' )
                {
                    $args['order'] = 'ASC';
                }
            }
        }
        
        $wpq = new WP_Query($args);
        if($wpq->have_posts())
        {
            $html = "<ul>";
            while($wpq->have_posts())
            {
                $post = $wpq->the_post();
                if($lim > 0)
                {
                    $html .= "<li class='" . get_post_type() . "_item'>";
                    $html .= " " . get_submit_button(__('Insert'), 'secondary', 'ls_insert_' . get_the_ID(), FALSE, array("data-item" => get_the_ID()) );
                    $html .= " <span data-item='" . get_the_ID() . "'>" . get_the_title() . "</span> - ";
                    if(get_post_type() == self::LS_TYPE_COURSE)
                    {
                        $html .= "Course - ";                    
                    }
                    $html .= get_the_author();
                    $html .= "</li>";
                }
                else
                {
                    $html .= "<li>" . get_submit_button(__('More Results'), 'secondary', 'ls_more', FALSE) . "</li>";                    
                }
                $lim--;
            }        
            $html .= "</ul>";
        }
        else
        {
            $html = "(No Results)";
        }

        return $html;
    }

	function login_redirect($redirect_to, $redirected_from, $user)
    {
        if(!empty($this->login_redirect))
        {
            return $this->login_redirect;
        }  
        else
        {
            if((get_user_option(self::LS_OPT_CLASS) !== FALSE) && (current_user_can('subscriber')))
            {
                return get_permalink(get_user_option(self::LS_OPT_CLASS));
            }
            else
            {
                return admin_url('index.php');
            }
        }
        return $redirect_to;
    }

	function login_message()
    {
        $options = get_option(self::LS_OPT_MAIN);
        if(isset($options[self::LS_OPT_MESSAGE]))
        {
            echo($options[self::LS_OPT_MESSAGE]);            
        }
        else
        {
            echo(self::LS_MSG_LOGIN);                     
        }
    }

	function login_form()
    {
        global $wpdb;
        $this->get_session();

        $ret = "";

        $class = "";
        $ret .= "<p>";

        $ret .= "<label for='" . self::LS_FLD_CLASS . "'>Class Id:<input name='" . self::LS_FLD_CLASS . "' type='text' value='' ></p>";

        if($this->session_uvinput == 1)
        {
            $ret .= "<p>";
            $ret .= "<label for='" . self::LS_FLD_CLEAR . "'><input id='" . self::LS_FLD_CLEAR . "' name='" . self::LS_FLD_CLEAR . "' type='checkbox' >";
            $ret .= "Clear session data created by ";
            if(empty($this->session_name))
            {
                $ret .= " an unknown user.";
            }
            else
            {
                $ret .= " user " . $this->session_name;
            }
            $ret .= "</label></p>";
        }
        $options = get_option(self::LS_OPT_GOOGLE);
        if(isset($options[self::LS_OPT_GOOGLE_ENABLE]) && !empty($options[self::LS_OPT_GOOGLE_ENABLE]) && !empty($options[self::LS_OPT_GOOGLE_CLIENT_ID]))
        {
            $ret .= "<hr class='ls_loginhr'>";
            $ret .= "<p class='ls_loginp'>";
            $ret .= "<input type='submit'  name='" . self::LS_FLD_GOOGLE_LI . "'   class='ls_glogin'  value='' /><br />Or";
            if($this->session_service == self::LS_SVC_GOOGLE && isset($_GET[self::LS_FLD_LOGGEDOUT]))
            {
                $ret .= "<br /><a href='http://www.google.com/accounts/Logout'>Log out</a> " . esc_html($this->session_name) . " from Google.<br />Or";
            }
            $ret .= "</p>";
        }
        echo $ret;
    }

    function set_logged_in_cookie( $cookie )
    {
        if(!isset($_COOKIE[LOGGED_IN_COOKIE]))
        {
            $_COOKIE[LOGGED_IN_COOKIE] = $cookie;
        }
    }

	function wp_login($user_login, $user)
	{
		//If a session is active, save it to the database
		global $wpdb;
        
        // copy to response data and input
        $this->get_session();
		wp_set_current_user($user->ID);
        $time = current_time('mysql');

        if($user != FALSE && !is_wp_error($user))
        {
            if(isset($_POST['wp-submit']))
            {
                $this->session_service = self::LS_SVC_WORDPRESS;
                $this->session_name = $user->display_name; 
                $table_name = $wpdb->prefix . self::LS_TBL_SESSION;
                $wpdb->update($table_name, array('uvname' => $this->session_name, 'service' => $this->session_service, 'time' => $time), array('id' => $this->session_id), array("%s", "%d", "%s"), array("%d"));
            }
        }


        if(isset($_POST[self::LS_FLD_CLASS]) && !empty($_POST[self::LS_FLD_CLASS]))
        {
            $class = $this->get_class_id($_POST[self::LS_FLD_CLASS]);
            if($class != 0)
            {
                $this->login_redirect = get_permalink($class);
                $this->session_class = $class;
            }
        }

        if($this->session_class != 0)
        {
            update_user_option($user->ID, self::LS_OPT_CLASS, $this->session_class);
        }

        if(!isset($_POST[self::LS_FLD_CLEAR]))
        {
            $this->merge_learnstone_data($this->session_id, $user->ID, TRUE, TRUE);
        }

        $table_name = $wpdb->prefix . self::LS_TBL_INPUTS;
        $count = $wpdb->update($table_name, array('dbupdate' => $time), array('user' => get_current_user_id()), array('%s'), array('%d'));

        $table_name = $wpdb->prefix . self::LS_TBL_RESPONSES;
        $count += $wpdb->update($table_name, array('dbupdate' => $time), array('user' => get_current_user_id()), array('%s'), array('%d'));

        $this->session_uvinput = ($count > 0) ? 1 : 0;
        $this->clear_session($user->ID, TRUE, $this->session_id, TRUE, TRUE);
	
    }

    function merge_learnstone_data($session, $userOrSession, $isUser, $mergeCurrentClass)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::LS_TBL_RESPONSES . self::LS_TBL_SESSION_SFX;
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE user=%d", array($session));
        $rows = $wpdb->get_results($sql);
        $time = current_time('mysql');
        foreach($rows as $row)
        {
            $marked = $row->marked;
            $data = unserialize($row->history);
            foreach($data as $key => $response)
            {
                $this->set_learnstone_data( $row->post, $row->stone, $response, $key, $marked, $userOrSession, $isUser);
            }
        }

        $table_name1 = $wpdb->prefix . self::LS_TBL_INPUTS . self::LS_TBL_SESSION_SFX;
        $sql = $wpdb->prepare("SELECT * FROM $table_name1 WHERE user=%d", array($session));
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row)
        {
            $this->set_lesson_data($row->post, $row->field, unserialize($row->history), $userOrSession, $isUser);
        }

        $table_name2 = $wpdb->prefix . self::LS_TBL_CLASSES . self::LS_TBL_SESSION_SFX;
        $sql = $wpdb->prepare("SELECT * FROM $table_name2 WHERE user=%d AND member=1", array($session));
        $rows = $wpdb->get_results($sql);

        $table_name2 = $wpdb->prefix . self::LS_TBL_CLASSES . ($isUser ? "" : self::LS_TBL_SESSION_SFX);
        // Insert current class. Should get status from correct place, currently setting 0
        if($mergeCurrentClass && $this->session_class != 0)
        {
            $sql = $wpdb->prepare("SELECT * FROM $table_name2 WHERE user=%d AND classid=%d", array($userOrSession, $this->session_class));
            $row = $wpdb->get_row($sql);
            if($row == NULL)
            {
                $wpdb->insert($table_name2,
                        array('user' => $userOrSession, 'classid' => $this->session_class, 'status' => 0, 'member' => 1, 'dbupdate' => $time),
                        array('%d', '%d', '%d', '%d', '%s'));
            }
            else
            {
                $wpdb->update($table_name2,
                        array('member' => 1, 'dbupdate' => $time),
                        array('user' => $userOrSession, 'classid' => $this->session_class),
                        array('%d', '%s'),
                        array('%d', '%d'));
            }
        }
        foreach($rows as $row)
        {
            $sql = $wpdb->prepare("SELECT * FROM $table_name2 WHERE user=%d AND classid=%d", array($userOrSession, $row->classid));
            $row2 = $wpdb->get_row($sql);
            if($row2 == NULL)
            {
                $wpdb->insert($table_name2,
                        array('user' => $userOrSession, 'classid' => $row->classid, 'status' => $row->status, 'member' => 1, 'dbupdate' => $time),
                        array('%d', '%d', '%d', '%d', '%s'));
            }
            else
            {
                $member = $row2->member;
                if($member == 0)
                {
                    $member = $row->member;
                }
                $wpdb->update($table_name2,
                        array('member' => $member, 'dbupdate' => $time),
                        array('user' => $userOrSession, 'classid' => $row->status),
                        array('%d', '%s'),
                        array('%d', '%d'));
            }
        }
    }

    function clear_session($user, $deleteData, $session, $localSession, $isValid, $state = FALSE, $class = 0, $stone = 0)
    {
        
        global $wpdb;
        
        $time = current_time('mysql');
        if($localSession)
        {
            $inputStatus = $this->session_uvinput;
        }
        else
        {
            //get status from db
            $inputStatus = 1;
        }

        if($inputStatus != 0)
        {
            $table_name = $wpdb->prefix . self::LS_TBL_RESPONSES . self::LS_TBL_SESSION_SFX;
            $table_name1 = $wpdb->prefix . self::LS_TBL_INPUTS . self::LS_TBL_SESSION_SFX;

            $sql = $wpdb->prepare("DELETE FROM $table_name WHERE user=%d", array($session));
            $wpdb->query($sql);

            $sql = $wpdb->prepare("DELETE FROM $table_name1 WHERE user=%d", array($session));
            $wpdb->query($sql);

            $table_name2 = $wpdb->prefix . self::LS_TBL_CLASSES . self::LS_TBL_SESSION_SFX;
            if($deleteData)
            {
                $sql = $wpdb->prepare("DELETE FROM $table_name2 WHERE user=%d", array($session));
                $wpdb->query($sql);
            }
            else
            {
                $wpdb->update($table_name2, array('member' => 0, 'dbupdate' => $time), array('user' => $session), array('%d', '%s'), array('%d'));
            }
        }

		$table_name = $wpdb->prefix . self::LS_TBL_SESSION;

        $update_fields = array(
                'user' => $user,
                'dbupdate' => $time,
                'uvinput' => 0,
                'valid' => $isValid
			);

        $update_format = array(
                "%d",
                "%s",
                "%d",
                "%d"
			);

        if($state !== FALSE)
        {
            $update_fields['state'] = $state;
            $update_format[] = "%s";

            $update_fields['classid'] = $class;
            $update_format[] = "%d";

            $update_fields['stone'] = $stone;
            $update_format[] = "%d";
        }

        if(is_user_logged_in() && $localSession)
        {
            global $current_user;
            get_currentuserinfo();
            $update_fields['uvname'] = $current_user->display_name;
            $update_format[] = "%s";            
        }
        else if(!isset($update_fields['classid']))
        {
            $update_fields['classid'] = $class;
            $update_format[] = "%d";
        }

        if($localSession && ($this->session_valid != $isValid))
        {
            $update_fields['time'] = $time;
            $update_format[] = "%s";           
        }

		$wpdb->update(
			$table_name,
			$update_fields,
			array(
				'id' => $session
			),
            $update_format,
			array(
				"%d"
			)
		);

        if($localSession)
        {
            $this->session_uvinput = 0;
            $this->session_valid = $isValid;
        }
    }

    function mark_uv_input($session)
    {
        global $wpdb;

        $this->get_session();

		$table_name = $wpdb->prefix . self::LS_TBL_SESSION;

        $time = current_time('mysql');

		$wpdb->update(
			$table_name,
			array(
                'uvinput' => 1
			),
			array(
				'id' => $session
			),
			array(
                "%d"
			),
			array(
				"%d"
			)
		);
        
        if($session === $this->session_id)
        {
            $this->session_uvinput = 1;
        }
        
    }


	function wp_logout()
	{
        global $wpdb;
        $this->get_session();   
        $this->clear_session(0, FALSE, $this->session_id, TRUE, TRUE);
        if(!isset($_GET['redirect_to']))
        {
            wp_redirect(home_url('?' . self::LS_FLD_LOGGEDOUT . '=true'));
            exit();
        }
	}

	function activation() {
		$this->db_install();

        remove_role('ls_teacher');
        add_role( 'ls_teacher', 'Teacher',	array(
								'publish_lessons' => true,
								'edit_lessons' => true,
								'edit_others_lessons' => true,
								'delete_lessons' => true,
								'delete_others_lessons' => true,
								'read_private_lessons' => true,
								'edit_lesson'=> true,
								'delete_lesson' => true,
								'read_lesson' => true,
							 )
		);

        remove_role('ls_student');
		add_role( 'ls_student', 'Student',	array(
								'publish_lessons' => true,
								'edit_lessons' => true,
								'edit_others_lessons' => true,
								'delete_lessons' => true,
								'delete_others_lessons' => true,
								'read_private_lessons' => true,
								'edit_lesson'=> true,
								'delete_lesson' => true,
								'read_lesson' => true,
							 )
                         );
        flush_rewrite_rules();
	}

	function db_install() {

		global $wpdb;

		$installed_ver = get_option( self::LS_OPT_DB_VERSION );

		if( $installed_ver == FALSE)
		{
			$installed_ver = "0.0";			
			add_option( self::LS_OPT_DB_VERSION, $installed_ver );
		}

		if( $installed_ver != self::LS_DB_VERSION )
		{
	   		$table_name = $wpdb->prefix . self::LS_TBL_SESSION;

			$sql = "CREATE TABLE $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					user bigint(20),
                    classid bigint(20),
                    uvname varchar(50),
                    uvinput tinyint(5),
                    state varchar(50),
                    valid tinyint(5) DEFAULT 1,
                    service mediumint(5) DEFAULT 0,
                    stone bigint(5) DEFAULT 0,
					dbupdate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					UNIQUE KEY id (id),
                    KEY dbupdate_key (dbupdate),
                    KEY user_key (user,classid),
                    KEY classid_key (classid)
				);";
			dbDelta( $sql );

	   		$table_name = $wpdb->prefix . self::LS_TBL_LEARNSTONES;

			$sql = "CREATE TABLE $table_name (
					id bigint(20) NOT NULL AUTO_INCREMENT,
					post bigint(20),
					parent bigint(20),
					name varchar(" . self::LS_MAX_TITLE_SIZE . "),
					lsorder int(5),
					UNIQUE KEY id (id),
					UNIQUE KEY post_key (post,name)
				);";
			dbDelta( $sql );

	   		$table_name = $wpdb->prefix . self::LS_TBL_COURSES;
			$sql = "CREATE TABLE $table_name (
					parent bigint(20),
					child bigint(20),
					corder int(5),
					UNIQUE KEY post_key (parent,corder),
					KEY child_key (child)
				);";
			dbDelta( $sql );

            foreach(array("user" => "", "session" => self::LS_TBL_SESSION_SFX) as $key => $suffix)
            {
	   		    $table_name = $wpdb->prefix . self::LS_TBL_RESPONSES . $suffix;

			    $sql = "CREATE TABLE $table_name (
					    id mediumint(9) NOT NULL AUTO_INCREMENT,
					    post bigint(20),
					    user bigint(20),
					    stone bigint(20),
					    history LONGTEXT,
                        value mediumint(20),
					    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    					dbupdate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					    marked mediumint(20) DEFAULT 0 NOT NULL,
                        UNIQUE KEY id (id),
					    KEY stone_key (post,user,stone),
					    KEY user_key (user,stone),
					    KEY update_key (post,dbupdate)
				    );";
			    dbDelta( $sql );

	   		    $table_name = $wpdb->prefix . self::LS_TBL_CLASSES . $suffix;
			    $sql = "CREATE TABLE $table_name (
					    user bigint(20),
					    classid bigint(20),
					    status int(5),
                        member tinyint(5) DEFAULT 1,
    					dbupdate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					    UNIQUE KEY id (user,classid),
					    KEY class_key (classid),
					    KEY update_key (dbupdate)
				    );";
			    dbDelta( $sql );

	   		    $table_name = $wpdb->prefix . self::LS_TBL_INPUTS . $suffix;
			    $sql = "CREATE TABLE $table_name (
					    id bigint(20) NOT NULL AUTO_INCREMENT,
                        post bigint(20),
					    user bigint(20),
                        field varchar(20),
					    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    					dbupdate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					    history LONGTEXT,
                        value varchar(30000),
					    UNIQUE KEY id (id),
					    KEY user_key (post,user,field),
					    KEY update_key (post,dbupdate)
				    );";
			    dbDelta( $sql );
            }

			update_option( self::LS_OPT_DB_VERSION, self::LS_DB_VERSION );
		}

	}

	function plugins_loaded() {
		if (get_site_option( self::LS_OPT_DB_VERSION ) != self::LS_DB_VERSION) {
			$this->db_install();
		}
	}

	function get_session()
	{
		global $wpdb;
        if($this->session_id == -1)
        {
      		$this->session_id = 0;
            $this->session_class = 0;
            $this->sesssion_uvinput = 0;
            $this->session_name = "";
            $this->session_state = "";
            $this->session_service = 0;
            $this->session_stone = 0;
		    if (isset($_COOKIE[self::LS_COOKIE])) {
			    $this->session_id = $_COOKIE[self::LS_COOKIE];
		    }
    		$table_name = $wpdb->prefix . self::LS_TBL_SESSION;
            $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id=%d", array($this->session_id));
            $session = $wpdb->get_row($sql);

            if($session != NULL)
            {
                $this->session_class = $session->classid;
                $this->session_name = $session->uvname;
                $this->session_uvinput = $session->uvinput;
                $this->session_state = $session->state;
                $this->session_valid = $session->valid;
                $this->session_service = $session->service;
                $this->session_stone = $session->stone;
            }

        	if($this->session_id == 0) {		
	    		$user = 0;
		    	if(is_user_logged_in())
			    {
				    $current_user = wp_get_current_user();
				    $user = $current_user->ID;
			    }
			    $rows_affected = $wpdb->insert( $table_name, 
							array(
								'user' => $user,
                                'uvinput' => 0,
                                'dbupdate' => current_time('mysql')
							),
                            array("%s", "%d", "%s")
					);
                if($rows_affected != FALSE)
			    {
				    $this->session_id = $wpdb->insert_id;
			    }
    		}

            $options = get_option(self::LS_OPT_MAIN);
		    if($options[self::LS_OPT_SESSION_DURATION] == 0)
		    {
			    setcookie(self::LS_COOKIE, $this->session_id, 0);
		    }
		    else
		    {
			    setcookie(self::LS_COOKIE, $this->session_id, time()+$options[self::LS_OPT_SESSION_DURATION]);
		    }
		}
        else
        {
  //                          die('cookiec' . $this->session_id);

        }

	}

    function get_lesson_data()
    {
        global $wpdb;
        if($this->lesson_data === 0)
        {
            $post_id = get_the_ID();
            $this->lesson_data = array();
            $table_name = $wpdb->prefix . self::LS_TBL_INPUTS;
            $suffix = "";
            if(is_user_logged_in())
            {
                $user = get_current_user_id();
            }
            else
            {
                $this->get_session();
                $suffix = self::LS_TBL_SESSION_SFX;
                $user = $this->session_id;
            }

            $sql = $wpdb->prepare("SELECT field,value FROM $table_name$suffix WHERE post=%d AND user=%d", array($post_id,$user));
            $data = $wpdb->get_results($sql);
            foreach($data as $row)
            {
                $this->lesson_data['lesson'][$row->field] = $row->value;
            }

            $table_name = $wpdb->prefix . self::LS_TBL_RESPONSES;
            $sql = $wpdb->prepare("SELECT stone, value FROM $table_name$suffix WHERE post=%d AND user=%d", array($post_id,$user));
            $data = $wpdb->get_results($sql);
            foreach($data as $row)
            {
                $this->lesson_data['responses'][$row->stone] = $row->value;
            }
        }
    }


    function set_lesson_data($post_id, $field, $inputs, $userOrSession, $isUser)
    {
		global $wpdb;

        $table_name = $wpdb->prefix . self::LS_TBL_INPUTS;
        if(!$isUser)
        {
            $table_name .= self::LS_TBL_SESSION_SFX;
        }
        
        $this->mark_uv_input($userOrSession);
        if(strpos($field, 'lsi_') === 0)
        {
            $field = substr($field, 4);
        }

        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE post=%d AND user=%d AND field=%s", array($post_id, $userOrSession, $field));
        $row = $wpdb->get_row($sql);

        $added = FALSE;
        $add = FALSE;
        if($row == NULL)
        {
            $in = $inputs;
            $add = TRUE;
        }
        else
        {
            $in = unserialize($row->history);
            $latest = end($in);
            $time = key($in);
            foreach($inputs as $key => $input)
            {
                // should only add if difference or from earlier session
                if(!(empty($input)))
                {
                    if(($input != $latest) || ($key < $time))
                    {
                        $in[$key] = $input;
                        $added = TRUE;
                        $add = TRUE;
                    }
                }
            }
        }

        if($add)
        {
            ksort($in);
            $lastValue = "";
            $newIn = array();
            foreach($in as $key => $value)
            {
                if($value != $lastValue)
                {
                    $newIn[$key] = $value;
                }
                $lastValue = $value;
            }

            if(count($newIn) > 0)
            {
                if(!$added)
                {
                    $wpdb->insert($table_name,
                                    array('post' => $post_id,
                                            'user' => $userOrSession,
                                            'field'=> $field,
                                            'history' => serialize($newIn),
                                            'value' => end($newIn),
                                            'time' => key($newIn),
                                            'dbupdate' => current_time('mysql')),
                                    array("%d","%d","%s","%s","%s", "%s", "%s"));                
                }
                else
                {
                    $wpdb->update($table_name,
                                    array('history' => serialize($newIn),
                                        'dbupdate' => current_time('mysql'),
                                        'value' => end($newIn),
                                        'time' => key($newIn),
                                        'dbupdate' => current_time('mysql')),
                                    array(
                                        'id' => $row->id
                                    ),
                                    array("%s","%s","%s", "%s", "%s"),
                                    array("%d"));
                }
            }
        }
    }

	function set_learnstone_data($post_id, $learnstone, $response, $marked, $time, $userOrSession, $isUser)
	{
        
		global $wpdb;
		// Set data in database if user is logged in
        $suffix = "";
		if(!$isUser)
		{
            $suffix = self::LS_TBL_SESSION_SFX;
        }
        $this->mark_uv_input($userOrSession);

		$table_name = $wpdb->prefix . self::LS_TBL_RESPONSES . $suffix;
		$t = $time;
		if($t == 0)
		{
			$t = current_time('mysql');
		}

        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE post=%d AND user=%d AND stone=%d", array($post_id, $userOrSession, $learnstone));
        $row = $wpdb->get_row($sql);
        if($row == NULL)
        {
		    $wpdb->insert($table_name,
			    array(
				    'post' => $post_id,
				    'user' => $userOrSession,
				    'stone' => $learnstone,
    			    'time' => $t,
                    'history' => serialize(array($t => $response )),
                    'value' => $response,
                    'dbupdate' => current_time('mysql'),
                    'marked' => $marked
			    ),
			    array(
				    "%d",
				    "%d",
				    "%d",
				    "%s",
				    "%s",
                    "%d",
                    "%s",
                    "%d"
			    )
		    );
        }
        else
        {
            $data = unserialize($row->history);
            $data[$t] = $response;
            ksort($data);
		    $wpdb->update($table_name,
			    array(
    			    'time' => $t,
                    'history' => serialize($data),
                    'value' => end($data),
                    'dbupdate' => current_time('mysql'),
                    'marked' => (key($data) == $t ? $marked : $row->marked)
			    ),
                array(
                    'id' => $row->id
                ),
			    array(
				    "%s",
				    "%s",
				    "%d",
                    "%s"
			    ),
			    array(
				    "%d"
			    )
		    );            
        }
	}

    function save_post( $id, $post )
    {
        global $wpdb;
        global $pagenow;

		if(!wp_is_post_autosave($id)) {
            if( get_post_type() == self::LS_TYPE_LESSON )
            { 
                $confirmed = array();
                if(isset($_POST[self::LS_FLD_CONFIRMED ]))
                {
                    $confirmed = $_POST[self::LS_FLD_CONFIRMED];
                }
                $this->save_post_order( $id, $confirmed );
            }
            else if( get_post_type() == self::LS_TYPE_CLASS )
            {
                $resave = FALSE;
                if(isset($_POST[self::LS_FLD_CLASS_REGEN]))
                {
                    $title = $this->get_new_class_id();
                    $resave = TRUE;
                }
                else if(isset($_POST[self::LS_FLD_NEW_CLASS_NAME]))
                {
                    $title = $_POST[self::LS_FLD_NEW_CLASS_NAME];
                    $resave = TRUE;
                }
                if($resave)
                {
                    remove_action( 'save_post', array( $this, 'save_post' ), 20, 2 );
                    wp_update_post(array('ID' => $id, 'post_name' => strtolower($title)));
                    add_action( 'save_post', array( $this, 'save_post' ), 20, 2 );
                }
            }
            if( get_post_type() == self::LS_TYPE_CLASS || get_post_type() == self::LS_TYPE_COURSE )
            {
                // Should begin transaction here
                $table_name = $wpdb->prefix . self::LS_TBL_COURSES;
                $wpdb->delete($table_name, array('parent' => $id ));
                $index = 0;
                if(isset($_POST['ls_item']))
                {
                    foreach($_POST['ls_item'] as $child)
                    {
                        $wpdb->insert($table_name, array('parent' => $id, 'child' => intval($child), 'corder'=>$index), array('%d', '%d', '%d'));
                        $index++;
                    }
                }
                // Commit here
            }
        }
    }

    function save_post_order( $id, $confirmed )
    {
        global $wpdb;

        $this->shortcode_validation = TRUE;
        $this->shortcode_error = self::LS_STATUS_OK;
        $content = get_post($id);
        do_shortcode($content->post_content);
        $error = $this->shortcode_error;
        if($error == self::LS_STATUS_OK)
        {            
            $ret = $this->get_new_order($id, $confirmed, TRUE);

            $error = $ret[0];
            $neworder = $ret[1];
            $oldlss = $ret[2];
            $titles = $ret[3];

            if($error == self::LS_STATUS_OK)
            {
                $table_name = $wpdb->prefix . self::LS_TBL_LEARNSTONES;
                //Consider starting transaction

                //Delete any ls still in $oldlss
                foreach($oldlss as $key2 => $oldls)
                {
                    $wpdb->delete( $table_name,
                                    array('id' => $oldls->id),
                                    array("%d") );
                }

                //Add all new one         
                foreach($neworder as $key => $value)
                {
                    if($value[0] == -1)
                    {
                        $wpdb->insert($table_name,
				            array(
					            'post' => $id,
					            'parent' => 0,
					            'name' => $titles[$key],
					            'lsorder' => $key
				            ),
				            array(
					            "%d",
					            "%d",
					            "%s",
					            "%d"
				            )
                        );
                    }
                    else
                    {
			            $wpdb->update( $table_name,
					            array(
						            'name' => $titles[$key],
                                    'lsorder' => $key
					            ),
					            array(
						            'id' => $value[0]
					            ),
					            array(
						                "%s",
						                "%d"
					            ),
					            array(
						            "%d"
					            )
                            );
                    }

                    //Consider committing transaction
                }
            }
        }

        if($error != self::LS_STATUS_OK)
        {
            remove_action( 'save_post', array( $this, 'save_post' ), 20, 2 );
            wp_update_post(array('ID' => $id, 'post_status' => 'draft'));
            add_action( 'save_post', array( $this, 'save_post' ), 20, 2 );
        }

        //Save meta data regarding error
        update_post_meta($id, self::LS_CUSTOM_STATUS, $error);
    }

    function get_new_order($id, $confirmed, $get_resolutions)
    {
        global $wpdb;
        $error = self::LS_STATUS_OK;
        $table_name = $wpdb->prefix . self::LS_TBL_LEARNSTONES;
        
            //t1 t1=
            //t2 t3=
            //t3 t5Rt4
            //t4 t6N
            //   t7N
            //   t8Rt4...oh dear!
            
            //Data required: t1=0
            //               t3=0
            //               t5=t4
            //               t6=-1
            //               t7=-1
            //               t8=t4

        $content = get_post($id);
        $neworder = array();
        $oldlss = array();
        $titles = array();

        if($error == self::LS_STATUS_OK)
        {
            $lss = explode(self::LS_SPLITTER, $content->post_content);
            foreach($lss as $key => $value)
            {
                $html = str_get_html($value);
                $found = FALSE;
                // Find first <H1>
                foreach($html->find('h1') as $h1)
                {
                    $titleKey = $h1->plaintext;
                    if(strlen($titleKey) > self::LS_MAX_TITLE_SIZE)
                    {
                        $error = self::LS_STATUS_TITLE_TOO_LONG;
                    }
                    else
                    {
                        foreach($titles as $title)
                        {
                            if(strcasecmp($title, $titleKey) == 0)
                            {
                                $error = self::LS_STATUS_DUPLICATE;
                                break;
                            }
                        }
                    }
                    $titles[] = $titleKey;
                    $found = TRUE;
                    break;
                }
                if(!$found)
                {
                    $error = self::LS_STATUS_NO_TITLE;
                    break;
                }
            }
        }

        if($error == self::LS_STATUS_OK && $get_resolutions)
        {
            $oldlss = $wpdb->get_results("SELECT * FROM $table_name WHERE post=$id ORDER BY lsorder", OBJECT);
            $neworder = array();
            $anyNewtitle = FALSE;
            foreach($titles as $key => $title)
            {
                $found = FALSE;
                $testTitle = $title;
                $newtitle = FALSE;
                if(isset($confirmed[$key]))
                {
                    if($confirmed[$key] == -1)
                    {
                        $newtitle = TRUE;
                    }
                    elseif ($confirmed[$key] !== 0)
                    {
                        $testTitle = $confirmed[$key];
                    }
                }
                if(!$newtitle)
                {
                    foreach($oldlss as $key2 => $oldls)
                    {
                        if(strcasecmp($testTitle, $oldls->name) == 0)
                        {
                            $neworder[] = array($oldls->id, $title);
                            unset($oldlss[$key2]);
                            $found = TRUE;
                            break;
                        }
                    }
                }
                if(!$found)
                {
                    if(strcasecmp($testTitle, $title) != 0)
                    {
                        // Two stones resolved to the same title
                        $error = self::LS_STATUS_BAD_RESOLVE;
                        break;                        
                    }
                    else
                    {
                        $neworder[] = array(-1,$title);
                        $anyNewtitle = TRUE;                
                    }
                }
            }

            if((count($oldlss) > 0) && (count($confirmed) == 0) && $anyNewtitle)
            {
                //Needs resolving
                $error = self::LS_STATUS_RESOLVE;
            }
        }

        return array($error, $neworder, $oldlss, $titles);
    }

	function the_content($content)
	{
		global $wpdb;
		global $post;
		global $wp_query;
        $ret = $content;
		if( is_singular()) {
            if(get_post_type() == self::LS_TYPE_LESSON) {
			    if ($this->dashboard) {
                    $post_id = get_the_ID();
                    $ret = $this->get_new_order($post_id, array(), TRUE);
                    $error = $ret[0];
                    $order = $ret[1];
                    $titles = $ret[3];
                    $user = get_current_user_id();
                    $ret = '<div id="ls_db_wrapper"><form method="post">';
                    $ret .= "<h1>Lesson:" . get_the_title() . "</h1>";
                    //$ret .= "<div id='debug'>debug</div>";
                    $ret .= '<ul id="ls_db_menu"><li><a href="' . esc_url(get_dashboard_url($user)) . '">Home</a></li>';
                    if($error == self::LS_STATUS_OK)
                    {
                        $ret .= '<li><a id="ls_db_view" href="#">Lesson</a></li>';
                        $ret .= '<li><a id="ls_db_stream" href="#">Learnstream</a></li>';
                        $ret .= "<li><a id='ls_change' href='#'>Display</a></li>";
                        $ret .= "<li><a id='ls_manage' href='#'>Management</a></li>";

                        $classes = array();
                        $this->get_classes( get_the_ID(), $classes );
                        
                        $ret .= "<li>Filter: <select id='ls_filter' name='ls_filter'>";
                        $ret .= "<option value='-1'>(Any Class)</option>";
                        $curClassName = "";
                        $curclass = -1;
                        if(count($classes) > 0)
                        {
                            $inClass = "";
                            $conj = "";
                            foreach($classes as $class)
                            {
                                if($class[1])
                                {
                                    $inClass .= $conj . $class[0];
                                    $conj = ",";
                                }
                            }

                            $authorClasses = $wpdb->get_results("SELECT ID,post_name,post_title FROM {$wpdb->posts} WHERE ID IN ($inClass) AND post_status='publish' ORDER BY post_name", OBJECT_K);
                            $curClassName = "";
                            if(isset($_GET[self::LS_FLD_CLASS_NO]))
                            {
                                $curclass = $_GET[self::LS_FLD_CLASS_NO];
                            }
                            foreach($authorClasses as $class)
                            {
                                // Check requested
                                $selected = "";
                                if($curclass == $class->ID)
                                {
                                    $selected = " selected='true' ";
                                    $curClassName = esc_html(strtoupper($class->post_name));
                                }
                                $ret .= "<option value='" . esc_attr($class->ID) . "' $selected>" . esc_html(strtoupper($class->post_name)) . " - " . esc_html($class->post_title) . "</option>";
                            }
                        }
                        $ret .= "</select>";
                        $ret .= get_submit_button(__('Filter'), 'secondary', 'ls_filter_but', FALSE );
                        $ret .= "</li>";
                        $ret .= "</ul>";

                        $dops = "<div id='ls_dops'>";

                        $doptions = get_option(self::LS_OPT_MAIN);
                        $values = array("(None)" => array(self::LS_TOK_INPUT, 0), "(URL)" => array(self::LS_TOK_INPUT , 1));
                        if(isset($doptions[self::LS_OPT_INPUT_DISP]))
                        {
                            foreach($doptions[self::LS_OPT_INPUT_DISP] as $opt )
                            {
                                if(!isset($opt['deleted']) && !empty($opt['name']) && !empty($opt['format']))
                                {
                                    $values[$opt['name']] = array($opt['format'], (isset($opt['url']) ? 1 : 0));                                    
                                }
                            }
                        }
                        ksort($values);

                        // This maps name=>format
                        $slides = explode(self::LS_SPLITTER, $content);
                        $this->shortcode_ls = 1;
                        foreach($slides as $slide)
                        {
                            do_shortcode($slide);
                            $this->shortcode_ls++;
                        }

                        $options = get_user_option(self::LS_OPT_INPUT_DISP_SEL);
                        if(count($this->shortcode_fields) > 0)
                        {
                            $dops .= "<table><tr><td>Field</td><td>Format</td>";

                            foreach($this->shortcode_fields as $fld => $ls)
                            {
                                $selected = "(None)";
                                if($options !== FALSE)
                                {
                                    if(isset($options['l' . get_the_ID()][$fld]))
                                    {
                                        $selected = $options['l' . get_the_ID()][$fld];
                                    }
                                    elseif (isset($options['l0'][$fld]))
                                    {
                                        $selected = $options['l0'][$fld];                                        
                                    }
                                }
                                $dops .= "<tr><td>" . $fld . "</td><td>";
                                $dops .= "<select id='lsf_$fld' name='lsf_$fld'>";
                                $i =0;
                                foreach($values as $key=>$val)
                                {
                                    $dops .= "<option id='lsf_$fld$i' data-format='" . $val[0] . "' data-url='" . $val[1] . "' value='$key' " . (($selected == $key) ? "selected='true'":"") . ">$key</option>";
                                    $i++;
                                }
                                $dops .= "</select>";
                                $dops .= "</tr>";
                            }

                            $dops .= "</table>";
                        }

                        $ret .= $dops;
                        $ret .= "</div>";
                        $ret .= "<div id='ls_man'>";
                        $ret .= "<p>" . get_submit_button( __('Merge'), 'secondary', 'ls_merge', FALSE, array('id' => 'ls_merge') ) . " ticked inputs to latest session or selected user</p>";
                        $ret .= "<p id='ls_p_remove_class'>" . get_submit_button( __('Remove'), 'secondary', 'ls_remove', FALSE, array('id' => 'ls_remove') ) . " ticked users from &quot;<span class='ls_class_name'>" . ($curClassName == "" ? "All my classes" : $curClassName) . "</span>&quot;</p>";
                        $ret .= "<input type='hidden' name='ls_purge_owner' id='ls_purge_owner' value='" . ($post->post_author == get_current_user_id() ? "1" : "0") . "' />";
                        $ret .= "<p id='ls_p_purge'>" . get_submit_button( __('Purge'), 'secondary', 'ls_purge', FALSE, array('id' => 'ls_purge') ) . " ticked users lesson data and remove from &quot;<span class='ls_class_name'>" . ($curClassName == "" ? "All my classes" : $curClassName) . "</span>&quot;</p>";
                        $ret .= "</div>";
              
                        $rows = $this->get_recent_responses(get_the_ID(), $classes);
                        $latest = $rows['latest']['time'];
				        $ret .= "<table id='ls_dashboard'>";
                        $ret .= "<thead><tr data-class='head'><th data-ls='ls-0' class='ls_rotated'><div><span>Bulk Action</span></div></th><th data-ls='ls-0' class='ls_rotated'><div><span>Name</span></div></th><th data-ls='ls-0' class='ls_rotated'><div><span>Last Update</span></div></th>";
                        $norow = "<tr data-class='head'><td></td><td></td><td></td>";
                        $index = 1;
                        foreach($order as $ls)
                        {
                            $norow .= "<td>$index</td>";
                            $inputHead = "";
                            foreach($this->shortcode_fields as $key => $ind)
                            {
                                if($ind == $index)
                                {
                                    $inputHead .= "<th data-input='lsf_$key' class='ls_rotated ls_db_input_hide'><div><span>$key</span></div></th>";
                                    $norow .= "<td class='ls_db_input_hide'></td>";
                                }
                            }
                            $ret .= "<th " . (empty($inputHead) ? "" : "data-hasinput='true' ")  . "data-ls='ls" . $ls[0] . "' class='ls_rotated'><div><span>" . $ls[1] . "</span></div></th>";
                            $ret .= $inputHead;
                            $index++;
                        }
                        $ret .= "</tr></thead>";
                        $ret .= "<tbody>" . $norow . "</tr>";

                        $cnt = 0;
                        $table = array();

                        if(isset($rows['users']))
                        {
                            foreach($rows['users'] as $key => $row)
                            {
                                $name = self::LS_NAME_UNVERIFIED;
                                $cnt++;
                                if(isset($row['name']))
                                {
                                    $name = $row['name'];
                                }
                                $style = "ls_db_show";
                                if(!isset($row['classes'][$curclass]) && $curclass != -1)
                                {
                                    $style = "ls_db_hide";
                                }
                                $bg = 'ls_db_user';
                                if(strpos($key, 'session') === 0)
                                {
                                  $bg = 'ls_db_session';  
                                }
                                // class='' gets replaced later with true class / odd / even
                                $rowst = "<tr id='$key' class='' data-class='|" . implode("|", $row['classes']) . "|'>";
                                $rowst .= "<td class='$bg'><input type='checkbox' id='lsc_$key' name='lsc_$key'></td><td class='ls_db_name'>" . $name . "</td>";
                                $t = "-";
                                if($row['time']  != "-")
                                {
                                    $t = $this->get_formatted_time($row['time']);
                                }
                                $rowst .= "<td data-time='" . $row['time'] . "' class='ls_db_time'>" . $t . "</td>";
                                $index = 1;
                                foreach($order as $ls)
                                {
                                    $res = "0";
                                    if(isset($row['responses']["ls" . $ls[0]]))
                                    {
                                        $res = $row['responses']["ls" . $ls[0]];
                                    }
                                    $inputMark = "&nbsp;";

                                    $rowsuf = "";
                                    // Add current class here: if not in owners class list mark as not active.
                                    $inputFull = -1;
                                    foreach($this->shortcode_fields as $k => $ind)
                                    {
                                        if($index == $ind)
                                        {
                                            if($inputFull == -1)
                                            {
                                                $inputFull = 1;
                                            }

                                            $inp = "";
                                            if(isset($rows['latest']['inputs'][$key . "_" . $k]))
                                            {
                                                $inp = $rows['latest']['inputs'][$key . "_" . $k][2];
                                            }

                                            $pureInp = $inp;
                                            if(!empty($inp))
                                            {
                                                $inp = $this->get_formatted_input($options, $values, $k, $inp);
                                            }
                                            else
                                            {
                                                $inputFull = 0;
                                            }
                                            $rowsuf .= "<td data-input='" . esc_attr($pureInp) . "' class='ls_db_input_hide'>" . $inp . "</td>";
                                        }                                
                                    }
                                    if($inputFull >= 0)
                                    {
                                        $inputMark = "<a class='ls_db_input_a " . ($inputFull == 1 ? 'ls_db_input_a_full' : 'ls_db_input_a_empty') . "'>&#133;</a>";
                                    }
                                    $rowst .= "<td><span id='{$key}ls" . $ls[0] . "' class='ls_respspan ls_resp$res'>$inputMark</span></td>" . $rowsuf;
                                    $index++;
                                }
                                $rowst .= "</tr>";
                                $table[strtolower("a" . $name . $cnt)] = array($rowst, $style);
                            }
                        }

                        ksort($table);

                        $none = TRUE;
                        $rowcount = 1;
                        foreach($table as $row)
                        {
                            $style = $row[1];
                            if($style == "ls_db_show")
                            {
                                $none = FALSE;
                                if(($rowcount % 2) == 1)
                                {
                                    $style .= " ls_db_odd";
                                }
                                else
                                {
                                    $style .= " ls_db_even";                                    
                                }
                                $rowcount++;
                            }
                            $ret .= str_replace("class=''", "class='$style'", $row[0]);
                        }
                        $ret .= "<tr data-class='none' class='" . ($none ? "ls_db_show" : "ls_db_hide") . "'><td class='ls_db_none' colspan='" . (3 + count($order)) . "'>(No Students)</td></tr>";
                        $ret .= "</tbody></table>";

                        // Reverse timesort inputs
                        $timeinputs = array();
                        foreach($rows['latest']['inputs'] as $inp)
                        {
                            $key = $inp[3];
                            if(isset($timeinputs[$key]))
                            {
                                $index = 0;
                                while(isset($timeinputs[$key . $index]))
                                {
                                    $index++;
                                }
                                $key .= $index;
                            }
                            $timeinputs[$key] = array($inp[0], $inp[1], $inp[2], $inp[3]);
                        }
                        krsort($timeinputs);
				        $ret .= "<table id='ls_stream'>";
                        $ret .= "<tr><th>Time</th><th>Name</th><th>Input</th><th>Value</th></tr>";
                        $rowcount = 1;
                        foreach($timeinputs as $key => $inp)
                        {
                            $style = "ls_db_show";
                            $row = $rows['users'][$inp[0]];
                            
                            if(!isset($row['classes'][$curclass]) && $curclass != -1)
                            {
                                $style = "ls_db_hide";
                            }
                            else
                            {
                                if(($rowcount % 2) == 1)
                                {
                                    $style .= " ls_db_odd";
                                }
                                else
                                {
                                    $style .= " ls_db_even";                                    
                                }
                                $rowcount++;
                            }
                            $ret .= "<tr class='$style' data-user='" . $inp[0] . "' data-iname='lsf_" . $inp[1] . "' >";
                            $ret .= "<td data-time='" . $inp[3] . "' >" . $this->get_formatted_time($inp[3]) . "</td>";
                            $name = self::LS_NAME_UNVERIFIED;
                            if(isset($row['name']))
                            {
                                $name = $row['name'];
                            }
                            $ret .= "<td>" . $name . "</td>";
                            $ret .= "<td>" . $inp[1] . "</td>";
                            $ret .= "<td data-input='" . esc_attr($inp[2]) . "'>" . $this->get_formatted_input($options, $values, $inp[1], $inp[2]) . "</td>";
                            $ret .= "</tr>";
                        }
                        $ret .= "</table>";
                        $ret .= "<input id='ls_dashboard_time' type='hidden' value='$latest' />";
                    }
                    else
                    {
                       $ret .= "</ul>";
                       $ret .= "<p>This lesson has errors</p>";                   
                    }
                    $ret .= "</form></div>";
			    }
			    elseif($this->pdf === TRUE) {
                    if($error == self::LS_STATUS_OK && is_plugin_active(TCPDF_PLUGIN))
                    {
                        ob_clean();
                        $author = get_the_author_meta('display_name', get_post_field('post_author', get_the_ID()));
                        $pdf = new LearnstonesPDF(get_the_title(), $author, get_permalink());
                        
                        $pdf->add_css(plugins_url('css/ls_pdf.css', __FILE__));
    		            $slides = explode(self::LS_SPLITTER, $content);
                        $pdfopts = get_option(self::LS_OPT_PDF);
                        $pdflights = isset($pdfopts[self::LS_OPT_PDF_LIGHTS]) ? $pdfopts[self::LS_OPT_PDF_LIGHTS] : self::LS_PDF_LIGHTS_DEFAULT;
    		            foreach($slides as $key => $value) {
                            $value = do_shortcode($value);
                            $pdf->add_slide($value, $pdflights);
                        }
                        $pdf->output(get_the_title());
                        die();
                        
                    }
                }
                else
			    {
				    $postId = get_the_ID();
                    $error = $this->get_lesson_error();
                    if($error == self::LS_STATUS_OK)
                    {
                        $this->get_lesson_data();
                        $this->shortcode_validation = FALSE;
                        $opts = get_option(self::LS_OPT_MAIN);
                        $this->lights = $this->LS_LIGHTS_DEFAULT;
                        if(isset($opts[self::LS_OPT_LIGHTS]))
                        {
                            $this->lights = array_merge($this->lights,$opts[self::LS_OPT_LIGHTS]);
                        }
                        $ret = "<div id='ls_presentation'>";
                        $ret .= "<div id='ls_presentation_w'>Slide</div>";
                        $ret .= "</div>";
				        $ret .= '<a id="openGallery" href="#">View Lesson</a> or ';
                        $url = "";
                        if(is_user_logged_in())
                        {
                            $url = esc_url(get_dashboard_url(get_current_user_id()));
                        }
                        else
                        {
                            $url = home_url();
                        }
                        $ret .= 'return to <a href="' . esc_attr($url) . '">Dashboard</a>.';
 				        $ret .=	'<div id="ls_slides" style="display:none" >';
				        $first = true;
				        $classes = explode(" ", self::LS_STYLES);
				        $slides = explode(self::LS_SPLITTER, $content);
				       // $slides[] = "<h1>Submission</h1>Name/Email:<input type=\"text\" /><a onclick=\"jQuery.ls.submission('GotText' ); return false;\" href=\"#\">Submit</a>";
				        // This is the start of a non-js link, just for ref: $link = admin_url('admin-ajax.php?action=ls_submission&post_id='.$postId.'&nonce='.$nonce);
                
                        $table_name = $wpdb->prefix . self::LS_TBL_LEARNSTONES;
                        $lss = $wpdb->get_results("SELECT id FROM $table_name WHERE post=$postId ORDER BY lsorder", OBJECT);

				        foreach($slides as $key => $value) {
					        if($first) { 
						        $ret .= '<div class="ls_menu">';
						        $ret .=     '<div class="ls_stones"><table><tr>';
                                $caps = "";
						        for ($i = 0; $i < count($slides); $i++)
						        {
                                    $class = "ls_menu0";
                                    if(isset($lss[$i]) && isset($this->lesson_data['responses'][$lss[$i]->id]))
                                    {
							            $resp = $this->lesson_data['responses'][$lss[$i]->id];
	                                    if($resp !== FALSE)
							            {
								            $class=$classes[$resp];
							            }
                                    }
							        $ret .=	    '<td><input type="submit" data-menu="ls_menu' . $i . '" class="ls_menuimg ' . $class . ' " value="' . ($i + 1) . '" name="ls_menu' . $i . '" /></td>';
						            $caps .= '<td  class="ls_rotated"><div><input type="submit" class="ls_menu_input ls_menu_input_ls ls_menu_input_align" value="dummy" name="ls_menu' . $i . '" data-menu="ls_menu' . $i . 'item" /></div></td>';
                                }
						        $ret .= 	'</tr>';
						        $ret .= 	"<tr>$caps</tr></table></div>";
                                $classLogin = "ls_loginmenuhide";
                                $classLogout = "ls_loginmenushow";
                                $name = "";
                                if(!is_user_logged_in())
                                {
                                    $classLogin = "ls_loginmenushow";
                                    $classLogout = "ls_loginmenuhide";
                                }
                                else
                                {
                                    $user = wp_get_current_user();
                                    $name = $user->display_name;
                                }

                                $classOutput = "";
                                if($this->session_class != 0)
                                {
                                    $classOutput = get_the_title($this->session_class);
                                    if(!empty($classOutput))
                                    {
                                        $classOutput = "<li class='ls_loginmenu'>Class: <input type='submit' class='ls_menu_input' value='" . esc_attr($classOutput) . "' name='" . self::LS_FLD_VIEW_CLASS . "' /></li>";
                                    }
                                    $classOutput .= "<li><input type='submit' class='ls_menu_input' value='Change Class' name='" . self::LS_FLD_CHANGE_CLASS . "' /></li>";
                                }
                                else
                                {
                                    $classOutput .= "<li><input type='submit' class='ls_menu_input' value='Join Class' name='" . self::LS_FLD_CHANGE_CLASS . "' /></li>";
                                }
                                $ret .=     "<ul class='ls_loginmenu'>";
                                $ret .=         "<li class='ls_title'>Lesson:" . get_the_title() . "</li>";
                                $ret .= $classOutput;
                                $ret .=         "<li class='$classLogin'><input type='hidden' name='post_id' value='" . get_the_ID() . "' /><input type='hidden' name='" . self::LS_FLD_STONE . "' value='' /><input name='ls_username' class='ls_username ls_ph' type='text' placeholder='" . __('Learnstones User') . "' value='" . esc_attr($this->session_name) . "'/><div class='ls_loginphide'>Please login to keep your results</div>&nbsp;<input name='ls_password' class='ls_password ls_ph' type='password' placeholder='" . __('Learnstones Password') . "'/>&nbsp;";
                                $ret .= get_submit_button( __('Login'), 'secondary', 'ls_login', FALSE, array('id' => 'ls_login') ) . "</li>";
                                $options = get_option(self::LS_OPT_GOOGLE);
                                if(isset($options[self::LS_OPT_GOOGLE_ENABLE]) && !empty($options[self::LS_OPT_GOOGLE_ENABLE]) && !empty($options[self::LS_OPT_GOOGLE_CLIENT_ID])) {
                                    $ret .= "<li class='$classLogin'><input type='submit' class='ls_glogins' name='" . self::LS_FLD_GOOGLE_LI . "' class='ls_glogins' value='' /></li>";
                                }
                                $ret .=         "<li class='$classLogout'>" . __('Welcome,') . " <span class='ls_loginname'>$name</span></li><li class='$classLogout'><input type='submit' class='ls_menu_input' name='" . self::LS_FLD_LOGOUT . "' value='Log Out' /></li>";
                                $ret .=     "</ul>";
						        $ret .= '</div>';
					        }
					        $ret .= '<div class="ls_slideshow" ';
					        if($first) {
						        $ret .= 'rel="gallery"';
					        }
					        $ret .= ' >';
                            $ret .= '<div class="ls_body" data-menu="ls_menu' . $key . '"><div>' . $value . "</div>";

                            $ret .= '[' . self::LS_SC_SYS_LIGHTS . ']';
            			    $ret .= '</div>';
					        $ret .= '</div>';
					        if($first) {
						        $first = false;
					        }
				        }
				        $ret .=	'</div>';
                    }
                    else
                    {
                        $ret = "This lesson has errors";                   
                    }
			    }
            }
            elseif((get_post_type() == self::LS_TYPE_CLASS) || (get_post_type() == self::LS_TYPE_COURSE))
            {
                $current_user = wp_get_current_user();
                $ret = $content . "<h1 class='entry-title'>Content</h1>" . $this->get_course_list(self::LS_MODE_CLASS_VIEW, get_the_ID(), $current_user->ID == get_the_author_meta('ID'));
                if(get_post_type() == self::LS_TYPE_CLASS)
                {
                    if(is_user_logged_in() && $current_user->ID == get_the_author_meta('ID'))
                    {
                        $ret .= "<h1 class='entry-title'>Participants</h1>";
                        $ret .= $this->get_students_list(get_the_ID(), TRUE);
                    }                    
                }
            }
		}
		return $ret;
	}

    function get_students_list( $class, $title )
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::LS_TBL_CLASSES;
        $sql = $wpdb->prepare("SELECT user, display_name FROM $table_name LEFT JOIN {$wpdb->users} ON {$wpdb->users}.ID = $table_name.user WHERE $table_name.classid=%d AND member=1", $class);
        $users = $wpdb->get_results($sql, OBJECT);
        $ret = "<table>";
        if($title)
        {
            $ret .= "<tr><th>Student</th></tr>";
        }
        $ret .= "<tr>";
        $added = FALSE;
        foreach($users as $cuser)
        {
            $added = TRUE;
            $ret .= "<tr><td>{$cuser->display_name}</td></tr>";
        }

        $table_name = $wpdb->prefix . self::LS_TBL_CLASSES . self::LS_TBL_SESSION_SFX;
        $table_name1 = $wpdb->prefix . self::LS_TBL_SESSION;
        $sql = $wpdb->prepare("SELECT id,uvname FROM $table_name LEFT JOIN $table_name1 ON $table_name1.id = $table_name.user WHERE $table_name.classid=%d AND member=1", $class);
        $users = $wpdb->get_results($sql, OBJECT);
        foreach($users as $cuser)
        {
            $added = TRUE;
            if(empty($user->uvname))
            {
                $ret .= "<tr><td>" . self::LS_NAME_UNVERIFIED . "</td></tr>";                
            }
            else
            {
                $ret .= "<tr><td>{$cuser->uvname}</td></tr>";
            }
        }


        if(!$added)
        {
            $ret .= "<tr><td>(None)</td></tr>";
        }
        $ret .= "</table>";
        return $ret;
    }

    function shortcode_input( $atts )
    {
        $name = "";
        $ret = "";
        $val = "";
        if(isset($atts['name']) && !empty($atts['name']))
        {
            $name = esc_attr($atts['name']);
            if(isset($this->lesson_data['lesson'][$atts['name']]))
            {
                $val = $this->lesson_data['lesson'][$atts['name']];   
            }
        }
        elseif($this->shortcode_validation)
        {
            $this->shortcode_error = 0;
        }
        if(!empty($name))
        {
            $type = 'text';
            if(isset($atts['type']))
            {
                $type = $atts['type'];
            }
            if($type === "textarea")
            {
                if($this->pdf)
                {
                    $ret = "__________________<br />";
                    $ret .= "__________________<br />";
                    $ret .= "__________________<br />";
                }
                else
                {
                    $rows = 5;
                    $cols = 40;
                    if(isset($atts['rows']))
                    {
                        $rows = $atts['rows'];                        
                    }
                    if(isset($atts['cols']))
                    {
                        $cols = $atts['cols'];
                    }
                    if(isset($atts['answer']))
                    {
                        $this->auto_answers["lsi_" . $name] = $atts['answer'];
                    }
                    $ret = "<textarea cols='" . esc_attr($cols) . "' rows='" . esc_attr($rows) . "' name='lsi_$name'>" . esc_html($val) . "</textarea>";
                }
            }
            elseif ($type === "radio" || $type === "checkbox")
            {
                $ret = "";
                $random = TRUE;
                if(isset($atts['random']) && $atts['random'] == "false")
                {
                    $random = FALSE;
                }
                $opts = explode(",", $atts['options']);
                if($random)
                {
                    shuffle($opts);
                }
                if($this->pdf)
                {
                    foreach($opts as $opt)
                    {
                        $ret .= "__ " . $opt + "<br />";
                    }
                }
                else
                {
                    $index = 0;
                    $vals = explode(",", $val);
                    $answers = "";
                    $conj = "";
                    $newopts = array();
                    foreach($opts as $opt)
                    {
                        if(substr_compare($opt, "+", strlen($opt) - 1, 1) === 0)
                        {
                            $opt = substr($opt, 0, strlen($opt) - 1);
                            $answers .= $conj . $opt;
                            $conj = ",";
                        }
                        $newopts[] = $opt;
                    }
                    foreach($newopts as $opt)
                    {
                        $selected = "";
                        $inputName = "lsi_" . $name;
                        $inputGroup = $inputName;
                        if($type == 'checkbox')
                        {
                            $inputName .= $opt;
                        }
                        else
                        {
                            $inputGroup .= "_$index";
                        }
                        if(in_array($opt, $vals))
                        {
                            $selected = " checked='checked'";
                        }
                        $ret .= "<input $selected type='$type' name='$inputName' data-$type-id='$inputGroup' value='" . esc_attr($opt) . "'/>&nbsp;<label>" . esc_html($opt) . "</label><br />";
                        $index++;
                    }
                    if(!empty($answers))
                    {
                        $this->auto_answers["lsi_" . $name] = $answers;
                    }
                }
            }
            else
            {
                if($this->pdf)
                {
                    $ret = "__________________ ";
                }
                else
                {
                    if(isset($atts['answer']))
                    {
                        $this->auto_answers["lsi_" . $name] = $atts['answer'];
                    }
                    $ret = "<input type='text' value='" . esc_attr($val) . "' name='lsi_$name' />";
                }
            }
            $this->shortcode_fields[$name] = $this->shortcode_ls;
            if(count($this->auto_answers))
            {
                $this->lights = $this->LS_LIGHTS_DEFAULT_AUTOMARK;
            }
        }
        return $ret;   
    }

    function shortcode_classid( $atts )
    {
        global $wpdb;
        $url = $this->get_current_url();
        if(is_user_logged_in())
        {
            $url = get_home_url();
        }
        $ret = "<form method='post' action='" . $url . "'><ul class='ls_classid'><li>";
        $class = "";
        if(isset($_POST[self::LS_FLD_CLASS]))
        {
            $class = esc_attr($_POST[self::LS_FLD_CLASS]);
        }
        else
        {
            $this->get_session();
            if($this->session_class != 0)
            {
                $p = get_post($this->session_class);
                if($p != NULL)
                {
                    $class = strtoupper($p->post_name);                    
                }
            }
        }
        if(!$this->session_valid)
        {
            $ret .= "Your session has been cleared. Log in or rejoin class</li><li>";
        }
        $ret .= "Class Id:<input name='" . self::LS_FLD_CLASS . "' type='text' value='$class' >";
        if(!$this->valid_class)
        {
            $ret .= "</li><li>Class not recognised";
        }
        if(is_user_logged_in())
        {
            if(!isset($atts['widget']))
            {
                $current_user = wp_get_current_user();
                $ret .= "</li><li>Currently logged on as " . esc_html($this->session_name) . ". <a href=" . esc_attr(wp_logout_url(wp_login_url() . "?" . self::LS_FLD_LOGGEDOUT . "=true")) . ">Not me!</a>"; 
            }
        }
        else
        {
            $ret .= "</li><li>Name:<input name='" . self::LS_FLD_UVNAME . "' type='text' value='" . $this->session_name . "'>";
        }
        
        if(!$this->session_valid)
        {
            $ret.= "<input id='" . self::LS_FLD_CLEAR . "' name='" . self::LS_FLD_CLEAR . "' type='hidden' value='on'>";
        }
        elseif (!is_user_logged_in() && $this->session_uvinput == 1)
        {
            $ret .= "</li><li><label for='" . self::LS_FLD_CLEAR . "''>Clear session data created by ";
            if(empty($this->session_name))
            {
                $ret .= " an unknown user";
            }
            else
            {
                $ret .= "a user called " . $this->session_name;
            }
            $ret .= ":</label><input id='" . self::LS_FLD_CLEAR . "' name='" . self::LS_FLD_CLEAR . "' type='checkbox'>";
        }
        
        $ret .= "</li><li>" . get_submit_button(__('Join'), 'secondary', 'ls_class_but', FALSE);
        if(!is_user_logged_in())
        {
            $ret .= "</li><li>Learnstones <a href='" . wp_login_url() . "'>Log in</a>";
            $options = get_option(self::LS_OPT_GOOGLE);
            if(isset($options[self::LS_OPT_GOOGLE_ENABLE]) && !empty($options[self::LS_OPT_GOOGLE_ENABLE]) && !empty($options[self::LS_OPT_GOOGLE_CLIENT_ID]))
            { 
                if(isset($_GET[self::LS_FLD_LOGGEDOUT]) && $this->session_service != self::LS_SVC_WORDPRESS)
                {
                    $ret .= "</li><li>Don't forget to <a href='http://www.google.com/accounts/Logout'>log out</a> from Google.";
                }
                else
                {
                    $ret .= "</li><li><input type='submit'  name='" . self::LS_FLD_GOOGLE_LI . "'   class='ls_glogin'  value='' />";
                }
            }
        }
        $ret .= "</li>";
        $ret .= "</ul></form>";
        return $ret;
    }

    function shortcode_system_lights( $atts )
    {
        if($this->shortcode_validation)
        {
            $this->shortcode_error = self::LS_STATUS_INVALID_SC;
            return "";
        }
        else
        {
			$ret = 	   '<ul class="ls_lights">';
            foreach($this->lights as $lightNo => $caption)
            {
                $lclasses = esc_attr("ls_lightsspan ls_lights$lightNo");
                $aclasses = esc_attr("ls_lightsa ls_lightsa$lightNo");
				$ret .= 		"<li><input type='submit' class='$lclasses' name='$lightNo' value=''/><input type='submit' value='" . esc_attr(__($caption)) . "' class='$aclasses' name='$lightNo' /></li>";
			}
			$ret .= 	    '</ul>';
            return $ret;
        }
    }

    function shortcode_lights( $atts )
    {
        $classes = explode(" ", self::LS_STYLES);
        //$defs = array();
        //$ls = shortcode_atts($defs, $atts);
        $vls = array();
        //die($atts['l1']);
        foreach($atts as $lk => $lv)
        {
            if(!empty($lv))
            {
                $vls[$lk] = $lv;
            }
        }
        if(count($vls) > 0)
        {
            $this->lights = $vls;
        }

        if($this->shortcode_validation)
        {
            //Check all atts are format l* where *>=1 <= menustyles
        }
        return "";
    }

    function get_class_id( $name )
    {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_name=%s AND post_type=%s", array(strtolower($name), self::LS_TYPE_CLASS));
        $class = $wpdb->get_var($sql);
        return $class;
    }

 
    function get_new_class_id( )
    {

        $val = self::LS_CLS_CODE_SIZE;
        $options = get_option(self::LS_OPT_MAIN);
        if(isset($options[self::LS_OPT_CLASSID_SIZE]))
        {
            $val = $options[self::LS_OPT_CLASSID_SIZE];
        }

        $lets = self::LS_CLS_CHARS;
        if(isset($options[self::LS_OPT_CLASSID_CHARS]))
        {
           $lets = $options[self::LS_OPT_CLASSID_CHARS];
        }

        $title = "";
        do {
            $title = "";
            for($i = 0; $i < $val; $i++)
            {
                $letter = rand(0, strlen($lets) - 1);
                $title .= $lets[$letter]; 
            }
        } while($this->get_class_id($title));
        return $title;
    }

    function get_classes( $id, &$current )
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::LS_TBL_COURSES;
        $sql = $wpdb->prepare("SELECT parent,child,corder FROM $table_name WHERE child=%d ", $id);
        $items = $wpdb->get_results($sql, OBJECT);
        $user_id = get_current_user_id();
        foreach($items as $item)
        {
            if(get_post_type($item->parent) == self::LS_TYPE_CLASS)
            {
                //need to check current user is author of class too
                $current[] = array($item->parent, get_post_field('post_author', $item->parent) == $user_id);
            }
            else
            {
                $this->get_classes( $item->parent, $current );
            }
        }
    }

    function get_lessons( $id, $level, &$current, $maxdepth )
    {
        global $wpdb;

        foreach($current as $lesson)
        {
            if($lesson['id'] == $id)
            {
                return $current;
            }
        }

        $sql = $wpdb->prepare("SELECT post_title,post_type FROM {$wpdb->posts} WHERE ID=%d", $id);
        $parent_item = $wpdb->get_row($sql, OBJECT);
        if($parent_item->post_type == self::LS_TYPE_LESSON)
        {
            $current[] = array('id' => $id, 'title' => $parent_item->post_title, 'level' => $level, 'type' => self::LS_TYPE_LESSON );
        }
        else
        {
            $table_name = $wpdb->prefix . self::LS_TBL_COURSES;
            $sql = $wpdb->prepare("SELECT parent,child,corder FROM $table_name WHERE parent=%d ORDER BY corder", $id);
            $items = $wpdb->get_results($sql, OBJECT);
            if ($parent_item->post_type == self::LS_TYPE_CLASS || $level == 0)
            {
                foreach($items as $item)
                {
                    $this->get_lessons( $item->child, $level + 1, $current, $maxdepth );
                }
            }
            elseif ($parent_item->post_type == self::LS_TYPE_COURSE)
            {
                if($level > 0)
                {
                    $current[] = array('id' => $id, 'title' => $parent_item->post_title, 'level' => $level, 'type' => self::LS_TYPE_COURSE );
                }
                if($level < $maxdepth)
                {
                    foreach($items as $item)
                    {
                        $this->get_lessons( $item->child, $level + 1, $current, $maxdepth );
                    }
                }
             }
        }

        return $current;
    }

    function get_course_list( $mode, $id, $admin )
    {
        ob_start();
        $this->course_list( $mode, $id, $admin );
        $ret = ob_get_clean();
        return $ret;
    }


    function course_list( $mode, $id, $admin )
    {
        $list = array();
        $maxdepth = 10000;
        $opts = FALSE;
        $class = "";
        global $post;
        if($mode == self::LS_MODE_CLASS_EDIT || $mode == self::LS_MODE_COURSE_EDIT)
        {
            $maxdepth = 1;
            $opts = TRUE;
            $class = "_edit";
        }
        $this->get_lessons( $id, 0, $list, $maxdepth );
        $last_lev = 1;
        echo("<ul>");
        if(count($list) > 0)
        {
            foreach($list as $item)
            {
                while($last_lev > $item['level'])
                {
                    echo("</li></ul>");
                    $last_lev--;   
                }
                if($item['type'] == self::LS_TYPE_COURSE)
                {
                    echo("<li class='ls_course_item$class'>");
                    if($opts)
                    {
                        echo("<input name='ls_item[]' type='hidden' value='" . $item['id'] . "' /><input name='ls_selection' type='radio' value='" . $item['id'] . "' checked='checked' />");
                    }
                    echo($item['title'] . "<ul>");
                    $last_lev++;   
                }
                else
                {
                    echo("<li  class='ls_lesson_item$class'>");
                    if($opts)
                    {                        
                        echo("<input name='ls_item[]' type='hidden' value='" . $item['id'] . "' /><input name='ls_selection' type='radio' value='" . $item['id'] . "' checked='checked' />");
                        echo($item['title'] . "</li>");
                    }
                    else
                    {
                        $urlExtra = array();
                        if($mode == self::LS_MODE_CLASS_VIEW && $admin)
                        {
                            $urlExtra = array(self::LS_FLD_DASH => "1", self::LS_FLD_CLASS_NO => $id);
                        }
                        echo("<a href='" . add_query_arg($urlExtra, get_permalink($item['id'])) . "'>");
                        echo($item['title'] . "</a></li>");
                    }
                }
            }
            while($last_lev >=0 )
            {
                //need to add </li> if course with items
                echo("</ul>");
                $last_lev--;   
            }
        }
        echo("</ul>");
    }

    function get_recent_responses($post_id, $classes = NULL, $from = '', $userOrSession = 0, $isUser = FALSE)
    {
        global $wpdb;
        if($classes == NULL)
        {
            $classes = array();
            $this->get_classes( $post_id, $classes );
        }

        $lessonOwner = (get_post_field("post_author", $post_id ) == get_current_user_id());

        $inClass = "";
        $otherClass = "";
        $conj = "";
        $conj2 = "";
        foreach($classes as $class)
        {
            if($class[1])
            {
                $inClass .= $conj . $class[0];
                $conj = ",";
            }
            else
            {
                $otherClass .= $conj2 . $class[0];
                $conj2 = ",";
            }
        }


        //Inputs
        $inputs = array();

        $latest = $from;
        if(empty($latest))
        {
            $latest = current_time('mysql');
        }

        $rows = array();

        $where = "";
        $tables = array("session" => self::LS_TBL_SESSION_SFX, "user" => "");
        if($userOrSession != 0)
        {
            if(!$isUser && $key="session")
            {
                $where = " AND user=%d";
                $tables = array("session" => self::LS_TBL_SESSION_SFX);
            }
            elseif ($isUser && $key="user")
            {
                $where = " AND user=%d";
                $tables = array("user" => "");
            }
        }
        
        $userInClass = array();
        $classSearch = array();
        if(!empty($inClass))
        {
            $classSearch[] = array($inClass, 1, 4);
        }
        if(!empty($otherClass))
        {
            $classSearch[] = array($otherClass, 2, 0);            
        }
        //$rows['debug'] = '@';
        foreach($tables as $key=>$suffix)
        {
           /*
            * classes
            */
            foreach($classSearch as $cs)
            {
                $table_name = $wpdb->prefix . self::LS_TBL_CLASSES . $suffix;
                $sql3 = "SELECT * FROM $table_name WHERE classid IN (" . $cs[0] . ")" . $where;
                if(!empty($where))
                {
                    $sql3 = $wpdb->prepare($sql3, array($userOrSession));
                }
                $classes = $wpdb->get_results($sql3);
                foreach($classes as $class)
                {
                    $user_key = $key . $class->user;
                    if($class->dbupdate > $latest)
                    {
                        $latest = $class->dbupdate;
                    }
                    if(!isset($userInClass[$user_key]) || ($userInClass[$user_key][0] == 4))
                    {
                        $time = $class->dbupdate;
                        if(isset($userInClass[$user_key]) && ($userInClass[$user_key][4] > $time))
                        {
                            $time = $userInClass[$user_key][4];
                        }
                        if($class->member)
                        {
                            $userInClass[$user_key] = array($cs[1], '--', $class->user, array(), $time);
                            if($cs[1] === 1)
                            {
                                $userInClass[$user_key][3][] = $class->classid;
                            }
                        }
                        else
                        {
                            //$rows['debug'] .= "T:$user_key,$time";
                            $userInClass[$user_key] = array($cs[2], '--', $class->user, array(), $time);                            
                        }
                    }
                    else if($cs[1] === 1 && $class->member)
                    {
                        $userInClass[$user_key][3][] = $class->classid;
                        if($class->dbupdate > $userInClass[$user_key][4])
                        {
                            $userInClass[$user_key][4] = $class->dbupdate;
                        }
                    }
                 }
            }
            /*
            * Responses
            */
            $table_name = $wpdb->prefix . self::LS_TBL_RESPONSES . $suffix;
            $sql2 = "SELECT * FROM $table_name WHERE post=%d" . $where;
            
            $args = array($post_id);
            $users = array();
            if(!empty($where))
            {
                $args[] = $userOrSession;
            }
            if(!empty($from))
            {
                $args[] = $from;
                $sql2 .= " AND dbupdate>%s";
            }
            $sql2 .= " ORDER BY user, stone, time";
            $sql2 = $wpdb->prepare($sql2, $args);
 		    $data = $wpdb->get_results($sql2);
            foreach($data as $row)
            {
                $user_key = $key . $row->user;
                if($row->dbupdate > $latest)
                {
                    $latest = $row->dbupdate;
                }
                if(!isset($userInClass[$user_key]))
                {
                    $userInClass[$user_key] = array(0, '--', $row->user, array(), $row->time);                            
                }
                if(($userInClass[$user_key][0] === 1) || ($lessonOwner && ($userInClass[$user_key][0] === 0 || $userInClass[$user_key][0] === 4)))
                {
                    if($row->time > $userInClass[$user_key][1])
                    { 
                        $userInClass[$user_key][1] = $row->time;
                    }
                    $rows['users'][$user_key]['responses']["ls" . $row->stone] = $row->value;
                }
            }


            /*
            * Inputs
            */

            $table_name = $wpdb->prefix . self::LS_TBL_INPUTS . $suffix;
            $sql3 = "SELECT * FROM $table_name WHERE post=%d" . $where;

            $args = array($post_id);
            if(!empty($where))
            {
                $args[] = $userOrSession;
            }
            
            if(!empty($from))
            {
                $sql3 .= " AND dbupdate>%s";
                $args[] = $from;
            }

            $sql3 = $wpdb->prepare($sql3, $args);

 		    $data = $wpdb->get_results($sql3);
            foreach($data as $row)
            {
                $user_key = $key . $row->user;
                if($row->dbupdate > $latest)
                {
                    $latest = $row->dbupdate;
                }
                if(!isset($userInClass[$user_key]))
                {
                    $userInClass[$user_key] = array(0, '--', $row->user, array(), $row->time);                            
                }
                if(($userInClass[$user_key][0] === 1) || ($lessonOwner && ($userInClass[$user_key][0] === 0 || $userInClass[$user_key][0] === 4)))
                {
                    if($row->time > $userInClass[$user_key][1])
                    {
                        $userInClass[$user_key][1] = $row->time;
                    }
                    $fld = $row->field;
                    $inputs[$user_key . "_" . $fld] = array($user_key, $fld, $row->value, $row->time);
                }
            }
       }

       /*
        * Sessions
        */
        $table_name = $wpdb->prefix . self::LS_TBL_SESSION;
        if(!empty($from))
        {
            $sql = $wpdb->prepare("SELECT id,time,dbupdate,user,uvname,valid,uvinput FROM $table_name WHERE dbupdate>%s", array($from));
            $sessions = $wpdb->get_results($sql);
            foreach($sessions as $session)
            {
                if($session->dbupdate > $latest)
                {
                    $latest = $session->dbupdate;
                }
                $user_key = ($session->user == 0 ? 'session' . $session->id : 'user' . $session->user);
                if(!isset($rows['users'][$user_key]['session']))
                {
                    if(!$session->valid)
                    {
                        $rows['users'][$user_key]['session'] = 'deleted';
                        $rows['users'][$user_key]['name'] = '';
                        $rows['users'][$user_key]['time'] = '-';
                        $rows['users'][$user_key]['classes'] = array();
                            
                    }
                    elseif (isset($userInClass[$user_key]))
                    {
                //$rows['debug'] .= "SCHEK:$user_key-(" . implode('*', $userInClass[$user_key][3]) . ")|";
                        // Here we only want to add sessions/users to global where there is actual input
                        if  (($userInClass[$user_key][0] === 1) || ($lessonOwner && ($userInClass[$user_key][0] === 0) && $session->uvinput))
                        {
                            //$rows['debug'] .= "IN HERE:$user_key-" . implode('*' , $userInClass[$user_key][3]);
                            $rows['users'][$user_key]['session'] = 'session' . $session->id;
                            if($session->uvname == NULL)
                            {
                                if($session->user == 0)
                                {
                                    $rows['users'][$user_key]['name'] = self::LS_NAME_UNVERIFIED;
                                }
                                else
                                {
                                    $u = get_userdata($session->user);
                                    $rows['users'][$user_key]['name'] = $u->display_name;
                                }
                            }
                            else
                            {
                                $rows['users'][$user_key]['name'] = $session->uvname;
                            }
                            $rows['users'][$user_key]['time'] = $userInClass[$user_key][1];
                            $rows['users'][$user_key]['classes'] = $userInClass[$user_key][3];   
                        }                         
                    }
                }
            }
        }

        foreach($userInClass as $user_key => $data)
        {   
            //$rows['debug'] .= "IN HERE 3:$user_key,$from," . $userInClass[$user_key][4] . "," . $data[1] . "," . $from . ",". (isset($rows['users'][$user_key]['session'])?"Y//":"N//");
            if(!isset($rows['users'][$user_key]['session']))
            {
                //echo('no session:' . $user_key);
                if($data[1] == '--' && !empty($from) && ($userInClass[$user_key][4] > $from) && $data[0] != 1)
                {
                    //If I was removed from class, I could will still be
                    //in global class if I am not in another class!
                                //$rows['debug'] .= "IN HERE 2:$user_key";
                    $rows['users'][$user_key]['session'] = 'deleted';
                    $rows['users'][$user_key]['name'] = '';
                    $rows['users'][$user_key]['time'] = '-';
                    $rows['users'][$user_key]['classes'] = array();
                }      
                elseif (($data[0] === 1 && (empty($from) || $data[1] != "--" || ($userInClass[$user_key][4] > $from))) || ($lessonOwner && ($data[0] === 0 || $data[0] === 4) && $data[1] != "--"))
                {
                    //$rows['debug'] .= "Session add";
                    $rows['users'][$user_key]['session'] = $user_key;
                    if(strpos($user_key, "session") === 0)
                    {
                        $name = self::LS_NAME_UNVERIFIED;  
                        $table_name = $wpdb->prefix . self::LS_TBL_SESSION;
                        $sql = $wpdb->prepare("SELECT uvname FROM $table_name WHERE id=%d", array($data[2]));
                        $session = $wpdb->get_row($sql, OBJECT);
                        
                        if(!empty($session->uvname))
                        {
                            $name = $session->uvname;  
                        }
                        $rows['users'][$user_key]['name'] = $name;
                    }
                    else
                    {
                        $u = get_userdata($data[2]);
                        $rows['users'][$user_key]['name'] = $u->display_name;
                    }
                    
                    $rows['users'][$user_key]['time'] = $data[1];
                    $rows['users'][$user_key]['classes'] = $data[3];
                }
            }
        }
        //die();
        $rows['latest']['inputs'] = $inputs;
        $rows['latest']['time'] = $latest;
        return $rows;
    }

    function get_current_url()
    {
        $url = 'http';
        if(isset($_SERVER["HTTPS"]))
        {
            if($_SERVER["HTTPS"] == "on")
            {
                $url .= "s";
            }
        }
        $url .= "://" . $_SERVER["SERVER_NAME"];
        if(isset($_SERVER["HTTPS"]))
        if($_SERVER["SERVER_PORT"] != "80")
        {
            $url .= ":" . $_SERVER["SERVER_PORT"];
        }
        $url .= $_SERVER["REQUEST_URI"];
        return $url;
    }

    function get_json_for($post_id, $latest, $userOrSession = 0, $isUser = FALSE)
    {
        $rows = $this->get_recent_responses($post_id,NULL,$latest, $userOrSession, $isUser);
        if (isset($rows['users']))
        {
            foreach($rows['users'] as $key => $row)
            {
                if (isset($row['session']))
                {
                    $result['session'][$key] = array($row['session'], $row['name'], "|" . implode("|", $row['classes']) . "|", $row['time']);
                }
                if(isset($row['responses']))
                {
                    foreach($row['responses'] as $ls => $resp)
                    {
                        $result['updates'][$key][$ls] = $resp;
                    }
                }
            }
        }
         
        //$result['debug']= $rows['debug'];       
        $result['latest'] = $rows['latest']['time'];

        $timeinputs = array();
        foreach($rows['latest']['inputs'] as $inp)
        {
            $key = $inp[3];
            if(isset($timeinputs[$key]))
            {
                $index = 0;
                while(isset($timeinputs[$key . $index]))
                {
                    $index++;
                }
                $key .= $index;
            }
            $timeinputs[$key] = array($inp[0], $inp[1], $inp[2], $inp[3]);
        }
        ksort($timeinputs);

        $result['inputs'] = array_values($timeinputs);                        
 		$result['response'] = "ok";
     	return $result;
        
    }

    function get_formatted_time($time)
    {
        if($time == "--")
        {
            $t = $time;
        }
        else
        {            
            $d1 = date_create("now");
            $d2 = date_create($time);
            if($d1->format("Y-m-d") == $d2->format("Y-m-d"))
            {
                $t = $d2->format("H:i:s");
            }
            else
            {
                $di = date_diff(date_create($d1->format("Y-m-d")),date_create($d2->format("Y-m-d")));
                $t = $di->format("%a days");
            }
        }
        return $t;
    }

    function get_formatted_input($options, $values, $name, $pureInp)
    {
        $fmtt = "(None)";
        if(isset($options['l' . get_the_ID()][$name]))
        {
            $fmtt = $options['l' . get_the_ID()][$name];
        }
        elseif(isset($options['l0'][$name]))
        {
            $fmtt = $options['l0'][$name];
        }

        $fmt = array(self::LS_TOK_INPUT,0);
        if(isset($values[$fmtt]))
        {
            $fmt = $values[$fmtt];
        }

        $inp = str_replace(self::LS_TOK_INPUT, $pureInp, $fmt[0]); 
        if($fmt[1])
        {
            $purl = parse_url($inp);
            if(isset($purl['query']))
            {
                $purl['query'] = rawurlencode($purl['query']);                                                    
            }
            if(isset($purl['fragment']))
            {
                $purl['fragment'] = rawurlencode($purl['fragment']);
            }
            $pinp = http_build_url($purl);  
            $inp = "<a href='" . $pinp . "'>" . esc_html($pureInp) . "</a>";
        }
        else
        {
            $inp = preg_replace_callback('~(<pre.*?><code*.?>)(.*?)(</code></pre>)~ismu',
                function($i) {
                    return $i[1] . esc_html($i[2]) . $i[3];
                }, $inp
            );
        }

        return $inp;
    }

    function wp_footer()
    {
	    wp_localize_script( self::LS_SCRIPT_FOOT, 'lsAnswers', $this->auto_answers);
	    wp_enqueue_script( self::LS_SCRIPT_FOOT );
    }
}

$ls_plugin = new Learnstones_Plugin();

?>
