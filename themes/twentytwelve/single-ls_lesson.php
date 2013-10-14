<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); 
?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			Click <a id="openGallery" href="#">here</a> to view lesson.
			<?php while ( have_posts() ) : the_post(); ?>
				<div id="slides" style="display:none" >
					<?php
						$first = true;
						$nonce = wp_create_nonce("ls_submission_nonce");
						$content = get_the_content();
						// This should be a global as it is used in learnstones.php
						$classes = explode(" ", "lsred lsamber lsgreen lsgrey");
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
										  if(isset($_SESSION['post' . get_the_ID()][$i]))
										  {
											if(isset($classes[$_SESSION['post' . get_the_ID()][$i]]))
											{
												$class=$classes[$_SESSION['post' . get_the_ID()][$i]];
											}
										  }
										  ?>
											<li><a onclick="jQuery.colorbox.setSelectedIndex(<?php echo($i) ?>); return false;" href="#"><span data-menu="lsmenu<?php echo($i) ?>" class="lsmenuimg <?php echo($class); ?>" ><?php echo($i +1) ?></span><span data-menu="lsmenu<?php echo($i) ?>item">Dummy</span></a></li><?php
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
<?php get_footer(); ?>
?