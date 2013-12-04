<?php
/*
Plugin Name: Learnstones
Plugin URI: http://learnstones.com/
Description: Does all our learnstones stuff.
Author: Raymond Francis
Version: 0.3
*/

//http://localhost/wordpress/?ls_lesson=what

class Learnstones_Plugin
{

	const LS_DB_VERSION = "0.21";

	const LS_STYLES = "lsred lsamber lsgreen lsgrey";

	const LS_COOKIE = "lscookie";

	const LS_OPTIONS = "ls_options";

	const LS_TYPE_LESSON = "ls_lesson";

	const LS_OPT_DB_VERSION = "ls_db_version";
	const LS_OPT_SESSION_DURATION = "duration";
	const LS_OPT_SESSION_PURGE = "purge";

	const LS_TBL_SESSION = "ls_session";
	const LS_TBL_RESPONSES = "ls_responses";
	const LS_TBL_LEARNSTONES = "ls_learnstones";

	const LS_URLQ_DASH = "db";

	private $session;
	private $session_id;
	private $deb = 0;

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_logout', array( $this, 'logout' ) );
		add_action( 'wp_login', array($this, 'login'), 10, 2 );
		add_action( 'wp_ajax_ls_submission', array($this, 'submission'));
		add_action( 'wp_ajax_nopriv_ls_submission', array($this, 'submission_nopriv'));
		add_action( 'plugins_loaded', array($this, 'db_check') );
		add_action( 'admin_menu', array($this, 'create_options_page') );
		add_action( 'admin_init', array($this, 'register_and_build_fields') );
		//add_action( 'pre_get_posts', array($this, 'goto_learnstone') );

		add_filter( 'map_meta_cap', array( $this, 'map_meta_cap'), 10, 4 );
		add_filter( 'post_row_actions', array($this, 'lesson_action_row'), 10, 2 );
		add_filter( 'the_content', array($this, 'content_filter' ) );
		add_filter( 'query_vars', array($this, 'add_query_vars') );

