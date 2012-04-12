<?php


class pager_mysql extends pager_base {
	
	
	public function init() {
		
	}
	
	protected function pagerQuery() {
		$q = 'SELECT * FROM ' . $this->_tablename;
		
		$l = (($this->itemsper_page) ? ' LIMIT ' . $this->offset . ',' . $this->itemsper_page : null); 
		
		if (!empty($this->settings['filters'])) 
			$q .= ' ' . $this->filterQueryBuilder($this->settings['filters']);								
		
		return $q . $l;
	}
}