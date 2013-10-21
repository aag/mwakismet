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
        $specialPageCSS = $this->getPageCSS();

        $wgOut->addHTML($specialPageCSS);
        $wgOut->addHTML(wfMsg('num-spam-edits', $rowcount) . "<br /><br />");

        $wgOut->addHtml('<form action="" method="post">');

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

        $wgOut->addHtml('<button type="submit">' . wfMsg('save') . '</button>');
        $wgOut->addHtml('</form>');
    }

    function getPageCSS() {
        $pageCSS = <<<CSS
        <style>
            .spam-diff {
                border: 1px #aaa solid;
                border-radius: 3px;
            }
            .diff-header {
                background-color: #dadada;
                padding: 5px;
                border-radius: 2px 2px 0 0;
                border-bottom: 1px #aaa solid;
                color: #333;
            }
            .diff-header h4 {
                margin: 0;
                padding: 0;
                color: #333;
            }
            .diff-header p {
                margin-bottom: 0;
            }
            .diff-holder {
                height: 200px;
                overflow: auto;
            }
            .diff-footer {
                background-color: #dadada;
                border-radius: 0 0 2px 2px;
                border-top: 1px #aaa solid;
            }
            .diff-footer fieldset {
                border: 0;
                margin: 0;
                padding: 0;
            }
            .diff-footer label {
                margin-bottom: 5px;
            }
        </style>
CSS;

        return $pageCSS;
    }
}

?>