		register_activation_hook( __FILE__, array($this, 'activation') );
	}

	function add_query_vars( $qvars ) {
		// For learnstone selection by title
		$qvars[] = 'course';
		$qvars[] = 'ls';

		return $qvars;
	}


	function init() {

		global $wpdb;

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


		register_post_type( 	self::LS_TYPE_LESSON,
					array(
						'labels' => $labels,
						'public' => true,
						'has_archive' => true,
						'show_in_menu' => true,
						//'rewrite' => array('slug' => 'lessons'),
						//'capability_type' => 'lessons',
						//'capabilities' => $caps,
						//'map_meta_cap' => true,
					)

		);

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

		//wp_deregister_script('jquery');
		//wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', false, '');		
		//wp_enqueue_script( 'jquery' );
	
		wp_register_script( 'ls_script', plugins_url('js/ls.js', __FILE__), array('jquery', 'ls_theme_script_0') );
		wp_localize_script( 'ls_script', 'lsAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'LS_RED' => 0, 'LS_AMBER' => 1, 'LS_GREEN' => 2, 'LS_GREY' => 3, 'LS_STYLES' => self::LS_STYLES ));

		$this->session_id = 0;
		if (isset($_COOKIE[self::LS_COOKIE])) {
			$this->session_id = $_COOKIE[self::LS_COOKIE];
		}

		$table_name = $wpdb->prefix . self::LS_TBL_SESSION;
		if($this->session_id == 0) {		
			$user = 0;
			if(is_user_logged_in())
			{
				$current_user = wp_get_current_user();
				$user = $current_user->ID;
			}
			$rows_affected = $wpdb->insert( $table_name, 
							array(
								'time' => current_time('mysql'),
								'user' => $user
							)
					);
                        if($rows_affected != FALSE)
			{
				$this->session_id = $wpdb->insert_id;
			}
		}
		else
		{
			$wpdb->update( $table_name,
					array(
						'time' => current_time('mysql')
					),
					array(
						'id' => $this->session_id
					),
					array(
						 "%s"
					),
					array(
						"%d"
					)
			);
		}


		$this->get_session();

		$options = get_option(self::LS_OPTIONS);
		if($options[self::LS_OPT_SESSION_DURATION] == 0)
		{
			setcookie(self::LS_COOKIE, $this->session_id, 0);
		}
		else
		{
			setcookie(self::LS_COOKIE, $this->session_id, time()+$options[self::LS_OPT_SESSION_DURATION]);
		}

		wp_enqueue_script( 'ls_script' );

	}

	function lesson_action_row($actions, $post)
	{
		if ($post->post_type == self::LS_TYPE_LESSON){
	             $actions['dashboard'] = "<a title='" . esc_attr(__('Dashboard')) . "' href='" . get_permalink( $post )  . "&" . self::LS_URLQ_DASH . "=1'>" . __('Dashboard') . "</a>";
		}
		return $actions;
	}

	function create_options_page() {
		add_options_page('Learnstones', 'Learnstones', 'administrator', __FILE__, array($this, 'build_options_page' ) );
	}

	function build_options_page() {
		?>
			<div id="wrap">
				<div class="icon32" id="icon-tools"> <br /> </div>
				<h2>Learnstones Options</h2>
				<form method="post" action="options.php">
					<?php settings_fields(self::LS_OPTIONS); ?>
					<?php do_settings_sections(__FILE__); ?>
					<?php submit_button(); ?>
				</form>
			</div>
		<?php
	}

	function register_and_build_fields() {
		register_setting(self::LS_OPTIONS, self::LS_OPTIONS, array($this, 'validate_setting'));
		add_settings_section('ls_main_section', 'Main Settings', array($this, 'setting_section'), __FILE__);
		add_settings_field(self::LS_OPT_SESSION_DURATION, 'Learnstones Session Duration:', array($this, 'setting_input'), __FILE__, 'ls_main_section', self::LS_OPT_SESSION_DURATION);
		add_settings_field(self::LS_OPT_SESSION_PURGE, 'Purge Sessions from Database:', array($this, 'setting_input'), __FILE__, 'ls_main_section', self::LS_OPT_SESSION_PURGE);
	}

	function validate_setting($ls_options) {
		return $ls_options;
	}

	function setting_section() {
	}

	function setting_input($arg) {
		$options = get_option(self::LS_OPTIONS);
		if($arg == self::LS_OPT_SESSION_DURATION)
		{ ?>
			<select name='<?php echo self::LS_OPTIONS . "[" . $arg . "]'" ?> >
				<option value='0'		<?php if($options[self::LS_OPT_SESSION_DURATION] == 0) { echo "selected='true'"; } ?>>Browser Session</option>
				<option value='86400'		<?php if($options[self::LS_OPT_SESSION_DURATION] == 86400) { echo "selected='true'"; } ?>>1 Day</option>
				<option value='2592000'		<?php if($options[self::LS_OPT_SESSION_DURATION] == 2592000) { echo "selected='true'"; } ?>>30 Days</option>
				<option value='31536000'	<?php if($options[self::LS_OPT_SESSION_DURATION] == 31536000) { echo "selected='true'"; } ?>>1 Year</option>
			</select> <?php
		}
		else
		{ ?>
			<select name='<?php echo self::LS_OPTIONS . "[" . $arg . "]'" ?> >
				<option value='0'		<?php if($options[self::LS_OPT_SESSION_PURGE] == 0) { echo "selected='true'"; } ?>>Never</option>
				<option value='86400'		<?php if($options[self::LS_OPT_SESSION_PURGE] == 86400) { echo "selected='true'"; } ?>>1 Day</option>
				<option value='2592000'		<?php if($options[self::LS_OPT_SESSION_PURGE] == 2592000) { echo "selected='true'"; } ?>>30 Days</option>
				<option value='31536000'	<?php if($options[self::LS_OPT_SESSION_PURGE] == 31536000) { echo "selected='true'"; } ?>>1 Year</option>
			</select> <?php
		}
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


	function submission()
	{
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "ls_submission_nonce")) {
			exit("No naughty business please");
		}

		$post_id = $_REQUEST['post_id'];
		if($_REQUEST['type'] == "mark")
		{
			$this->set_learnstone_data($post_id, $_REQUEST['learnstone'], $_REQUEST['response'], 0);
			$result['response'] = "ok";
		}
		else // "sub"
		{
			$author_id = get_post_field("post_author", $post_id );
			$title = get_post_field("post_title", $post_id );
			$email = get_the_author_meta( "user_email", $author_id );
			if(isset($this->session['post' . $post_id]))
			{
				$marks = $this->session['post' . $post_id];
				mail($email, $title . " response", print_r($marks, true));
			}			
			$result['response'] = $email;
		}
		
		echo json_encode($result);
		die();

	}

	function login($user_login, $user)
	{
		//If a session is active, save it to the database
		global $wpdb;

		wp_set_current_user($user->id);
		if(isset($this->session))
		{
			foreach($this->session as $post => $responses)
			{
				foreach($responses as $learnstone => $response)
				{
					$this->set_learnstone_data( substr($post, 4), $learnstone, $response[0], $response[1]);
				}
			}
			// Loop through array setting learnstone value
			$table_name = $wpdb->prefix . self::LS_TBL_SESSION;
			$this->session = array();
			$wpdb->update(
				$table_name,
				array(
					'session_data' => serialize($this->session)
				),
				array(
					'id' => $this->session_id
				),
				array(
					"%s"
				),
				array(
					"%d"
				)
			);
											
		}
	}

	function logout()
	{
	}

	function submission_nopriv()
	{
		$this->submission();
		
		die();
	}

	function activation() {
		$this->db_install();
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
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	   		$table_name = $wpdb->prefix . self::LS_TBL_SESSION;

			$sql = "CREATE TABLE $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					user bigint(20),
					session_data BLOB,
					UNIQUE KEY id (id)
				);";
			dbDelta( $sql );

	   		$table_name = $wpdb->prefix . self::LS_TBL_RESPONSES;

			$sql = "CREATE TABLE $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					user bigint(20),
					post bigint(20),
					stone bigint(20),
					response smallint(5),
					time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					data BLOB,
					UNIQUE KEY id (id),
					KEY response_key (user,post,stone,response)
				);";
			dbDelta( $sql );

	   		$table_name = $wpdb->prefix . self::LS_TBL_LEARNSTONES;

			$sql = "CREATE TABLE $table_name (
					id bigint(20) NOT NULL AUTO_INCREMENT,
					course bigint(20),
					post bigint(20),
					parent bigint(20),
					name varchar(128),
					order int(5),
					UNIQUE KEY id (id),
					UNIQUE KEY course_key (course, name),
					UNIQUE KEY post_key (post, name)
				);";
			dbDelta( $sql );


			update_option( self::LS_OPT_DB_VERSION, self::LS_DB_VERSION );
		}

	}

	function db_check() {
		if (get_site_option( self::LS_OPT_DB_VERSION ) != self::LS_DB_VERSION) {
			$this->db_install();
		}
	}

	function get_session()
	{

		global $wpdb;

		if(!isset($this->session))
		{
			if (isset($_COOKIE[self::LS_COOKIE])) {
				$this->session_id = $_COOKIE[self::LS_COOKIE];
				$table_name = $wpdb->prefix . self::LS_TBL_SESSION;
				$session_obj = $wpdb->get_row("SELECT * FROM $table_name WHERE id = " . $this->session_id);
				if(isset($session_obj->session_data))
				{
					$this->session = unserialize($session_obj->session_data);
				}
				else
				{
					$this->session = array();
				}
			}
		}
	}

	function get_learnstone_data($post_id, $learnstone)
	{
		global $wpdb;
		$ret = FALSE;

		// Get data from database if user is logged in
		if(is_user_logged_in())
		{			
			$current_user = wp_get_current_user();
			$user = $current_user->ID;
	   		$table_name = $wpdb->prefix . self::LS_TBL_RESPONSES;
			$data = $wpdb->get_row("SELECT response FROM $table_name WHERE user = " . $user . " AND post = " . $post_id . " AND stone = " . $learnstone . " ORDER BY time DESC LIMIT 1");
			if($data != NULL)
			{
				$ret = $data->response;
			}
		}
		else
		{
			if(isset($this->session['post' . $post_id][$learnstone]))
			{
				$ret = $this->session['post' . $post_id][$learnstone][0];
		  	}
		}

		return $ret;
	}


	function set_learnstone_data($post_id, $learnstone, $response, $time)
	{
		global $wpdb;
		
		// Set data in database if user is logged in
		if(is_user_logged_in())
		{
			$current_user = wp_get_current_user();
			$user = $current_user->ID;
			$table_name = $wpdb->prefix . self::LS_TBL_RESPONSES;
			$t = $time;
			if($t == 0)
			{
				$t = current_time('mysql');
			}
			$wpdb->insert($table_name,
				array(
					'user' => $user,
					'post' => $post_id,
					'stone' => $learnstone,
					'response' => $response,
					'time' => $t,
					'data' => bin2hex(serialize(array()))
				),
				array(
					"%d",
					"%d",
					"%d",
					"%d",
					"%s",
					"%s"
				)
			);
		}
		else
		{
			if(isset($this->session))
			{
				if(isset($this->session['post' . $post_id]))
				{
					$marks = $this->session['post' . $post_id];
				}
				$resp = array( $response, current_time('mysql'));
				$marks[$learnstone] = $resp;
				$this->session['post' . $post_id] = $marks;
				$table_name = $wpdb->prefix . self::LS_TBL_SESSION;
				$wpdb->update(
					$table_name,
					array(
						'session_data' => serialize($this->session)
					),
					array(
						'id' => $this->session_id
					),
					array(
						"%s"
					),
					array(
						"%d"
					)
				);
			}
		}
	}


	function content_filter($content)
	{
		global $wpdb;
		global $post;

		$ret = $content;
		if( is_singular() && is_main_query() && get_post_type() == self::LS_TYPE_LESSON) {
			if(isset($_GET[self::LS_URLQ_DASH]))
			{
				$ret = "The dashboard";
			}
			else
			{
				$postId = get_the_ID();
				$ret = 	'Click <a id="openGallery" href="#">here</a> to view lesson.';
				$ret .=		'<div id="slides" style="display:none" >';
				$first = true;
				$nonce = wp_create_nonce("ls_submission_nonce");
				$classes = explode(" ", self::LS_STYLES);
				$slides = explode("<hr />", $content);
				$slides[] = "<h1>Submission</h1>Name/Email:<input type=\"text\" /><a onclick=\"jQuery.ls.submission('GotText', " . $postId . ", '" . $nonce . "' ); return false;\" href=\"#\">Submit</a>";
				// This is the start of a non-js link, just for ref: $link = admin_url('admin-ajax.php?action=ls_submission&post_id='.$postId.'&nonce='.$nonce);

				foreach($slides as $key => $value) {
					if($first) { 
						$ret .= '<div class="lsfixedmenu" >';
						$ret .= 	'<ul class="lsmenu" >';
						for ($i = 0; $i < count($slides); $i++)
						{
							$class = "lsgrey";
							$resp = $this->get_learnstone_data($postId, $i);
							if($resp !== FALSE)
							{
								$class=$classes[$resp];
							}
							$ret .=		'<li><a onclick="jQuery.ls.setSelectedIndex(' . $i . '); return false;" href="#"><span data-menu="lsmenu' . $i . '" class="lsmenuimg ' . $class . '" >' . ($i + 1) . '</span><span data-menu="lsmenu' . $i . 'item">Dummy</span></a></li>';
						}
						$ret .= 	'</ul>';
						$ret .= '</div>';
						$ret .= '<div>';
						$ret .= 	'<ul class="lslights">';
						$ret .= 		'<li><a class="lslightsa lslightsred" onclick="jQuery.ls.mark(jQuery.ls.getSelectedIndex(), lsAjax.LS_RED, ' . $postId . ', \'' . $nonce . '\' ); return false;" href="#">Red</a></li>';
						$ret .= 		'<li><a class="lslightsa lslightsamber" onclick="jQuery.ls.mark(jQuery.ls.getSelectedIndex(), lsAjax.LS_AMBER, ' . $postId . ', \'' . $nonce . '\' ); return false;" href="#">Amber</a></li>';
						$ret .= 		'<li><a class="lslightsa lslightsgreen" onclick="jQuery.ls.mark(jQuery.ls.getSelectedIndex(), lsAjax.LS_GREEN, ' . $postId . ', \'' . $nonce . '\' ); return false;" href="#">Green</a></li>';
						$ret .= 	'</ul>';
						$ret .= '</div>';
					}
					$ret .= '<div class="slideshow" ';
					if($first) {
						$ret .= 'rel="gallery"';
					}
					$ret .= ' >';
					$ret .= 	'<div data-menu="lsmenu' . $key . '">' . $value . '</div>';
					$ret .= '</div>';
					if($first) {
						$first = false;
					}
				}
				$ret .=		'</div>';
			}
		}
		return $ret;
	}

	function get_file()
	{
		return __FILE__;
	}
}

global $ls_plugin;
$ls_plugin = new Learnstones_Plugin();

?>