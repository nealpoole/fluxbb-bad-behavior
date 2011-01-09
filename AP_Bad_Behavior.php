<?php
/***********************************************************************

  Copyright (C) 2002-2005  Smartys (smartys@punbb-hosting.com)

  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/

// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);

// If the "Save" button was clicked
if (isset($_POST['save']))
{
	$form = array_map("intval", $_POST['form']);
	while (list($key, $input) = @each($form))
	{
		$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$input.' WHERE conf_name=\'o_badbehavior_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
	}
	
	// Regenerate the config cache
	require_once PUN_ROOT.'include/cache.php';
	generate_config_cache();
	
	redirect($_SERVER['REQUEST_URI'], 'Options updated. Redirecting...');
}
else
{
	// Display the admin navigation menu
	generate_admin_menu($plugin);

?>
	<div id="badbehaviorplugin" class="plugin blockform">
		<h2><span>Bad Behavior Plugin</span></h2>
		<div class="box">
			<div class="inbox">
				<p>The Bad Behavior system helps protect your site from malicious bots (spammers, email harvesters, etc).</p>
				<p>For more information please visit the <a href="http://www.bad-behavior.ioerror.us/">Bad Behavior</a> homepage.</p>
				<p>If you find Bad Behavior valuable, please consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=error%40ioerror%2eus&item_name=Bad%20Behavior%20<?php echo BB2_VERSION; ?>%20%28From%20Admin%29&no_shipping=1&cn=Comments%20about%20Bad%20Behavior&tax=0&currency_code=USD&bn=PP%2dDonationsBF&charset=UTF%2d8">financial contribution</a> to further development of Bad Behavior.</p>
			</div>
		</div>
		<h2 class="block2"><span>Statistics:</span></h2>
		<div class="box">
			<div class="inbox">
				<p><?php echo bb2_insert_stats(true) ?></p>
			</div>
		</div>
		<h2 class="block2"><span>Settings</span></h2>
		<div class="box">
			<form id="example" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<p class="submittop"><input type="submit" name="save" value="Save changes" /></p>
				<div class="inform">
					<fieldset>
						<legend>Change your settings and submit!</legend>
						<div class="infldset">
							<table class="aligntop" cellspacing="0">
								<tr>
									<th scope="row">Display Statistics</th>
									<td>
										<input type="radio" name="form[display_stats]" value="1"<?php if ($pun_config['o_badbehavior_display_stats'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong>Yes</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[display_stats]" value="0"<?php if ($pun_config['o_badbehavior_display_stats'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong>No</strong>
										<span>Allows you to decide if statistics should be displayed publicly or not (you will also need to edit the code to include a call to bb2_insert_stats()). This setting does not affect the statistics seen above.</span>
									</td>
								</tr>
								<tr>
									<th scope="row">Verbose Logging</th>
									<td>
										<input type="radio" name="form[verbose]" value="1"<?php if ($pun_config['o_badbehavior_verbose'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong>Yes</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[verbose]" value="0"<?php if ($pun_config['o_badbehavior_verbose'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong>No</strong>
										<span>More verbose logging (logs data from all requests made).</span>
									</td>
								</tr>
								<tr>
									<th scope="row">Strict Mode</th>
									<td>
										<input type="radio" name="form[strict]" value="1"<?php if ($pun_config['o_badbehavior_strict'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong>Yes</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[strict]" value="0"<?php if ($pun_config['o_badbehavior_strict'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong>No</strong>
										<span>Strict checking (blocks more spam but may block some people)</span>
									</td>
								</tr>
							</table>
						</div>
					</fieldset>
				</div>
				<p class="submitend"><input type="submit" name="save" value="Save changes" /></p>
			</form>
		</div>
	</div>
<?php

}

// Note that the script just ends here. The footer will be included by admin_loader.php.
