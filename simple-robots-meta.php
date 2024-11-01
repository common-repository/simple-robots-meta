<?php
/*
Plugin Name: Simple Robots Meta
Plugin URI: https://wordpress.org/plugins/simple-robots-meta/
Description: Let you toggle the display of some basic meta tags relating to robots/web crawlers, namely "noarchive", "noindex", and "nofollow"
Version: 1.0
Author: Stephen Rider
Author URI: http://striderweb.com/nerdaphernalia/
Text Domain: simple-robots-meta
Domain Path: /lang
*/

class SRM_robots_meta {
	var $meta_options;
	var $option_db_name = 'plugin_simple-robots-meta_settings';
	var $option_name = 'robots_meta_settings';

	function __construct() {
		add_action( 'wp_head', array( &$this, 'echo_meta' ), 1 );
		add_action( 'admin_menu', array( &$this, 'add_admin_page' ) );
		add_action( 'admin_init', array( &$this, 'register_options') );
	}
	
	function register_options() {
		register_setting( $this->option_name, $this->option_db_name, array( &$this, 'validate_settings') );
	}

	function validate_settings( $input ) {
		$input['index'] = ( $input['index'] == 1 ? 1 : 0 );
		$input['follow'] = ( $input['follow'] == 1 ? 1 : 0 );
		$input['archive'] = ( $input['archive'] == 1 ? 1 : 0 );
		return $input;
	}

	function set_defaults( $overwrite = false ) {
		$new_options = array(
			'index' => true,
			'follow' => true,
			'archive' => true,
		);
		if( $overwrite ) {
			update_option( $this->option_db_name, $new_options );
		} else {
			$old_options = get_option( $this->option_db_name, $new_options );
			if( count( $old_options ) != count( $new_options ) ) {
				$new_options = array_merge( $new_options, $old_options );
				update_option( $this->option_db_name, $new_options );
			} else {
				$new_options = $old_options;
			}
		}
		return $new_options;
	}

	function echo_meta() {
		$options = $this->set_defaults();
		$index = $options['index'] ? 'index' : 'noindex';
		$follow = $options['follow'] ? 'follow' : 'nofollow';
		$archive = $options['archive'] ? 'archive' : 'noarchive';
		echo "<meta name=\"robots\" content=\"$index, $follow, $archive\" />\n";
	}
	
	function add_admin_page() {
		if( current_user_can( 'manage_options' ) ) {
			$page = add_options_page( 'Robots Meta Settings', 'Robots Meta', 'manage_options', 'robots-meta', array( &$this, 'admin_settings_page' ) );
		}
	}
	
	function admin_settings_page() {
		add_action( 'in_admin_footer', array( &$this, 'admin_footer' ), 9 );
		?>
		<div class="wrap">
			<h2>Robots Meta</h2>
			<p>Use this form to give some basic instructions to search engines such as Google, Yahoo, or Bing. The default for any of these is Yes (checked).</p>
			<form method="post" action="options.php">
				<?php settings_fields( $this->option_name ); ?>
				<?php $options = $this->set_defaults(); ?>
				<?php //do_settings_sections('plugin'); ?>
				<p>
					<label for="chkindex"><input id="chkindex" name="<?php echo $this->option_db_name; ?>[index]" type="checkbox" value="1" <?php checked( $options['index'], 1 ); ?>></input> <strong>index</strong> (should this site show up in search engine results)</label><br />
					<label for="chkfollow"><input id="chkfollow" name="<?php echo $this->option_db_name; ?>[follow]" type="checkbox" value="1" <?php checked( $options['follow'], 1 ); ?>></input> <strong>follow</strong> (should the search engine's web crawler follow links on this site)</label><br />
					<label for="chkarchive"><input id="chkarchive" name="<?php echo $this->option_db_name; ?>[archive]" type="checkbox" value="1" <?php checked( $options['archive'], 1 ); ?>></input> <strong>archive</strong> (should this site be archived/cached by the search engine)</label>
				</p>
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</form>
		<?php
	}
	
	function admin_footer() {
		$pluginfo = get_plugin_data(__FILE__);
		printf( 'Simple Robots Meta plugin | Version %1$s | by Stephen Rider<br />', $pluginfo['Version'] );
	}
}

$SRM_robots_meta = new SRM_robots_meta;
?>