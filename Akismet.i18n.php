<?php
/*
 * Internationalization file for the Akismet Mediawiki extension.
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
 * @file
 * @ingroup Extensions
 */

$messages = array();
 
/** English
 * @author Adam Goforth
 */
$messages['en'] = array(
    'akismet' => 'Akismet Admin', // Important! This is the string that appears on Special:SpecialPages
    'akismet-desc' => 'Adds Akismet integration to Mediawiki.',
    'spam-detected' => '<span style="color: red">Warning:</span> Akismet detected spam! Edit unsuccessful.',
    'admin-page-desc' => 'This is the administration page for the Akismet extension.',
    'num-edits-found' => 'Found {{PLURAL:$1|1 spam edit|$1 spam edits}}.',
    'not-spam' => 'not spam',
);
 
/** Deutsch (German)
 * @author Adam Goforth
 */
$messages['de'] = array(
    'akismet' => 'Akismet Admin',
    'akismet-desc' => 'Adds Akismet integration to Mediawiki.',
    'spam-detected' => '<span style="color: red">Warnung:</span> Die Änderung wurde von Akismet als spam eingestuft! Bearbeitung nicht erfolgreich.',
    'admin-page-desc' => 'Diese Seite ist die Administrationsseite für die Akismet Extension.',
    'num-edits-found' => '{{PLURAL:$1|1 spam Änderung|$1 spam Änderungen}} gefunden.',
    'not-spam' => 'nicht spam',
);

?>
