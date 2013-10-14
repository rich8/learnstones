<?php
/*
Plugin Name: Learnstones
Plugin URI: http://learnstones.com/
Description: Does all our learnstones stuff.
Author: Raymond Francis
Version: 0.3
*/
class Learnstones_Plugin
{

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_logout', array( $this, 'logout' ) );
		add_filter( 'map_meta_cap', array( $this, 'map_meta_cap'), 10, 4 );
		add_action( 'wp_ajax_ls_submission', array($this, 'submission'));
		add_action( 'wp_ajax_nopriv_ls_submission', array($this, 'submission_nopriv'));

		//register_activation_hook( __FILE__, array($this, 'rewrite_flush') );
	}

	function init() {
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
		wp_localize_script( 'ls_script', 'lsAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'LS_RED' => 0, 'LS_AMBER' => 1, 'LS_GREEN' => 2, 'LS_GREY' => 3, 'LS_STYLES' => "lsred lsamber lsgreen lsgrey" ));
		wp_enqueue_script( 'ls_script' );

		//The twentytwelve style sheet forces ul styles for some reason, hence dependency.
		wp_register_style( 'ls_style', plugins_url('css/slide.css', __FILE__), array('twentytwelve-style') );
		wp_enqueue_style( 'ls_style' );

		if(!session_id())
		{
			session_start();
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
			$result['response'] = "ok";
			if(isset($_SESSION['post' . $post_id]))
			{
				$marks = $_SESSION['post' . $post_id];
			} 
			$marks[$_REQUEST['learnstone']] = $_REQUEST['response'];
			$_SESSION['post' . $post_id] = $marks;
		}
		else // "sub"
		{
			$author_id = get_post_field("post_author", $post_id );
			$title = get_post_field("post_title", $post_id );
			$email = get_the_author_meta( "user_email", $author_id );
			if(isset($_SESSION['post' . $post_id]))
			{
				$marks = $_SESSION['post' . $post_id];
				mail($email, $title . " reponse", print_r($marks, true));
			}			
			$result['response'] = $email;
		}
		
		echo json_encode($result);
		die();

	}

	function logout()
	{
		if(session_id())
		{
			session_destroy();
		}
	}

	function submission_nopriv()
	{
		$result['response'] = "illegal";

		echo json_encode($result);
		
		die();
	}

	function rewrite_flush() {
		// First, we "add" the custom post type via the above written function.
		// Note: "add" is written with quotes, as CPTs don't get added to the DB,
		// They are only referenced in the post_type column with a post entry, 
		// when you add a post of this CPT.
		//$this->ls_lesson_init();

		// ATTENTION: This is *only* done during plugin activation hook in this example!
		// You should *NEVER EVER* do this on every page load!!
		//flush_rewrite_rules();
	}
}

new Learnstones_Plugin();

?>