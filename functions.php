<?php

require_once 'SolrPhpClient/Apache/Solr/Service.php';

class EG_WP
{
	// The SOLR arguments for the plugin
	const SOLR_HOST = 'solr.host.qia.oxi.net';
	const SOLR_PORT = '8080';
	const SOLR_PATH = 'solr';
	
	// Class variables:
	// - args is an array to pass to the service's
	// - pageSize is an int to set the size of a page
	// - pageNumber is an int to set the page number of results
	protected $args, $pageSize, $pageNumber;
	
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
			// default search term is *:* - no class argument matching searchTerm
			$searchTerm = "*:*";
			if (!empty($this->args['searchTerm'])) {
				$searchTerm = $this->args['searchTerm'];
			}
			$enablePaging = false;
			if (!empty($this->args['enablePaging'])) {
				$enablePaging = $this->args['enablePaging'];
			}
			// default offset is 0 - no class argument matching offset
			$offset = "0";
			if (!empty($this->args['offset'])) {
				$offset = $this->args['offset'];
			}
			// default results is -1 - no class argument matching results
			// -1 results returns the count of matching searchTerm
			$results = "10";
			if (!empty($this->args['results'])) {
				$results = $this->args['results'];
			}
			$additionalParameters = array( 'fl' => '*,score' );
			if (!empty($this->args['additionalParameters'])) {
				$additionalParameters = $this->args['additionalParameters'];
			}
			$pagingSize = "6";
			if (!empty($this->args['pagingSize'])) {
				$pagingSize = $this->args['pagingSize'];
			}
			$pageSize = "10";
			if (!empty($this->args['pageSize'])) {
				$pageSize = $this->args['pageSize'];
			}
			$requestedPage = "0";
			if (!empty($this->args['requestedPage'])) {
				$requestedPage = $this->args['requestedPage'];
			}
			$qs_pagenumber = "pg";
			if (!empty($this->args['qs_pagenumber'])) {
				$qs_pagenumber = $this->args['qs_pagenumber'];
			}
			
			$searchArguments = array( 
				'enablePaging' => $enablePaging,
				'offset' => $offset,
				'results' => $results,
				'searchTerm' => $searchTerm,
				'prettySearchTerm' => ($searchTerm == "*:*" || $searchTerm == "*") ? "Any" : $searchTerm,
				'additionalParameters' => $additionalParameters,
				'pageSize' => $pageSize,
				'requestedPage' => ($requestedPage == 0 ? 0 : ($requestedPage - 1)),
				'pagingSize' => $pagingSize,
				'qs_pagenumber' => $qs_pagenumber,
			);
		}
		// return array with values (default at the least, or class arguments)
		return $searchArguments;
	}
	
	private $solrInstance;
	protected function SolrInstance()
	{
		if (empty($solrInstance)) {
			// new SOLR class object using above const's
			$solr = new Apache_Solr_Service( EG_WP::SOLR_HOST, EG_WP::SOLR_PORT, EG_WP::SOLR_PATH );
			// test SOLR service
			if (!$solr->ping()) {
				throw new Exception('EG_WP: Solr service not responding.');
			} else {
				$solrInstance = $solr;
			}
		}
		return $solrInstance;
	}
	
	public function ResultCount() 
	{
		// get the SOLR instance
		$solr = $this->SolrInstance();
		// get the search arguments
		$search = $this->SearchArguments();
		$query = $search['searchTerm'];
		$additionalParameters = $search['additionalParameters'];
		// execute search
		$res = $solr->search($query, 0, -1, $additionalParameters);
		
		return $res->response->numFound;
	}
	
	public function PagingResult()
	{
		$search = $this->SearchArguments();
		$count = intval($this->ResultCount()) - 1;
		
		$qs_pagenumber = $search['qs_pagenumber'];
		$pagingSize = intval($search['pagingSize']);
		$pageSize = intval($search['pageSize']);
		$requestedPage = intval($search['requestedPage']);
		$computedPage = intval($search['requestedPage']) + 1;
		$offsetSize = $requestedPage * $pageSize;
		
		$varDivided = ($count / $pageSize);
		$totalPages = ceil($varDivided);
		
		$ceilPagingSize = ceil( $pagingSize / 2 );
		$floorPagingSize = floor( $pagingSize / 2 );
		$lowerBound = max( 1, $requestedPage - $ceilPagingSize);
		$upperBound = min( $totalPages, $requestedPage + $floorPagingSize);
		
		if ($computedPage == 1) {
			$previousPage = 1;
			$hasPreviousBtn = false;
		} else {
			$previousPage = $computedPage - 1;
			$hasPreviousBtn = true;
		}
		if ($computedPage == $totalPages) {
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
			if ( $lb != 1 ) {
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
			if ( $ub != $tp ) {
				$pagingDiv .= '...&nbsp;&nbsp;<a href="?'.$qs_pagenumber.'='.$tp.'">'.$tp.'</a>';
			}
			if ( $hasNextBtn ) {
				$pagingDiv .= '&nbsp;&nbsp;<a href="?'.$qs_pagenumber.'='.$nextPage.'" class="eg-paging-next">Next >></a>';
			}
		}
		$pagingDiv .= '</div>';
		
		return array( 
			'offsetSize' => $offsetSize,
			'pageSize' => $pageSize,
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
			'searchTerm' => $search['searchTerm'],
			'prettySearchTerm' => $search['prettySearchTerm'],
			'additionalParameters' => $search['additionalParameters'],
		);
	}
			
	public function ExecuteSearch()
	{
		// get the SOLR instance
		$solr = $this->SolrInstance();
		
		// get the search arguments
		$search = $this->SearchArguments();
		$pagingEnabled = $search['enablePaging'];
		
		// is paging enabled, if so get search options from that
		if ( $pagingEnabled ) {
			$paging = $this->PagingResult();
			$offset = $paging['offsetSize'];
			$limit = $paging['pageSize'];
			$query = $paging['searchTerm'];
			$additionalParameters = $paging['additionalParameters'];
		// else get the default (or set) results sizes and parameters
		} else {
			$offset = $search['offset'];
			$limit = $search['results'];
			$query = $search['searchTerm'];
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