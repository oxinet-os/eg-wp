<?php

require_once 'SolrPhpClient/Apache/Solr/Service_custom.php';

class EG_WP
{
	// The SOLR arguments for the plugin
	const SOLR_HOST = 'solr.host.qia.oxi.net';
	const SOLR_PORT = '8080';
	const SOLR_PATH = 'solr';
	
	const SOLR_URI = 'http://egws.demoiis.oxfordcc.co.uk/resources?mode=solr';
	
	// Class variables:
	// - args is an array to pass to the service's
	// - results_pagesize is an int to set the size of a page
	// - pageNumber is an int to set the page number of results
	protected $args, $results_pagesize, $pageNumber;
	
	// Main construct, pass the arguments to correct construct.
	function __construct() 
    { 
        $a = func_get_args(); 
        $i = func_num_args(); 
        if (method_exists($this,$f='__construct'.$i))
		{ 
            call_user_func_array(array($this,$f),$a); 
        }
		else
		{
			throw new Exception('EG_WP: There is no contruct supporting '.$i.' variables, please consider passing an array object as the second argument.');
		}
    } 
	
	function __construct0()
	{
		// No arguments, then return this class object.
		return $this;
	}
	
	function __construct1($args)
    {
		// Construct with no array argument, just the web service.
        $this->args = $args;
		return $this;
    }
	
	private $searchArguments;
	public function SearchArguments()
	{
		if (empty($searchArguments))
		{
			// default search term is *:* - no class argument matching search_term
			$search_term = "*:*";
			$_term = str_replace( '"', '', trim($this->args['search_term']) );
			if (!empty($_term)) {
				$search_term = '"'.$_term.'"';
			}
			$search_enabled = false;
			if (!empty($this->args['search_enabled'])) {
				$search_enabled = $this->args['search_enabled'];
			}
			$paging_enabled = false;
			if (!empty($this->args['paging_enabled'])) {
				$paging_enabled = $this->args['paging_enabled'];
			}
			// default results_offset is 0 - no class argument matching results_offset
			$results_offset = "0";
			if (!empty($this->args['results_offset'])) {
				$results_offset = $this->args['results_offset'];
			}
			// default results_count is -1 - no class argument matching results_count
			// -1 results_count returns the count of matching search_term
			$results_count = "10";
			if (!empty($this->args['results_count'])) {
				$results_count = $this->args['results_count'];
			}
			$additionalParameters = array( 'fl' => '*,score' );
			if (!empty($this->args['additionalParameters'])) {
				$additionalParameters = $this->args['additionalParameters'];
			}
			$paging_size = "6";
			if (!empty($this->args['paging_size'])) {
				$paging_size = $this->args['paging_size'];
			}
			$results_pagesize = "10";
			if (!empty($this->args['results_pagesize'])) {
				$results_pagesize = $this->args['results_pagesize'];
			}
			$requestedPage = "0";
			if (!empty($this->args['requestedPage'])) {
				$requestedPage = $this->args['requestedPage'];
			}
			$qs_pagenumber = "pg";
			if (!empty($this->args['qs_pagenumber'])) {
				$qs_pagenumber = $this->args['qs_pagenumber'];
			}
			$qs_searchterm = "qq";
			if (!empty($this->args['qs_searchterm'])) {
				$qs_searchterm = $this->args['qs_searchterm'];
			}
			
			$searchArguments = array( 
				'search_enabled' => $search_enabled,
				'paging_enabled' => $paging_enabled,
				'results_offset' => $results_offset,
				'results_count' => $results_count,
				'search_term' => $search_term,
				'prettySearchTerm' => ($_term == "*:*" || $_term == "*") ? "Any" : $_term,
				'additionalParameters' => $additionalParameters,
				'results_pagesize' => $results_pagesize,
				'requestedPage' => ($requestedPage == 0 ? 0 : ($requestedPage - 1)),
				'paging_size' => $paging_size,
				'qs_pagenumber' => $qs_pagenumber,
				'qs_searchterm' => $qs_searchterm,
			);
		}
		// return array with values (default at the least, or class arguments)
		return $searchArguments;
	}
	
	private $solrInstance;
	protected function SolrInstance($asUri)
	{
		if (empty($solrInstance)) {
			if ( $asUri ) {
				//echo 'SolrInstance useAPI = '.$asUri.'.<br/>';
				// new SOLR class object using above const's
				$solr = new Apache_Solr_Service( EG_WP::SOLR_URI );
				$solr->setQueryDelimiter("&");
				$solrInstance = $solr;
			} else {
				//echo 'SolrInstance useAPI = '.$asUri.'.<br/>';
				// new SOLR class object using above const's
				$solr = new Apache_Solr_Service( '', EG_WP::SOLR_HOST, EG_WP::SOLR_PORT, EG_WP::SOLR_PATH );
				// test SOLR service
				if (!$solr->ping()) {
					throw new Exception('EG_WP: Solr service not responding.');
				} else {
					$solrInstance = $solr;
				}
			}
		}
		return $solrInstance;
	}
	
	//protected function 
	
