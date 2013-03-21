<?php

/* Setup file for Akismet Mediawiki extension.
 *
 * File started on: 2007.10.16
 *
 * Copyright 2007 Adam Goforth
 *
 * This file is part of Mediawiki-Akismet.
 *
 * Mediawiki-Akismet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Mediawiki-Akismet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mediawiki-Akismet.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if ( !defined( 'MEDIAWIKI' ) ) {
?>
<p>This is the MediaWiki Akismet extension. To enable it, put </p>
<pre>require_once("$IP/extensions/Akismet/Akismet.setup.php");</pre>
<p>at the bottom of your LocalSettings.php.</p>
<?php
    exit(1);
}

//$wgShowExceptionDetails = true;

require_once( dirname( __FILE__ ) . '/includes/MwAkismet.class.php' );

// Autoload the plugin's main class and the special page class
$wgAutoloadClasses['MwAkismet'] = dirname(__FILE__) . '/includes/MwAkismet.class.php';
$wgAutoloadClasses['AkismetAdmin'] = dirname(__FILE__) . '/includes/AkismetAdmin.class.php';

// Register the edit handler
$wgHooks['EditFilter'][] = array(new MwAkismet(), 'checkEditPageWithAkismet');
$wgHooks['ArticleSaveComplete'][] = array (new MwAkismet(), 'saveMetadataToDB');

// Register the admin special page
$wgSpecialPages['AkismetAdmin'] = 'AkismetAdmin';

// Register an event to load the i18n messages
$wgHooks['LoadAllMessages'][] = 'mwAkismetLoadi18nMessages';

// Register the features
$wgExtensionCredits['other'][] = array(
    'name' => 'Akismet',
    'author' => 'Adam Goforth',
    'version' => 0.1,
    'url' => 'http://mediawiki-akismet.definingterms.com/',
    'description' => 'Adds Akismet integration to Mediawiki.',
    );

// Define user-configurable global variables
global $wgMWAkismetKey;
$wgMWAkismetKey = '';
global $wgMWAkismetURL;
$wgMWAkismetURL = '';

// Load the i18n messages
function mwAkismetLoadi18nMessages() {
    global $wgMessageCache;
    
    if (isset( $wgMessageCache )) {
        require_once( dirname( __FILE__ ) . '/Akismet.i18n.php' );
        $i18nMessages = mwAkismetMessages();
        foreach( $i18nMessages as $lang => $messages ){
            $wgMessageCache->addMessages( $messages, $lang );
        }
        return true;
    } else {
        echo("<!-- No message cache found! -->");
    }
    return true;
}

?>
