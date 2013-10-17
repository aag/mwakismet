Mediawiki-Akismet
=================
This is a project to add Akismet support to Mediawiki.  It will currently
run all page edits through the Akismet servers and disallow saving if
Akismet decides an edit is spam.  There is the beginning of an admin
interface to mark edits as spam or ham, but it doesn't allow submitting
to the Akismet servers.

Requirements
------------
Mediawiki-Akismet requires a working installation of MediaWiki, PHP 5, and an
Akismet account.  The extension has been tested with MediaWiki 1.19 and
PHP 5.4, but it may work on earlier versions.

Installing
----------
1. Install Mediawiki and verify that it's working.

2. Create the directory INSTALLDIR/extensions/Akismet on the server and copy 
   the Mediawiki-Akismet files to the directory.

3. Copy config_example.php to config.php and edit the variables in it to 
   match your server settings.

4. Using a web browser, navigate to 
   http://<MediawikiURL>/extensions/Akismet/install.php 
   and click the "Recreate the table" link.

5. Edit LocalSettings.php in the Mediawiki root directory.  Add these three 
   lines near the end of the file, but before the "?>":
   
   ```php
   // Akismet extension
   include( $IP . '/extensions/Akismet/Akismet.php' );
   $wgMWAkismetKey = '1234567890ab';
   $wgMWAkismetURL = 'http://www.example.com/';
   ```

6. Edit the $wgMWAkismetKey and $wgMWAkismetURL variables to match the API 
   key you got from Akismet and the location of your Mediawiki installation.
   If you don't have an Akismet API key yet, you can [create a free or paid
   account on the Akismet site](https://akismet.com/plans/) and get one.

7. Make page edits and let Akismet catch the spam.

License
-------
This code is released under the GPL 2 License.  See the COPYING file for
details.

