<?php
/**
 * Bulk Captions
 *
 * Helper page for Zenphoto, enabling the bulk captioning of images that don't yet have one.
 *
 * @author Marcus Wong (wongm)
 * @package plugins
 */

$plugin_description = gettext("Helper page for Zenphoto, enabling the bulk captioning of images that don't yet have one.");
$plugin_author = "Marcus Wong (wongm)";
$plugin_version = '1.0.0'; 
$plugin_URL = "https://github.com/wongm/zenphoto-captions/";
$plugin_is_filter = 500 | ADMIN_PLUGIN;
$plugin_disable = !extensionEnabled('photostream') ? gettext('<em>photostream</em> plugin is required.') : false;

zp_register_filter('admin_utilities_buttons', 'bulkCaptions::button');

class bulkCaptions {
	
	static function button($buttons) {
		$buttons[] = array(
						'category'		 => gettext('Admin'),
						'enable'			 => true,
						'button_text'	 => gettext('Bulk captions'),
						'formname'		 => 'zenphotoCaption_button',
						'action'			 => WEBPATH.'/plugins/bulk-captions',
						'icon'				 => 'images/pencil.png',
						'title'				 => gettext('Bulk caption images in your gallery.'),
						'alt'					 => '',
						'hidden'			 => '',
						'rights'			 => ALBUM_RIGHTS
		);
		return $buttons;
	}
}
?>