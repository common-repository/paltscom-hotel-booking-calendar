<?php
/**
 * @package palts.com
 * @version 1.1
 */
/*
Plugin Name: palts.com hotel booking calendar
Plugin URI: http://palts.com/wordpress-plugin
Description: Include real-time booking availability calendars in your wordpress site either in the page content or as a widget.
Author: palts.com
Version: 1.1
Author URI: http://palts.com
*/


add_shortcode ('palts-calendar', 'palts_shortcode');
add_action ('wp_footer', 'palts_ftr');
add_action ('wp_head', 'palts_head');
add_action('widgets_init', create_function('', 'return register_widget("palts_widget");'));

function palts_shortcode($atts) {
	$args = shortcode_atts(array(
		'roomtype' => '0',
		'height' => '400',
		'style' => '',
		'servicelist' => '',
		'locale' => 'en_GB',
		'server' => '',
		'hide' => '',
		'credits' => false
	), $atts);
	$args['roomtype'] = (int) $args['roomtype'];
	if($args['hide']) return '';
	if(!$args['roomtype']) return '<p style="border:2px solid red;">Please provide <i>roomtype</i> code, example: <br><i>[palts-calendar service="17"]</i><br> You can find the codes in your <a href="https://palts.com/a/en_GB/hotel/edit">hotel settings page</a></p>';
	return 
		'<div class="palts-content-calendar" style="width:200px; ' . $args['style'] . '">'
		.palts_iframe($args)
		.($args['credits'] ? '<div class="credits"><a href="http://palts.com/">palts.com hotel booking</a></div>' : '')
		.'</div>';
}

function palts_head() {
	?>
	<style type="text/css">
	/* tip: you are welcome to override these styles by !important styles of your own */
	.widget_palts_widget .credits a, .palts-content-calendar .credits a {
		font: 10px verdana,sans-serif;
	}
	.palts-content-calendar .credits{
		width: 100%;
		text-align: center;
	}
	</style> 
	<?php
}

function palts_iframe($args) {
	if(!$args['server']) $args['server'] = "http://palts.com";
	$src = $args['server'].'/a/'.$args['locale'].'/hotelsite/cal/service/'.$args['roomtype'];
	if($args['servicelist']) $src .= "/servicelist/1";
	return '<iframe width="200px" height="'.$args['height'].'px" frameborder="0" src="'.$src.'" scrolling="auto">tik-tak...</iframe>';
}

class palts_widget extends WP_Widget {
	function palts_widget() { // widget actual processes
		parent::WP_Widget(false, $name = 'palts.com calendar');	
	}

	function form($instance) { // outputs the options form on admin
		?>
		<p>Get help <a href="http://palts.com/wordpress-plugin/" target="_blank">here</a>.
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('roomtype'); ?>"><?php _e('Room type ID:'); ?></label>
			<input size="3" id="<?php echo $this->get_field_id('roomtype'); ?>" name="<?php echo $this->get_field_name('roomtype'); ?>" type="text" value="<?php echo esc_attr($instance['roomtype']); ?>" />
			<br />Find your room ID's from <a href="https://palts.com/a/et_EE/hotel/services" target="_blank">palts.com room type settings</a>.
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('servicelist'); ?>"><?php _e('Show list of room types'); ?></label>
			<input id="<?php echo $this->get_field_id('servicelist'); ?>" name="<?php echo $this->get_field_name('servicelist'); ?>" type="checkbox" <?php echo $instance['servicelist'] ? 'CHECKED' : '' ; ?> />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('locale'); ?>"><?php _e('Language:'); ?></label>
			<input size="3" id="<?php echo $this->get_field_id('locale'); ?>" name="<?php echo $this->get_field_name('locale'); ?>" type="text" value="<?php echo esc_attr($instance['locale']); ?>" />
			<br />(2-letter locale code)
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:'); ?></label>
			<input size="3" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo esc_attr($instance['height']); ?>" />px
		</p>
		<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['locale'] = strip_tags($new_instance['locale']);
		$instance['roomtype'] = (int)$new_instance['roomtype'];
		$instance['height'] = (int)$new_instance['height'];
		if($instance['height'] < 10) $instance['height'] = ''; 
		if($new_instance['servicelist']) $instance['servicelist'] = 1; 
		return $instance;
	}

	function widget($args, $instance) {
		if(!$instance['locale']) $instance['locale'] = 'en_GB';
		if(!$instance['height']) $instance['height'] = 300;
		echo $args['before_widget'];
		if ($instance['title']) echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title']; 
		echo palts_iframe($instance)
			.'<div class="credits"><a href="http://palts.com/">palts.com hotel booking</a></div>'
			.$args['after_widget']; 
	}
}

?>