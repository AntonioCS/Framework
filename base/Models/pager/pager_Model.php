<?php

class pager extends acs_base {
	
	/** 	 
	 * @var acs_db
	 */
	private $_conn = null;
	/**	 
	 * @var acs_session
	 */
	private $_session = null;
	
	/**
	 * @var string
	 */
	private $_datasource = null;
	/**
	 * @var int
	 */
	private $_itemsperpage = 10;
	/**
	 * @var int
	 */
	private $_currentpage = null;
	
	
	/**
	 * @var string
	 */
	private $_pagemumlinkformat = null;
	
	/**
	 * 
	 * @var string
	 */
	private $_pagertype = null;
	
	/**	 
	 * Database profile to use (to call the correct pager). Defaults to the default profile set
	 * @var string 
	 */
	private $_dbp = null;

	/**
	 * @var array
	 */
	private $_filters = array();
	
	/**
	 * @var array
	 */
	private $_templates = array(
								'main' => 'pager/page',
								'item' => 'pager/page_item',
								'numeration' => 'pager/page_numeration',
                                
                                'pageNumberHolder' => 'pager/page_numberholder',
                                'pageNumber' => 'pager/page_number',                                
                                'pageNumberSel' => 'pager/page_numbersel'
	);
    
    private $_baseurl = null;
    
    private $_pageparam = 'pagina';
    
	/**
	 * 
	 * Either return the pager type set by the user or the default type
	 */
	private function getPagerType() {
		if ($this->_pagertype)
			return $this->_pagertype;
		
		return $this->configData->dbp[$this->_dbp]['type'];
	}
	
	public function init() {	
		$this->_dbp = acs_config::getInstance()->dbp_default;
	}
	
	/**
	 * Set the type of pager (mysql, array, etc)
	 * 
	 * @param string $type
	 */
	public function setPagerType($type) {
		$this->_pagertype = $type;
		return $this;
	}
	
	public function AddFilter($filter) {
		$this->_filters['where'][] = $filter;
		return $this;		
	}

	public function AddFilterOr($filter) {
		return $this->AddFilter(array($filter)); //Add as array to differenciate between AND OR
	}
	
	public function AddFilterOrderBy($filter) {
		$this->_filters['orderby']['asc'][] = $filter;
		return $this;
	}
	
	public function AddFilterOrderByDesc($filter) {
		$this->_filters['orderby']['desc'][] = $filter;
		return $this;
	}
	
	public function AddFilterGroupBy($filter) {
		$this->_filters['groupby'][] = $filter;
		return $this;
	}
	
	public function setDataSource($data) {		
		$this->_datasource = $data;
		return $this;
	}
	
	/**
	 * If set to 0 all items are to be shown
	 * @param int $nitems
	 */
	public function setItemsPerPage($nitems) {
		$this->_itemsperpage = (int) $nitems;
		return $this;
	}
	
	public function setCurrentPage($page) {
		$this->_currentpage = (int) $page;
		return $this;
	}
    
    public function setBaseUrl($url) {
        $this->_baseurl = $url;
    }
    
    public function setPageParemeter($pagep) {
        $this->_pageparam = $pagep;
    }
	
	public function setPageNumLinkFormat($format) {
		$this->_pagemumlinkformat = $format;
		return $this;
	}
	
	public function setTemplate($template,$path) {
		if (isset($this->_templates[$template]))
			$this->_templates[$template] = $path;
			
		return $this;		
	}
	
	public function show() {
		$pagertype = $this->getPagerType();
		$thepager = 'pager/pagertypes/pager_' . $pagertype;
				
		require_once(dirname(__FILE__) . '/pager_base.php');
		return $this->loadmodel($thepager)->setSettings($this->getSettings())->show();
	}    
	
	/**
	 * Get all the data set
	 * 
	 */
	private function getSettings() {
		return array(				
					'datasource' => $this->_datasource,
					'itemsperpage' => $this->_itemsperpage,
					'currentpage' => $this->_currentpage,
					'filters' => $this->_filters,
					'pagemumlinkformat' => $this->_pagemumlinkformat,
					'templates' => array(
						'page' => $this->_templates['main'],
						'page_item' => $this->_templates['item'],
						'page_numerations' => $this->_templates['numeration'],
                        'pageNumberHolder' => $this->_templates['pageNumberHolder'],
                        'pageNumber' => $this->_templates['pageNumber'],
                        'pageNumberSel' => $this->_templates['pageNumberSel']
					),
                    'baseurl' => $this->_baseurl,
                    'pageparam' => $this->_pageparam
		);
	}
}
