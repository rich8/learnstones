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

	const LS_DB_VERSION = "0.2";

	const LS_OPT_DB_VERSION = "ls_db_version";

	const LS_STYLES = "lsred lsamber lsgreen lsgrey";

	const LS_COOKIE = "lscookie";

	const LS_OPTIONS = "ls_options";

	const LS_OPT_SESSION_DURATION = "duration";
	const LS_OPT_SESSION_PURGE = "purge";

	const LS_TBL_SESSION = "ls_session";
	const LS_TBL_RESPONSES = "ls_responses";

	private $session;
	private $session_id;
	private $deb = 0;

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_logout', array( $this, 'logout' ) );
		add_action( 'wp_login', array($this, 'login' ) );
		add_filter( 'map_meta_cap', array( $this, 'map_meta_cap'), 10, 4 );
		add_action( 'wp_ajax_ls_submission', array($this, 'submission'));
		add_action( 'wp_ajax_nopriv_ls_submission', array($this, 'submission_nopriv'));
		add_action( 'plugins_loaded', array($this, 'db_check') );
		add_action( 'admin_menu', array($this, 'create_options_page') );
		add_action( 'admin_init', array($this, 'register_and_build_fields') );


		register_activation_hook( __FILE__, array($this, 'activation') );
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


		register_post_type( 	'ls_lesson',
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

		wp_register_style( 'ls_colorbox_style', plugins_url('css/colorbox.css', __FILE__) );
		wp_enqueue_style( 'ls_colorbox_style' );

		wp_deregister_script('jquery');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', false, '');		
		wp_enqueue_script( 'jquery' );

		wp_register_script( 'ls_colorbox_script', plugins_url('js/jquery.colorbox.js', __FILE__), array('jquery') );
		wp_enqueue_script( 'ls_colorbox_script' );

		wp_register_script( 'ls_script', plugins_url('js/ls.js', __FILE__), array('jquery') );
		wp_localize_script( 'ls_script', 'lsAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'LS_RED' => 0, 'LS_AMBER' => 1, 'LS_GREEN' => 2, 'LS_GREY' => 3, 'LS_STYLES' => self::LS_STYLES ));

		//The twentytwelve style sheet forces ul styles for some reason, hence dependency.
		wp_register_style( 'ls_style', plugins_url('css/slide.css', __FILE__), array('twentytwelve-style') );
		wp_enqueue_style( 'ls_style' );
		$this->session_id = 0;
		if (isset($_COOKIE[self::LS_COOKIE])) {
			$this->session_id = $_COOKIE[self::LS_COOKIE];
		}

		if($this->session_id == 0) {		
			wp_localize_script( 'ls_script', 'lsDebug', array( 'ajaxcookie' => -1));
			$table_name = $wpdb->prefix . self::LS_TBL_SESSION;
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


		$options = get_option(self::LS_OPTIONS);
		$this->get_session();
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
		add_settings_section('main_section', 'Main Settings', array($this, 'setting_section'), __FILE__);
		add_settings_field(self::LS_OPT_SESSION_DURATION, 'Learnstones Session Duration:', array($this, 'setting_input'), __FILE__, 'main_section', self::LS_OPT_SESSION_DURATION);
		add_settings_field(self::LS_OPT_SESSION_PURGE, 'Purge Sessions from Database:', array($this, 'setting_input'), __FILE__, 'main_section', self::LS_OPT_SESSION_PURGE);
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

	function login()
	{
		//If a session is active, save it to the database
		global $wpdb;

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
					'session_data' => bin2hex(serialize($this->session))
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

	function single_lesson()
	{
		global $wpdb;
		global $post;

		?><div id="primary" class="site-content">
			<div id="content" role="main">
				Click <a id="openGallery" href="#">here</a> to view lesson.
				<?php while ( have_posts() ) : the_post(); ?>
					<div id="slides" style="display:none" >
						<?php
							$first = true;
							$nonce = wp_create_nonce("ls_submission_nonce");
							$content = get_the_content();
							$classes = explode(" ", self::LS_STYLES);
							$slides = explode("<hr />", $content);
							$slides[] = "<h1>Submission</h1>Name/Email:<input type=\"text\" /><a onclick=\"jQuery.ls.submission('GotText', $post->ID, '$nonce' ); return false;\" href=\"#\">Submit</a>";

							$link = admin_url('admin-ajax.php?action=ls_submission&post_id='.$post->ID.'&nonce='.$nonce);
							foreach($slides as $key => $value) {
								if($first) { ?>
									<div class="lsfixedmenu" >
										<ul class="lsmenu" ><?php 
											for ($i = 0; $i < count($slides); $i++)
											{
												$class = "lsgrey";
												$resp = $this->get_learnstone_data($post->ID, $i);
												if($resp !== FALSE)
												{
													$class=$classes[$resp];
												} ?>
												<li><a onclick="jQuery.colorbox.setSelectedIndex(<?php echo($i) ?>); return false;" href="#"><span data-menu="lsmenu<?php echo($i) ?>" class="lsmenuimg <?php echo($class); ?>" ><?php echo($i + 1) ?></span><span data-menu="lsmenu<?php echo($i) ?>item">Dummy</span></a></li><?php
											} ?>
										</ul>
									</div>
									<div>
										<ul class="lslights">
											<li><a class="lslightsa lslightsred" onclick="jQuery.ls.mark(jQuery.colorbox.getSelectedIndex(), lsAjax.LS_RED, <?php echo($post->ID) ?>, '<?php echo($nonce) ?>' ); return false;" href="#">Red</a></li>
											<li><a class="lslightsa lslightsamber" onclick="jQuery.ls.mark(jQuery.colorbox.getSelectedIndex(), lsAjax.LS_AMBER, <?php echo($post->ID) ?>, '<?php echo($nonce) ?>' ); return false;" href="#">Amber</a></li>
											<li><a class="lslightsa lslightsgreen" onclick="jQuery.ls.mark(jQuery.colorbox.getSelectedIndex(), lsAjax.LS_GREEN, <?php echo($post->ID) ?>, '<?php echo($nonce) ?>' ); return false;" href="#">Green</a></li>
										</ul>
									</div>
								<?php
							}
						?>
						<div class="colorbox" <?php if($first) { echo ('rel="gallery"'); } ?>>
							<div data-menu="lsmenu<?php echo($key) ?>" >
								<?php echo($value); ?>
							</div>
						</div>
						<?php
							if($first) { $first = false; }
						}
						?>
					</div>

					<?php /* get_template_part( 'content', get_post_format() ); */ ?>

					<nav class="nav-single">
						<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
						<span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentytwelve' ) . '</span> %title' ); ?></span>
						<span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentytwelve' ) . '</span>' ); ?></span>
					</nav><!-- .nav-single -->

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #primary -->
	<?php
	}
}

global $ls_plugin;
$ls_plugin = new Learnstones_Plugin();

?>