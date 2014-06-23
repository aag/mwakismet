<?php
/*
 * Class file for the AkismetEdit class.  This class stores information
 * about a page edit that was submitted to Aksimet.
 *
 * File started on: 2008.02.01
 *
 * Copyright 2008-2013 Adam Goforth
 *
 * This file is part of MediaWiki-Akismet.
 *
 * MediaWiki-Akismet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * MediaWiki-Akismet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MediaWiki-Akismet.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class AkismetEdit
{
    public static function createUserJudgeHTML($edit_id, $page_id, $timestamp, $username, $html_diff){
        $notSpamMsg = wfMsg('not-spam');
        $delSpamMsg = wfMsg('delete-permanently');

        $page_title = self::getArticleTitleFromID($page_id);
        $pageName = wfMsg('page-name', $page_title);
        $authorLabel = wfMsg('author-label', $username);
        $submittedOn = wfMsg('submitted-on', $timestamp);

        $htmlout = <<<HTML
        <div class="spam-diff">
            <div class="diff-header">
                <h4>$pageName</h4>
                <p>$authorLabel<br />
                $submittedOn</p>
            </div>
            <div class="diff-holder">
                $html_diff
            </div>
            <div class="diff-footer">
                <fieldset>
                    <label for="del-spam-$edit_id">
                        <input type="radio" id="del-spam-$edit_id" name="spam_not_spam-$edit_id" value="del-$edit_id" checked="checked" /> $delSpamMsg
                    </label>
                    <label for="not-spam-$edit_id">
                        <input type="radio" id="not-spam-$edit_id" name="spam_not_spam-$edit_id" value="not-spam-$edit_id" /> $notSpamMsg
                    </label>
                </fieldset>
            </div>
        </div><br /><br />
HTML;

        return $htmlout;
    }

    private static function getArticleTitleFromID($page_id){
        $db =& wfGetDB( DB_SLAVE );

        $res = $db->select('page', array('page_title'), array('page_id' => $page_id));
        $row = $db->fetchObject($res);

        $title = $row->page_title;
        $db->freeResult($res);

        return $title;
    }

    public static function getSpamEditsCount(){
        $db =& wfGetDB( DB_SLAVE );

        $res = $db->query("SELECT COUNT(*) as n FROM akismet_edits");
        $row = $db->fetchObject( $res );
        $rowcount = intval( $row->n );

        $db->freeResult($res);

        return $rowcount;
    }
}

?>
