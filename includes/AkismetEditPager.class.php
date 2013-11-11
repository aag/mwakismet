<?php
/* Akismet Edit pager class for MediaWiki-Akismet. This class paginates
 * the suspected spam edits on the Special Page.
 *
 * Started on: 2013.10.26
 *
 * Copyright 2013 Adam Goforth
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
 */

require_once(__DIR__ . "/AkismetEdit.class.php");

class AkismetEditPager extends ReverseChronologicalPager {

	function __construct( IContextSource $context = null ) {
		parent::__construct( $context );

        // Override the defaults
        $this->mLimitsShown = array( 10, 20 );
        $this->mDefaultLimit = 10;
        $this->mLimit = 10;
    }
	   
    /**
     * Returns an associative array providing all parameters needed for the
     * main paged query.
     *
     * Overrides the function in the parent class.
     */
    function getQueryInfo() {
		return array(
			'tables' => array( 'akismet_edits' ),
			'fields' => array(
                'id',
                'timestamp',
                'page_id',
                'username',
                'content',
                'akismet_submit_diff',
                'html_diff',
			),
			'conds' => array(),
			'join_conds' => array()
		);
    }
    
    /**
     * Returns the name of the DB index to use for pagination.
     *
     * Overrides the function in the parent class.
     */
    function getIndexField() {
        return 'timestamp';
    }

	/**
     * The database field name used as a default sort order.
     *
     * Overrides the function in the parent class.
	 */
    function getDefaultSort() {
        return 'timestamp';
    }

    /**
     * HTML formatting function. This takes a single row from the 
     * akismet_edits DB table and returns an HTML string. The rows are
     * concatenated together by the super class.
     *
     * Overrides the function in the parent class.
     *
     * @param $row Object: database row
     * @return String
     */
    function formatRow($row) {
        $edit_id = $row->id;
        $page_id = $row->page_id;
        $timestamp = wfTimestamp(TS_RFC2822, $row->timestamp);
        $username = $row->username;
        $difftext = $row->html_diff;

        return AkismetEdit::createUserJudgeHTML($edit_id, $page_id, $timestamp, $username, $difftext);
    }
 
}

