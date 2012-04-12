<?php
/**
* Helper file
* XML functions
* 
* @author AntonioCS
* @version 1.0
*/
class helper_xml {
	/**
	 * Create a xml file 
	 *
	 * @param unknown_type $file
	 * @param unknown_type $root
	 * @return unknown
	 */
	public static function xmlcreate($file,$root = 'root') {		
		$dom = new DOMDocument('1.0', 'utf-8');
		$element = $dom->appendChild(new DOMElement($root));

		$xml = $dom->saveXML();
		file_put_contents($file,$xml); 
		return $xml;
	}
	
	/**
	 * Save the given xml object to a file
	 *
	 * @param SimpleXMLElement $xml 
	 * @param string $file - Path to the file
	 * @return mixed The return value of file_put_contents
	 */
	public static function xmltofile(SimpleXMLElement $xml,$file) {
		//with this code I get the xml formated
		$doc = new DOMDocument('1.0');
		$doc->formatOutput = true;
		$domnode = dom_import_simplexml($xml);
		
		$domnode = $doc->importNode($domnode, true);		
		$doc->appendChild($domnode);

		//<![CDATA[ ]]> replace
		//Don't know why but the < and > from the cdata are getting replaced with there htmlentities		
		$xml_code = str_replace(
						array('&lt;![CDATA[',']]&gt;'),
						array('<![CDATA[',']]>'),		
						$doc->saveXML()
					);
		
		return file_put_contents($file,$xml_code);				
	}
	
	/**
	 * Generate a xml file from a database table
	 * The resource is passed to the table (the result from select * from bla)
	 *
	 * @param resource $dbresource
	 * @param string $xmlfile
	 * @param string $main_itemname
	 * @param string $root
	 */
	public static function xmlfromtable($dbresource,$xmlfile,$main_itemname,$root = 'Table') {		
		//Let's always recreate the file
		$xml = simplexml_load_string(self::xmlcreate($xmlfile,$root),'SimpleXMLElement',LIBXML_NOCDATA);	
		//LIBXML_NOCDATA this will make simplexml ignore any <![CDATA[...]]> ‘escaped’ content
			
		$row = 1;
		while ($res_ = $dbresource->fetch_assoc()) {
			$mitem = $xml->addChild($main_itemname . '_' . $row);
			foreach ($res_ as $itemname => $value)							
				$mitem->addChild($itemname,self::xmlCdata($value));

			$row++;
		}
							
		self::xmltofile($xml,$xmlfile);
		return $xml;
	}
	
	/**
	 * Returns value encapsulated with <![CDATA[ ]]>
	 *
	 * @return mixed The encapsulated data
	 */
	private static function xmlCdata($value) {
		return $value ? "<![CDATA[$value]]>" : 'null';
	}
	
	/**
	 * Simples function to display the contents of a SimpleXMLElement on to the screen
	 *
	 * @param SimpleXMLElement $xml
	 */
	public static function xmlshow(SimpleXMLElement $xml) {
		echo '<pre>';
		print_r($xml);
	}
	
	/**
	 * Convert an xml file to an array from a given element in the xml file (defaults to items)
	 *
	 * @param string $file
	 * @param string $element
	 * @return Array
	 */
	public static function xmltoarray($file,$element = 'items') {      
		$xml = simplexml_load_file($file,'SimpleXMLElement',LIBXML_NOCDATA);
		
		return self::_xmltoarray($xml->$element);
	}
	
	/**
	 * This is what really creates the array
	 * Had to separete so that I could use recursion
	 *
	 * @param SimpleXMLElement $xml
	 * @return Array - Containing all the xml
	 */
	private static function _xmltoarray(SimpleXMLElement $xml) {
		$xmlarray = null;
		$pattern = '/^_(\d+)_$/';
		$num = null;				
		
		foreach ($xml->children() as $item => $value) {				
			if ($value->children()) //check to see if it has children
				$value = self::_xmltoarray($value);
			else
				$value = sprintf('%s',$value); //must use sprintf so I call the __toString method and get the actual value and not another SimpleXML object
				
			if (preg_match($pattern,$item,$num)) 
				$item = $num[1]; //This will give me the number			
				
			$xmlarray[$item] = $value;
		}
		return $xmlarray;	
	}
	
	/**
	 * Converts an array to a xml file. All numeric index will be set to _Num_
	 *
	 * @param array $data
	 * @param unknown_type $file
	 * @return numeric - The result of the xmltofile function
	 */
	public static function arraytoxml(array $data,$file,$elementname = 'items') {
        $xml = simplexml_load_string(self::xmlcreate($file)); //we will always create the file		
		if (!isset($xml->$elementname))
			$xml->addChild($elementname);				
		//Now let's add the data
		self::_arraytoxml($xml->$elementname,$data);	
			
		//print_r($xml);
		return self::xmltofile($xml,$file);		
	}
	//
	/**
	 * This is what really creates the array
	 * I had to seperate it so that I could use recursion :)
	 * The values of the array, might be values them selfves
	 *
	 * @param SimpleXMLElement $xml
	 * @param mixed $data
     * @return void
	 */
	private static function _arraytoxml(SimpleXMLElement &$xml,$data) {        
		foreach ($data as $k => $value)	{
			if (is_numeric($k))
				$k = '_' . $k . '_'; //xml does not allow elements to just be numeric			
			if (!isset($xml->$k))
				$xml->addChild($k);			
			if (is_array($value))
				self::_arraytoxml($xml->$k,$value);
			else 
				$xml->$k = self::xmlCdata($value);
		}				
	}	
}

?>