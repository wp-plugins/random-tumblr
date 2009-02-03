<?php
/*
Plugin Name:  Random Tumblr
Plugin URI: http://www.vjcatkick.com/?page_id=7015
Description: Sidebar widget which displays one photo from your tumblr randomly. This widget pulls only photo image which you uploaded and/or rebloged.
Version: 0.1.0
Author: V.J.Catkick
Author URI: http://www.vjcatkick.com/
*/

/*
License: GPL
Compatibility: WordPress 2.6 with Widget-plugin.

Installation:
Place the widget_single_photo folder in your /wp-content/plugins/ directory
and activate through the administration panel, and then go to the widget panel and
drag it to where you would like to have it!
*/

/*  Copyright V.J.Catkick - http://www.vjcatkick.com/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/* Changelog
* Feb 02 2009 - v0.1.0
- Initial release
*/


function widget_random_tumblr_init() {
	if ( !function_exists('register_sidebar_widget') )
		return;

	function widget_random_tumblr( $args ) {
		extract($args);

		$options = get_option('widget_random_tumblr');
		$title = $options['widget_random_tumblr_title'];
		$tuid = $options['widget_random_tumblr_uid'];
		$widget_random_tumblr_img_style = $options['widget_random_tumblr_img_style'];
		$widget_random_tumblr_display_pagelink = $options['widget_random_tumblr_display_pagelink'];
		$widget_random_tumblr_additional_html = $options['widget_random_tumblr_additional_html'];
		$widget_random_tumblr_width = $options['widget_random_tumblr_width'];

		$output = '<div id="widget_random_tumblr"><ul>';

		// section main logic from here 

		$tumblr_userid = $tuid;
		$tumblr_num = 1;
		$img_style = $widget_random_tumblr_img_style;
		$tumblr_size = 4;
		$display_pagelink = $widget_random_tumblr_display_pagelink;
		$pagecounter = 0;		// for future use
		$width_sidebar = $widget_random_tumblr_width;

		$_tumblrurl = 'http://' . $tumblr_userid . '.tumblr.com/api/read?start=' . $pagecounter . '&num=' . $tumblr_num . '&type=photo';
		$_tumblrurl  = urlencode( $_tumblrurl );	// for only compatibility
		$_tumblr_xml = @simplexml_load_file( $_tumblrurl );
		$max_number = $_tumblr_xml->posts[ 'total' ];
		$pagecounter = floor( rand( 0, $max_number - 1 ) );

		$tumblr_size = 2;
		$_tumblrurl = 'http://' . $tumblr_userid . '.tumblr.com/api/read?start=' . $pagecounter . '&num=' . $tumblr_num . '&type=photo';
		$_tumblrurl  = urlencode( $_tumblrurl );	// for only compatibility
		$_tumblr_xml = @simplexml_load_file( $_tumblrurl );

		foreach( $_tumblr_xml->posts[0]->post as $p ) {
			$photourl = $p->{"photo-url"}[$tumblr_size];		// 4 = 75px sq
			$linkurl = $p[url];
			$output .= '<a href="' . $linkurl . '" target="_blank" >';
			$output .= '<img src="' . $photourl . '" border="0" style="' . $img_style . '"  width="' . $width_sidebar . '" />';
			$output .= '</a>';
		} /* foreach */

		if( $display_pagelink ) {
			$output .= '<div style="width:100%; text-align:center; font-size:7pt; margin-right:10px; margin-bottom:3px;" >';
			$output .='<a href="http://' . $tumblr_userid . '.tumblr.com/" target="_blank" >';
			$output .= $_tumblr_xml->tumblelog[title];
			$output .= '</a></div>';
		} /* if */

		if( $widget_random_tumblr_additional_html ) {
			$output .= str_replace( "\\","", $widget_random_tumblr_additional_html );
		} /* if */

		// These lines generate the output
		$output .= '</ul></div>';

		echo $before_widget . $before_title . $title . $after_title;
		echo $output;
		echo $after_widget;
	} /* widget_random_tumblr() */

	function widget_random_tumblr_control() {
		$options = $newoptions = get_option('widget_random_tumblr');
		if ( $_POST["widget_random_tumblr_submit"] ) {
			$newoptions['widget_random_tumblr_title'] = strip_tags(stripslashes($_POST["widget_random_tumblr_title"]));
			$newoptions['widget_random_tumblr_uid'] = $_POST["widget_random_tumblr_uid"];
			$newoptions['widget_random_tumblr_img_style'] = $_POST["widget_random_tumblr_img_style"];
			$newoptions['widget_random_tumblr_display_pagelink'] = (boolean) $_POST["widget_random_tumblr_display_pagelink"];
			$newoptions['widget_random_tumblr_additional_html'] = $_POST["widget_random_tumblr_additional_html"];
			$newoptions['widget_random_tumblr_width'] = $_POST["widget_random_tumblr_width"];
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_random_tumblr', $options);
		}

		// those are default value
		if ( !$options['widget_random_tumblr_width'] ) $options['widget_random_tumblr_width'] = 165;

		$tuid = $options['widget_random_tumblr_uid'];
		$widget_random_tumblr_width = $options['widget_random_tumblr_width'];
		$widget_random_tumblr_img_style = $options['widget_random_tumblr_img_style'];
		$widget_random_tumblr_display_pagelink = $options['widget_random_tumblr_display_pagelink'];
		$widget_random_tumblr_additional_html = $options['widget_random_tumblr_additional_html'];

		$title = htmlspecialchars($options['widget_random_tumblr_title'], ENT_QUOTES);
?>
	    <?php _e('Title:'); ?> <input style="width: 170px;" id="widget_random_tumblr_title" name="widget_random_tumblr_title" type="text" value="<?php echo $title; ?>" /><br />
		<?php _e('Tumblr ID:'); ?> <input style="width: 100px;" id="widget_random_tumblr_uid" name="widget_random_tumblr_uid" type="text" value="<?php echo $tuid; ?>" />.tumblr.com<br />
		<?php _e('Image Width:'); ?> <input style="width: 80px;" id="widget_random_tumblr_width" name="widget_random_tumblr_width" type="text" value="<?php echo $widget_random_tumblr_width; ?>" /> <br />
		<?php _e('Img CSS:'); ?><br /><input style="width: 100%;" id="widget_random_tumblr_img_style" name="widget_random_tumblr_img_style" type="textarea" value="<?php echo $widget_random_tumblr_img_style; ?>" /><br />
		<input style="" id="widget_random_tumblr_display_pagelink" name="widget_random_tumblr_display_pagelink" type="checkbox" value="1" <?php if( $widget_random_tumblr_display_pagelink ) {echo 'checked';} ?> />Display tumblr link<br />
		<?php _e('Additional HTML:'); ?><br />
		<textarea rows=3 id="widget_random_tumblr_additional_html" name="widget_random_tumblr_additional_html" /><?php echo $widget_random_tumblr_additional_html; ?></textarea><br />


  	    <input type="hidden" id="widget_random_tumblr_submit" name="widget_random_tumblr_submit" value="1" />
		<div style="width:100%;text-align:right;"><a href="http://www.vjcatkick.com/" target="_blank"><img src='http://www.vjcatkick.com/logo_vjck.png' border='0'/></a></div>
<?php
	} /* widget_random_tumblr_control() */

	register_sidebar_widget('Random Tumblr', 'widget_random_tumblr');
	register_widget_control('Random Tumblr', 'widget_random_tumblr_control' );
} /* widget_random_tumblr_init() */

add_action('plugins_loaded', 'widget_random_tumblr_init');

?>