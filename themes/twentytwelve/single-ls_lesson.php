<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header();
global $ls_plugin;
$ls_plugin->single_lesson();
?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
