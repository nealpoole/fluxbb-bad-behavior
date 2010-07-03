##
##
##        Mod title:  Bad Behavior Integration for FluxBB
##
##      Mod version:  1.1.0
##  Works on FluxBB:  1.2.*, 1.4.*
##     Release date:  2010-07-03
##           Author:  Smartys (smartys@punbb-hosting.com)
##
##      Description:  This mod integrates the Bad Behavior (http://www.bad-behavior.ioerror.us)
##                    script with FluxBB.
##
##   Affected files:  include/common.php
##
##       Affects DB:  Yes
##
##            Notes:  The database changes are installed automatically by the script when it is
##                    first loaded. Also, Bad Behavior's logging may not work well with
##                    PostgreSQL/SQLite: if that's true, logging can be disabled by setting
##                    the value of the 'logging' key of the $bb2_settings_defaults array in
##                    bad-behavior-fluxbb.php to false.
##
##                    This mod includes Bad Behavior version 2.1.2
##
##       DISCLAIMER:  Please note that "mods" are not officially supported by
##                    FluxBB. Installation of this modification is done at your
##                    own risk. Backup your forum database and any and all
##                    applicable files before proceeding.
##
##


#
#---------[ 1. UPLOAD ]-------------------------------------------------------
#

AP_Bad_Behavior.php to /plugins/
bad-behavior-fluxbb.php to /include/
bad-behavior-mysql.php to /include/
/bad-behavior/* to /include/bad-behavior/


#
#---------[ 2. OPEN ]---------------------------------------------------------
#

include/common.php


#
#---------[ 3. FIND ]-----------------------------------------------------------
#

// Check/update/set cookie and fetch user info
$pun_user = array();
check_cookie($pun_user);


#
#---------[ 4. BEFORE, ADD ]---------------------------------------------------
#

// BadBehavior will stop the spammers!
require PUN_ROOT.'include/bad-behavior-fluxbb.php';


#
#---------[ 5. SAVE/UPLOAD ]-------------------------------------------------
#