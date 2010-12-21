<?php if ( ! defined('APP_VER')) exit('No direct script access allowed');


/**
 * Wygwam Upload Tags
 * 
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Brandon Kelly
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */

class Wygwam_upload_tags_ext {

	var $name           = 'Wygwam Upload Tags';
	var $version        = '1.0';
	var $description    = 'Parses Wygwam\'s Upload Directory settings for {username}, {member_id}, and {group_id}';
	var $settings_exist = 'n';
	var $docs_url       = 'http://pixelandtonic.com/wygwam';

	/**
	 * Class Constructor
	 */
	function __construct()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Activate Extension
	 */
	function activate_extension()
	{
		// add the row to exp_extensions
		$this->EE->db->insert('extensions', array(
			'class'    => 'Wygwam_upload_tags_ext',
			'hook'     => 'wygwam_config',
			'method'   => 'wygwam_config',
			'priority' => 10,
			'version'  => $this->version,
			'enabled'  => 'y'
		));
	}

	/**
	 * Update Extension
	 */
	function update_extension($current = '')
	{
		// Nothing to change...
		return FALSE;
	}

	/**
	 * Disable Extension
	 */
	function disable_extension()
	{
		// Remove all Wygwam_upload_tags_ext rows from exp_extensions
		$this->EE->db->query('DELETE FROM exp_extensions WHERE class = "Wygwam_upload_tags_ext"');
	}

	// --------------------------------------------------------------------

	/**
	 * wygwam_config hook
	 */
	function wygwam_config($config, $settings)
	{
		// If another extension shares the same hook,
		// we need to get the latest and greatest config
		if ($this->EE->extensions->last_call !== FALSE)
		{
			$config = $this->EE->extensions->last_call;
		}

		// Does this field have an Upload Directory set?
		$upload_dir = isset($config['upload_dir']) ? $config['upload_dir']
			: (isset($settings['upload_dir']) ? $settings['upload_dir'] : FALSE);

		if ($upload_dir && isset($config['filebrowserImageBrowseUrl']))
		{
			// Prepare the tags we wish to parse
			$vars = array(
				'username'  => $this->EE->session->userdata['username'],
				'member_id' => $this->EE->session->userdata['member_id'],
				'group_id'  => $this->EE->session->userdata['group_id']
			);

			// Get a reference to the Upload Directory session array
			$sess =& $_SESSION['wygwam_'.$upload_dir];

			// Parse the Server Path and URL settings
			$sess['p'] = $this->EE->functions->var_swap($sess['p'], $vars); // Server Path
			$sess['u'] = $this->EE->functions->var_swap($sess['u'], $vars); // URL
		}

		// Return the (unmodified) config
		return $config;
	}
}

// End of file ext.wygwam_upload_tags.php */
// Location: ./system/expressionengine/third_party/wygwam_upload_tags/ext.wygwam_upload_tags.php
