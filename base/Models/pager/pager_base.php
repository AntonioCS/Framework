<?php


abstract class pager_base extends acs_model {
	
	protected $settings;
	
	protected $total_items = 0;
	
	protected $total_pages = 0;
	protected $current_page = 0;
	protected $itemsper_page = 0;
	protected $offset = 0;
	
	protected $pagerData = null;
	protected $isData = false;
    
    protected $beforeAdd = array();
	
	/**
	 * Settings given from the main pager
	 * Array format: 
 	 * 	'datasource' => string,		
		'itemsperpage' => int,
		'currentpage' => int,
		'templates' => array(
						'page' => $this->_main,
						'page_item' => $this->_item,
						'page_numerations' => $this->_numeration
				)
		 
	 *
	 * @param array $settings
	 */
	public function setSettings($settings) {
		$this->settings = $settings;		
		
		$curpage = $this->getCurrPage();
        //$this->settings['currentpage'] -1; 
		$this->itemsper_page = $this->settings['itemsperpage'];
		
		$this->setTotalItems();		
		
		if ($this->itemsper_page) {
			$this->total_pages = ceil($this->total_items / $this->itemsper_page);
			$this->current_page = $curpage+1;
			
			$this->offset = ($curpage)* $this->itemsper_page;
		}
		//hook - Prevent from reimplementing all of the setSettings
		
		$this->after_settings();
		
		return $this;
	}
    
    private function getCurrPage() {
        $c = $this->settings['currentpage'];
        $page = 0;
        
        if (!$c) {
            $page = acs_router::getInstance()->fetchParameter($this->settings['pageparam']);
            if (!$page)
                $page = 0;
            else
                $page -=1;
        } 
        else
            $page = $c - 1;
        
        return $page;    
    }
	
	protected function after_settings() {
		
		//$this->setTotalItems();		
	}
	/**
	 * Return processed filter parameters
	 * 
	 * @param array $filtersettings
	 * @param bool $onlyconditions - To prevent adding group by and filter by when it's not needed (to get the total)
	 */
	protected function filterQueryBuilder($filtersettings,$onlyconditions = false) {
		$q = array();
		if (isset($filtersettings['where'])) {
			foreach ($filtersettings['where'] as $k => $condition) {
				
				if ($k > 0) {
					if (is_array($condition))
						$q[] = 'OR ' . $condition[0];
					else 
						$q[] = 'AND ' . $condition;
				}
				else
					$q[] = 'WHERE ' . (is_array($condition) ? $condition[0] : $condition);
			}
		}
		if (!$onlyconditions) {
			if (isset($filtersettings['groupby'])) {
				$q[] = 'GROUP BY ' . implode(',',$filtersettings['groupby']);		
			}
			
			if (isset($filtersettings['orderby'])) {
				$o = null;
				if (!empty($filtersettings['orderby']['asc'])) {
					$o = implode(',',$filtersettings['orderby']['asc']);
				}
				elseif (!empty($filtersettings['orderby']['desc'])) {
					$o = implode(',',$filtersettings['orderby']['des']) . ' DESC';
				}
				
				$q[] = 'ORDER BY ' . $o;
			}
		}
		
		return implode(' ',$q);
	}
	
	/**
	 * To be overriden in the correct pager	 
	 */
	protected function pagerQuery() {}
	
	/**
    * Data set for the paginations
    * 
    */
	protected function pagerDataSet() {	
					
		$this->query($this->pagerQuery());
		
		if ($this->ifresult()) 
			$this->isData = true;		
	}
	/**
    * While this is true there is data to be paginated
    * 
    */
	protected function pagerData() {
		return $this->fetch_result_assoc();				
	}
	
	/**
	 * 
	 * Return the pager settings
	 * 
	 */
	public function show() {		
					
		if (!$this->total_items)
			return ;
		
		$this->pagerDataSet();

		if ($this->isData) {
			$page = new acs_view($this->settings['templates']['page']);
			$page_item = new acs_view($this->settings['templates']['page_item']);
			
			$items = array();
			$items_count = 0;
								
			while (($res = $this->pagerData())) {

				$this->beforeAddRes($res);
                
				$res['items_count'] = ++$items_count;
				
				$items[] = $page_item->addData($res)->returnRender();
			}
						
			$page->body = implode("\n",$items);
            $page->page_numbers = $this->getPageNumbers();
            
			return $page->returnRender();
		}

		return null;
	}
	
	/**
	 * To edit the result before adding it to the view
	 * @param $res
	 */
	protected function beforeAddRes($res) {        
    }
	
	protected function pre_render(&$page,&$items) {}
	
	protected function pagination() {
		$pagesnumbers = $this->getPageNumbers();
	}
	
    /**
    * Calculate the pagination
    * 
    */
	protected function getPageNumbers() {
		$total_pages = 0;
        
		if ($this->itemsper_page)
            //$total_pages = (int)($this->total_items / $this->itemsper_page); //This was giving incorrect paginations
            $total_pages = ceil($this->total_items / $this->itemsper_page);    
			//$total_pages = round($this->total_items / $this->itemsper_page);  //This was giving incorrect paginations
		
		if ($total_pages <= 1)
			return;
            

		$pages = range(1,$total_pages);
		
		if ($this->settings['baseurl']) {
        
            $pageNumberHolder = new acs_view($this->settings['templates']['pageNumberHolder']);
                      
            $pageNumber = new acs_view($this->settings['templates']['pageNumber']);
            $pageNumberSel = new acs_view($this->settings['templates']['pageNumberSel']);
            
			$pages_ = array();
			foreach ($pages as $page) {
				if ($page != $this->current_page) {
					$link = $this->settings['baseurl'] . '/' . $this->settings['pageparam'] . '/' . $page;
                    //str_replace('[num]',$page,$this->settings['pagemumlinkformat']);
                    
                    $pageNumber->link = $link;
                    $pageNumber->number = $page;
                    
                    $pages_[] = $pageNumber->returnRender();					
				}
				else {
                    $pageNumberSel->number = $page;
					$pages_[] = $pageNumberSel->returnRender();
                }
			}
			
			$pages = $pages_;
		}
		
		return implode("\n",$pages);
	}
	
	protected function setTotalItems() {
		
		$this->change_table($this->settings['datasource']);
						
		$f = null;
		if (!empty($this->settings['filters'])) 
			$f = $this->filterQueryBuilder($this->settings['filters'],true);
		
		$total = $this->total($f);
		$this->total_items = $total;		 			
	}
}
