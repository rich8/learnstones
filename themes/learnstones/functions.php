<?php 

class Learnstones_Theme
{
	const LS_OPT_SLIDE = "slide";

	const LS_SLIDESHOW_FOLDER = "/slideshows";

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array($this, 'register_and_build_fields') );
	}

	function init()
	{

		$options = get_option(Learnstones_Plugin::LS_OPTIONS);
		$slide = "default";
		if(isset($options[self::LS_OPT_SLIDE]))
		{
			$slide = $options[self::LS_OPT_SLIDE];
		}


		if(!file_exists(get_stylesheet_directory() . self::LS_SLIDESHOW_FOLDER . "/" . $slide))
		{
			$slideshows = scandir(get_stylesheet_directory() . self::LS_SLIDESHOW_FOLDER);
			foreach($slideshows as $slideshow)
			{
				if(strpos($slideshow, ".") !== 0)
				{
					$slide = $slideshow;
					$options[self::LS_OPT_SLIDE] = $slideshow;
					update_option(Learnstones_Plugin::LS_OPTIONS, $options);
					break;
				}
			}
		}

		$slidefolder = get_stylesheet_directory() . self::LS_SLIDESHOW_FOLDER . "/" . $slide;

		$slideuri = get_stylesheet_directory_uri() . self::LS_SLIDESHOW_FOLDER . "/" . $slide;

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
					wp_register_style( $style, $slideuri . '/css/' . $file, array( $dep ));
				}
				else
				{
					wp_register_style( $style, $slideuri . '/css/' . $file);
				}
				wp_enqueue_style( $style );
				$dep = $style;
				$deps[] = $style;
				$cnt++;
			}
		}

		//The twentytwelve style sheet forces ul styles for some reason, hence dependency.
		$deps[] = 'twentytwelve-style';
		wp_register_style( 'ls_theme_style', get_stylesheet_directory_uri() . '/css/slide.css', $deps );
		wp_enqueue_style( 'ls_theme_style' );

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
					wp_register_script( $jsFile , $slideuri . '/js/' . $file, array('jquery', $dep) );
				}
				else
				{
					wp_register_script( $jsFile , $slideuri . '/js/' . $file, array('jquery') );
				}
				$dep = $jsFile;
				wp_enqueue_script( $jsFile );
				$cnt++;
			}
		}

	}

	function register_and_build_fields() {
		global $ls_plugin;
		add_settings_field(self::LS_OPT_SLIDE, 'Slideshow:', array($this, 'setting_input'), $ls_plugin->get_file(), 'ls_main_section', self::LS_OPT_SLIDE);
	}

	function setting_input($arg) {
		$options = get_option(Learnstones_Plugin::LS_OPTIONS);
		if($arg == self::LS_OPT_SLIDE)
		{ 
			$slideshows = scandir(get_stylesheet_directory() . self::LS_SLIDESHOW_FOLDER); ?>
			<select name='<?php echo Learnstones_Plugin::LS_OPTIONS . "[" . $arg . "]'" ?> ><?php
				foreach($slideshows as $slideshow)
				{
					if(strpos($slideshow, ".") !== 0)
					{?>
						<option value='<?php echo $slideshow; ?>' <?php if($options[self::LS_OPT_SLIDE] == $slideshow) { echo "selected='true'"; } ?>><?php count($slideshows); ?><?php echo $slideshow; ?></option><?php
					}
				} ?>
			</select> <?php
		}
	}

}

new Learnstones_Theme();

?>