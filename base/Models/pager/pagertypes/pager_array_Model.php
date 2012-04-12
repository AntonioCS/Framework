<?php


class pager_array extends pager_base {
	
	private $_items_returned = 0;
	
	protected function setTotalItems() {
		$this->total_items = count($this->settings['datasource']);
	}
	
	protected function pagerDataSet() {				
		$this->isData = !empty($this->settings['datasource']);
	}
	
	protected function pagerData() {
		if ($this->itemsper_page && ++$this->_items_returned > $this->itemsper_page)
			return null;
		
		//var_dump(key($this->settings['datasource']));
		//exit;
			
		$r = current($this->settings['datasource']);
		if ($r) {
			next($this->settings['datasource']);
			return $r;
		}
		
		return null;
	}
	
	protected function beforeAddRes(&$res) {
		$res = (array)$res;
	}
	
	protected function after_settings() {	
		
		if ($this->itemsper_page > 0) {			
			//set the correct data
			if ($this->current_page == 1)				
				return;

			//create a new array with the items to show for this page
			$keys = array_keys($this->settings['datasource']);
			$data = array();
			
			for ($i = $this->offset, $l = $i + $this->itemsper_page;$i < $l; $i++) {
				if (!isset($keys[$i]))
					break;
					
				$data[] = $this->settings['datasource'][$keys[$i]];
			}
			
			$this->settings['datasource'] = $data;			
		}
	}
}