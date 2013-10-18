<?php

/* Class that holds some of the hook handlers in the Akismet Mediawiki
 * extension.
 *
 * File started on: 2013.10.18
 *
 * Copyright 2013 Adam Goforth
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

class AkismetHooks {
	/**
	 * UnitTestsList hook handler
	 * @param $files Array: List of extension test files
	 * @return bool
	 */
	public static function onUnitTestsList(&$files) {
        $testDir = __DIR__ . '/test';
        $files = array_merge( $files, glob( "$testDir/*Test.php" ) );
		return true;
	}
}
