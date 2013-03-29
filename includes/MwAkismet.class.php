<?php
/* Akismet extension for Mediawiki.
 *
 * Started on: 2007.10.16
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
 */

require_once( dirname( __FILE__ ) . '/Akismet.class.php' );

class MwAkismet
{
    // Listening to the EditFilter event.
    // Returning true will allow the edit to be saved, returning false will require re-editing.
    public function checkEditPageWithAkismet($editor, $newText, $section, &$error){
        mwAkismetLoadi18nMessages();
        global $wgUser;
        
        $oldText = $editor->mArticle->getContent();
        $username = $wgUser->getName();
        $url = $editor->mTitle->getFullURL();

        $containsSpam = $this->changeIsSpam($oldText, $newText, $username, $url);

        // Don't allow the edit to be persisted.  Display the edit page again with an error message
        if ($containsSpam){
            // Add the edit to the database, so the user can view it manually later.
            if (!empty($editor->mArticle->mRevision)){
                $rev_id = $editor->mArticle->mRevision->getId();
            } else {
                $rev_id = null;
            }
            $page_id = $editor->mArticle->getId();
            $submitted_diff = $this->extractDiff($oldText, $newText);
            $title = $editor->mArticle->getTitle();
            $html_diff = $this->getHtmlDiff($title, $oldText, $newText);
            $this->addSuspectedSpamToDB($page_id, $rev_id, $newText, $username, $submitted_diff, $html_diff);

            $spamDetectedMsg = wfMsg( 'spam-detected' );
            $error = "<b>$spamDetectedMsg</b>";
            $editor->showEditForm( array( &$this, 'editCallback' ) );

            return false;
        }

        // Allow the edit
        return true;
    }

    // Gets the diff of two texts and queries the Akismet servers with it.
    private function changeIsSpam($oldText, $newText, $author, $permalink){
        $diff = $this->extractDiff($oldText, $newText);

        // Check the diff to see if it's spam
        $isSpam = $this->queryAkismet($author, $diff, $permalink);
        
        return $isSpam;
    }

    // A wrapper function to make it easy to create an Akismet object and query the servers
    // with it.
    public function queryAkismet($author, $textDiff, $permalink){
        global $wgMWAkismetKey;
        global $wgMWAkismetURL;
    
        // First check to see if the config settings are set
        if ($wgMWAkismetKey == '' || $wgMWAkismetURL == ''){
            echo "Akismet key and url must be set.  Instructions for getting a key are here: <a href=\"http://faq.wordpress.com/2005/10/19/api-key/\">API key FAQ on Wordpress.com</a>";
            die;
        }
        
        $akismet = new Akismet($wgMWAkismetURL ,$wgMWAkismetKey);
        $akismet->setCommentAuthor($author);
        $akismet->setCommentAuthorEmail("");
        $akismet->setCommentAuthorURL("");
        $akismet->setCommentContent($textDiff);
        $akismet->setPermalink($permalink);

        $isSpam = $akismet->isCommentSpam();

        return $isSpam;
    }

    private function addSuspectedSpamToDB($page_id, $rev_id, $newText, $username, $submitted_diff, $html_diff){
        $db =& wfGetDB( DB_MASTER );

        $timestamp = $db->timestamp();

        $db->insert('akismet_edits',
                    array(
                        'timestamp' => $timestamp,
                        'page_id' => $page_id,
                        'rev_id' => $rev_id,
                        'username' => $username,
                        'content' => $newText,
                        'akismet_submit_diff' => $submitted_diff,
                        'html_diff' => $html_diff));
    }

    /**
     *  Gets the diff of two strings.  The algorithm only returns lines that were added or changed 
     *  in the newText.  It does not show subtracted lines or the changes within a line (it just
     *  includes the whole line if it was changed.
     */
    public function extractDiff(&$oldText, &$newText){
        // Nothing was added if the newText is empty
        if ($newText == ""){
            return "";
        }
        
        $oldLines = explode("\n", $oldText);
        $newLines = explode("\n", $newText);

        $lastFound = -1;
        $addedLines = "";
        $newLinesBuffer = "";
        
        for ($i = 0; $i < count($oldLines); $i++){
            // Add prepended and in-the-middle lines
            for ($j = $lastFound + 1; $j < count($newLines); $j++){
                if ($oldLines[$i] == $newLines[$j]){
                    $addedLines .= $newLinesBuffer;
                    $lastFound = $j;
                    $newLinesBuffer = "";
                    break;
                } else {
                    $newLinesBuffer .= $newLines[$j] . "\n";
                }
            }
            $newLinesBuffer = "";
        }
        
        // Add appended lines
        if ($lastFound < (count($newLines) - 1)){
            for ($j = $lastFound + 1; $j < count($newLines); $j++){
                $newLinesBuffer .= $newLines[$j] . "\n";
            }
            $addedLines .= $newLinesBuffer;
        }
        
        return $addedLines;
    }

    /** 
     * Gets the HTML of the diff that MediaWiki displays to the user.
     */
    public function getHtmlDiff($pageTitle, $oldText, $newText){
        $de = new DifferenceEngine($pageTitle);
        $de->setText($oldText, $newText);
        $difftext = $de->getDiff("Old", "New");

        return $difftext;
    }
}

?>

