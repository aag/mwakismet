<?php

/* Setup file for the Akismet Mediawiki extension.
 *
 * File started on: 2007.10.16
 *
 * Copyright 2007-2013 Adam Goforth
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
<pre>require_once("$IP/extensions/Akismet/Akismet.php");</pre>
<p>at the bottom of your LocalSettings.php.</p>
<?php
    exit(1);
}

// Register the features
$wgExtensionCredits['other'][] = array(
    'name' => 'Akismet',
    'author' => 'Adam Goforth',
    'version' => 0.2,
    'url' => 'http://mediawiki-akismet.definingterms.com/',
    'descriptionmsg' => 'akismet-desc',
);

// Autoload the plugin's classes
$wgAutoloadClasses['MwAkismet'] = __DIR__ . '/includes/MwAkismet.class.php';
$wgAutoloadClasses['SpecialAkismet'] = __DIR__ . '/SpecialAkismet.php';
$wgAutoloadClasses['AkismetHooks'] = __DIR__ . '/Akismet.hooks.php';

// Autoload the localized UI strings
$wgExtensionMessagesFiles['Akismet'] = __DIR__ . '/Akismet.i18n.php';
$wgExtensionMessagesFiles['AkismetAlias' ] = __DIR__ . '/Akismet.alias.php';

// Register the edit handler
$wgHooks['EditFilter'][] = array(new MwAkismet(), 'checkEditPageWithAkismet');

// Add the unit tests
$wgAutoloadClasses['MwAkismetTest'] = __DIR__ . '/test/*Test.php';
$wgHooks['UnitTestsList'][] = 'AkismetHooks::onUnitTestsList';

// Register the DB schema update handler
$wgHooks['LoadExtensionSchemaUpdates'][] = 'AkismetHooks::onSchemaUpdate';

// Register the admin special page
$wgSpecialPages['Akismet'] = 'SpecialAkismet';

// Define user-configurable global variables
global $wgMWAkismetKey;
$wgMWAkismetKey = '';
global $wgMWAkismetURL;
$wgMWAkismetURL = '';

?>
