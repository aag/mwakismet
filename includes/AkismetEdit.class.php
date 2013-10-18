<?php
/*
 * Class file for the AkismetEdit class.  This class stores information
 * about a page edit that was submitted to Aksimet.
 *
 * File started on: 2008.02.01
 *
 * Copyright 2008-2013 Adam Goforth
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

class AkismetEdit
{
    public static function createUserJudgeHTML($edit_id, $page_id, $timestamp, $username, $html_diff){
        $article_title = self::getArticleTitleFromID($page_id);

        // Create the HTML
        $htmlout  = "<div style=\"border: 1px black solid;\">\n";
        $htmlout .= "<h4 style=\"background-color: lightgray; padding: 5px;\">" . $article_title . "<br />\n";
        $htmlout .= $timestamp . "<br />\n";
        $htmlout .= $username . "</h4>\n";
        $htmlout .= "<div class=\"diffholder\" style=\"height: 200px; overflow: auto;\">\n";
        $htmlout .= $html_diff . "\n";
        $htmlout .= "</div>\n";
        $htmlout .= "<div style=\"background-color: lightgray\">\n";
        $htmlout .= "<label for=\"spam-" . $edit_id . "\">\n";
        $htmlout .= "<input type=\"checkbox\" id=\"spam-" . $edit_id . "\" name=\"not_spam[]\" value=\"" . $edit_id . "\" />\n";
        $htmlout .= "Not Spam</label>\n";
        $htmlout .= "</div>\n";
        $htmlout .= "</div><br /><br />\n";

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

        $res = $db->query("SELECT COUNT(*) as n FROM mw_akismet_edits");
        $row = $db->fetchObject( $res );
        $rowcount = intval( $row->n );

        $db->freeResult($res);

        return $rowcount;
    }
}

?>