	public function ResultCount() 
	{
		$use_api = false;
		if (!empty($this->args['use_api'])) {
			$use_api = $this->args['use_api'];
		}
		
		// get the SOLR instance
		$solr = $this->SolrInstance($use_api);
		// get the search arguments
		$search = $this->SearchArguments();
		$query = $search['search_term'];
		$additionalParameters = $search['additionalParameters'];
		// execute search
		$res = $solr->search($query, 0, -1, $additionalParameters);
		
		return $res->response->numFound;
	}
	
	public function PagingResult()
	{
		$search = $this->SearchArguments();
		$count = intval($this->ResultCount()) - 1;
		
		$qs_searchterm = $search['qs_searchterm'];
		
		$qs_pagenumber = $search['qs_pagenumber'];
		$paging_size = intval($search['paging_size']);
		$results_pagesize = intval($search['results_pagesize']);
		$requestedPage = intval($search['requestedPage']);
		$computedPage = intval($search['requestedPage']) + 1;
		$offsetSize = $requestedPage * $results_pagesize;
		
		$varDivided = ($count / $results_pagesize);
		$totalPages = ceil($varDivided);
		
		$lowerBound = max( 1, $computedPage - $paging_size);
		$upperBound = min( $totalPages, $computedPage + $paging_size);
		
		if ($computedPage == 1) {
			$previousPage = 1;
			$hasPreviousBtn = false;
		} else {
			$previousPage = $computedPage - 1;
			$hasPreviousBtn = true;
		}
		if ($computedPage == $totalPages || $totalPages == 0) {
			$nextPage = 0;
			$hasNextBtn = false; 
		} else {
			$nextPage = $computedPage + 1;
			$hasNextBtn = true; 
		}
		
		$lb = $lowerBound;
		$ub = $upperBound;
		$tp = $totalPages;
		$cp = $computedPage;
		
		$pagingDiv = '<div class="eg-paging">';
		if ( $tp != 1 ) {
			if ( $hasPreviousBtn ) {
				$pagingDiv .= '<a href="?'.$qs_pagenumber.'='.$previousPage.'" class="eg-paging-prev"><< Previous</a>&nbsp;&nbsp;';
			}
			if ( $lb > 1 ) {
				$pagingDiv .= '<a href="?'.$qs_pagenumber.'=1">1</a>&nbsp;&nbsp;...&nbsp;&nbsp;';
			}
			while($lb <= $ub) {
				if ($lb == $cp) {
					$pagingDiv .= '<span class="eg-cur-page">'.$lb.'</span>&nbsp;&nbsp;';
				} else {
					$pagingDiv .= '<a href="?'.$qs_pagenumber.'='.$lb.'">'.$lb.'</a>&nbsp;&nbsp;';
				}
				$lb ++;
			}
			if ( $ub <= ($tp - 1) ) {
				$pagingDiv .= '...&nbsp;&nbsp;<a href="?'.$qs_pagenumber.'='.$tp.'">'.$tp.'</a>';
			}
			if ( $hasNextBtn ) {
				$pagingDiv .= '&nbsp;&nbsp;<a href="?'.$qs_pagenumber.'='.$nextPage.'" class="eg-paging-next">Next >></a>';
			}
		}
		$pagingDiv .= '</div>';
		
		return array( 
			'offsetSize' => $offsetSize,
			'results_pagesize' => $results_pagesize,
			'requestedPage' => $requestedPage,
			'computedPage' => $computedPage,
			'previousBtn' => $hasPreviousBtn,
			'previousPage' => $previousPage,
			'nextBtn' => $hasNextBtn,
			'nextPage' => $nextPage,
			'lowerBound' => $lowerBound,
			'upperBound' => $upperBound,
			'pagingElement' => $pagingDiv,
			'totalPages' => $totalPages,
			'search_term' => $search['search_term'],
			'prettySearchTerm' => $search['prettySearchTerm'],
			'additionalParameters' => $search['additionalParameters'],
		);
	}
			
	public function ExecuteSearch()
	{
		$use_api = false;
		if (!empty($this->args['use_api'])) {
			$use_api = $this->args['use_api'];
		}
		
		// get the SOLR instance
		$solr = $this->SolrInstance($use_api);
		
		// get the search arguments
		$search = $this->SearchArguments();
		$pagingEnabled = $search['paging_enabled'];
		
		// is paging enabled, if so get search options from that
		if ( $pagingEnabled ) {
			$paging = $this->PagingResult();
			$offset = $paging['offsetSize'];
			$limit = $paging['results_pagesize'];
			$query = $paging['search_term'];
			$additionalParameters = $paging['additionalParameters'];
		// else get the default (or set) results_count sizes and parameters
		} else {
			$offset = $search['results_offset'];
			$limit = $search['results_count'];
			$query = $search['search_term'];
			$additionalParameters = $search['additionalParameters'];
		}
		
		// execute search
		$res = $solr->search($query, $offset, $limit, $additionalParameters);

		// response
		$responseArray = get_object_vars($res->response);
		
		// if paging is enabled, merge the paging result array onto the front of the response array
		if ( $pagingEnabled ) {
			// prepend paging settings to response
			$mergedArray = array_merge( $paging, $responseArray );
		} else {
			// prepend search settings to response
			$mergedArray = array_merge( $search, $responseArray );
		}
		
		return $mergedArray;
	}
}

?>