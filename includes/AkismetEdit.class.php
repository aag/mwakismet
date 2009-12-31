<?php
/*
 * Class file for the AkismetEdit class.  This class stores information
 * about a page edit that was submitted to Aksimet.
 *
 * File started on: 2008.02.01
 *
 * Copyright 2008 Adam Goforth
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

		$shortdiff = self::shortenDiffTable($html_diff);

        // Truncate
        // Create the HTML
        $htmlout  = "<div style=\"border: 1px black solid;\">\n";
        $htmlout .= "<h4 style=\"background-color: lightgray; padding: 5px;\">" . $article_title . "<br />\n";
        $htmlout .= $timestamp . "<br />\n";
        $htmlout .= $username . "</h4>\n";
        $htmlout .= $shortdiff . "\n";
        $htmlout .= "<div style=\"background-color: lightgray\">\n";
        $htmlout .= "<label for=\"spam-" . $edit_id . "\">\n";
        $htmlout .= "<input type=\"checkbox\" id=\"spam-" . $edit_id . "\" name=\"not_spam[]\" value=\"" . $edit_id . "\" />\n";
        $htmlout .= "Not Spam</label>\n";
        $htmlout .= "</div>\n";
        $htmlout .= "</div><br /><br />\n";


        return $htmlout;
    }

	private static function shortenDiffTable($html_diff) {
		$maxLength = 750;
		$diffDOM = new DOMDocument();
		$diffDOM->loadHTML($html_diff);

		$insnodes = $diffDOM->getElementsByTagName("ins");
		self::shortenLongNodes($insnodes, $maxLength);

		$divnodes = $diffDOM->getElementsByTagName("div");
		self::shortenLongNodes($divnodes, $maxLength);

//		This is broken for some unknown reason
//		self::removeExcessRows($diffDOM);

		return $diffDOM->saveHTML();
	}

	private static function shortenLongNodes(&$nodeList, $maxLength) {
		foreach ($nodeList as $node) {
			if (strlen($node->nodeValue) > $maxLength) {
				$value = $node->nodeValue;
				//$value = htmlspecialchars($value);
				$value = str_replace("&", "&amp;", $value);
				$node->nodeValue = substr($value, 0, $maxLength);
			}
		}
	}

	private static function removeExcessRows(&$diffDOM) {
		$maxRows = 5;
		$trnodes = $diffDOM->getElementsByTagName("tr");

		$rowCount = 0;
		foreach ($trnodes as $node) {
			if ($rowCount > $maxRows) {
				$parent = $trnodes->item($rowCount)->parentNode;
				$parent->removeChild($trnodes->item($rowCount));
				echo("removing node ");
			}
			$rowCount++;
		}
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
