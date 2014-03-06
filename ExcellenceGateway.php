<?php
/**
 * @package ExcellenceGateway
 * @version 0.8
 */
/*
	Plugin Name: EG WordPress API
	Description: 
	Author: Oliver Chalk - Oxinet
	Version: 0.8
	Author URI: http://oxi.net
	Licence: GNU General Public License, version 3 (GPL-3.0)

	Copyright 2014 Oliver Chalk - Oxinet (email : ollie@oxi.net)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 3, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once 'functions.php';

function ExcellenceGateway_Search_func( $atts ) {	
	try {
	
		extract( shortcode_atts( array(
			'searchterm' => '*:*',
			'results' => intval(10),
			'offset' => intval(0),
			'pagesize' => intval(0),
			'pagingsize' => intval(6),
			'enablepaging' => false,
			'results_template' => '',
			'result_item_template' => '',
			'qs_pagenumber' => '',
		), $atts ) );
		
		$result_item_template = '';
		
		if ( $atts['result_item_template'] == '' || $atts['result_item_template'] == 'result_item_default' ) {
			$result_item_template = 'result_item_default.php';
		} else {
			$result_item_template = $atts['result_item_template'].'.php';
		}
		
		$pg = $atts['qs_pagenumber'];
		if (empty( $pg )) { $pg = 'pg'; }
		
		$requestedPage = 0;
		foreach ($_GET as $key => $value) {
			if ( $key === $pg ) {
				$requestedPage = intval($value);
			}
		}
		
		$eg_search = new EG_WP(
			array
			(
				'results' => $atts['results'],
				'offset' => $atts['offset'],
				'searchTerm' => $atts['searchterm'],
				'pageSize' => $atts['pagesize'],
				'enablePaging' => $atts['enablepaging'],
				'pagingSize' => $atts['pagingsize'],
				'requestedPage' => $requestedPage,
				'qs_pagenumber' => $pg,
			)
		);
		
		$eg_search_results = $eg_search->ExecuteSearch();
		
		if ( $atts['results_template'] == '' || $atts['results_template'] == 'results_default' ) {
			include 'Templates/results_default.php';
		} else {
			include 'Templates/'.$atts['results_template'].'.php';
		}
		
	} catch (Exception $e) {
	}
}

add_shortcode( 'ExcellenceGateway_Search', 'ExcellenceGateway_Search_func' );

function ExcellenceGateway_Single_func( $atts ) {
	
}

add_shortcode( 'ExcellenceGateway_Single', 'ExcellenceGateway_Single_func' );

?>