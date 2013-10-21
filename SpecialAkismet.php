<?php
/*
 * Code file for the administration Special Page of the Akismet
 * Mediawiki extension.
 *
 * File started on: 2007.12.26
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

require_once(__DIR__ . "/includes/AkismetEdit.class.php");

class SpecialAkismet extends SpecialPage
{
    function __construct() {
        parent::__construct('Akismet');
    }

    function execute($par){
        global $wgOut;
        $db =& wfGetDB( DB_SLAVE );

        $this->setHeaders();
        $wgOut->addModuleStyles('mediawiki.action.history.diff');

        $rowcount = AkismetEdit::getSpamEditsCount();

        $wgOut->addHTML(wfMsg('admin-page-desc'));
        $wgOut->addHTML("<br /><br />");
        $wgOut->addHTML(wfMsg('num-edits-found', $rowcount) . "<br /><br />");

        // Print out the suspected spam
        $res = $db->select('akismet_edits', array('id', 'timestamp', 'page_id', 'username', 'content', 'akismet_submit_diff', 'html_diff'));

        $i = 0;

        while (($row = $db->fetchObject($res)) && ($i < 100)){
            // Get the page information
            $edit_id = $row->id;
            $page_id = $row->page_id;
            $timestamp = wfTimestamp(TS_RFC2822, $row->timestamp);
            $username = $row->username;
            $difftext = $row->html_diff;

            $wgOut->addHTML(AkismetEdit::createUserJudgeHTML($edit_id, $page_id, $timestamp, $username, $difftext));
            $i++;
        }

        $db->freeResult($res);
    }
}

?>
