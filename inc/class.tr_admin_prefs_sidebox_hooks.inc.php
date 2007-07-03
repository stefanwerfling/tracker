<?php
/**
 * Tracker - Universal tracker (bugs, feature requests, ...) with voting and bounties
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @package tracker
 * @copyright (c) 2006 by Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$ 
 */

/**
 * Admin-, Prefs- and Sidebox-Menu hooks
 */
class tr_admin_prefs_sidebox_hooks
{
	function tr_admin_prefs_sidebox_hooks()
	{
		$this->check_set_default_prefs();
	}

	/**
	 * hooks to build trackers's sidebox-menu plus the admin and preferences sections
	 *
	 * @param string/array $args hook args
	 */
	function all_hooks($args)
	{
		$appname = 'tracker';
		$location = is_array($args) ? $args['location'] : $args;
		//echo "<p>tr_admin_prefs_sidebox_hooks::all_hooks(".print_r($args,True).") appname='$appname', location='$location'</p>\n";

		if ($location == 'sidebox_menu')
		{
			$file = array(
			);
			display_sidebox($appname,$GLOBALS['egw_info']['apps'][$appname]['title'].' '.lang('Menu'),$file);
		}

		if ($GLOBALS['egw_info']['user']['apps']['preferences'] && $location != 'admin')
		{
			$file = array(
				'Preferences'     => $GLOBALS['egw']->link('/index.php','menuaction=preferences.uisettings.index&appname='.$appname),
			);
			if ($location == 'preferences')
			{
				display_section($appname,$file);
			}
			else
			{
				display_sidebox($appname,lang('Preferences'),$file);
			}
		}

		if ($GLOBALS['egw_info']['user']['apps']['admin'] && $location != 'preferences')
		{
			$file = Array(
				'Site configuration' => $GLOBALS['egw']->link('/index.php','menuaction=tracker.uitracker.admin'),
				'Import TTS tickets' => $GLOBALS['egw']->link('/tracker/import_tts.php'),
//				'Custom fields' => $GLOBALS['egw']->link('/index.php','menuaction=admin.customfields.edit&appname='.$appname),
			);
			if ($location == 'admin')
			{
				display_section($appname,$file);
			}
			else
			{
				display_sidebox($appname,lang('Admin'),$file);
			}
		}
	}
	
	/**
	 * populates $GLOBALS['settings'] for the preferences
	 */
	function settings()
	{
		$this->check_set_default_prefs();

		$GLOBALS['settings']['notify_creator'] = array(
			'type'   => 'check',
			'label'  => 'Receive notifications about created tracker-items',
			'name'   => 'notify_creator',
			'help'   => 'Should the Tracker send you notification mails, if tracker items you created get updated?',
			'xmlrpc' => True,
			'admin'  => False,
		);
		$GLOBALS['settings']['notify_assigned'] = array(
			'type'   => 'check',
			'label'  => 'Receive notifications about assigned tracker-items',
			'name'   => 'notify_assigned',
			'help'   => 'Should the Tracker send you notification mails, if tracker items assigned to you get updated?',
			'xmlrpc' => True,
			'admin'  => False,
		);
		$GLOBALS['settings']['notify_html'] = array(
			'type'   => 'select',
			'label'  => 'Receive notifications in html',
			'name'   => 'notify_html',
			'help'   => 'Should the Tracker send you notification mails in html?',
			'values' => array(
				'0'  => lang('No'),
				'1'  => lang('Yes'),
				'medium' => lang('Yes, with larger fontsize'),
			),
			'xmlrpc' => True,
			'admin'  => False,
		);
		return true;	// otherwise prefs say it cant find the file ;-)
	}
	
	/**
	 * Check if reasonable default preferences are set and set them if not
	 *
	 * It sets a flag in the app-session-data to be called only once per session
	 */
	function check_set_default_prefs()
	{
		if ($GLOBALS['egw']->session->appsession('default_prefs_set','tracker'))
		{
			return;
		}
		$GLOBALS['egw']->session->appsession('default_prefs_set','tracker','set');

		$default_prefs =& $GLOBALS['egw']->preferences->default['tracker'];

		$defaults = array(
			'notify_creator'  => 1,
			'notify_assigned' => 1,
			'notify_html'	  => 1,
		);
		foreach($defaults as $var => $default)
		{
			if (!isset($default_prefs[$var]) || $default_prefs[$var] === '')
			{
				$GLOBALS['egw']->preferences->add('tracker',$var,$default,'default');
				$need_save = True;
			}
		}
		if ($need_save)
		{
			$GLOBALS['egw']->preferences->save_repository(False,'default');
		}
	}
}