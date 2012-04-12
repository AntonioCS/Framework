<?php

class helper_strings {

	  /**
	   1.
	  
   2.
	   * String word cut
   3.
	   *
   4.
	   * @private
   5.
	   */
	   /*
   6.
	  function _wpmutility_wordCut($content, $limit){
   7.
	  $content = explode(' ',$content);
   8.
	  for($i=0; $i<$limit; $i++) $summary[$i] = $content[$i];
   9.
	  $summary = implode(' ', $summary).'...';
  10.
	  return $summary;
  11.
	  }*/

	//In my opinion this is a better solution, both because you avoid the loop but it also takes into consideration that the content might be shorter than the limit:

	/*
	$contentTemp = explode(' ', $content); 
	if (count($contentTemp) > $limit) { 
	$content = implode(' ', array_slice($contentTemp, 0, $limit)) . '...'; } 
	return $content;
	*/

	/**
	* This method will return a string limited to the specified word count
	* 
	* @param mixed $text
	* @param mixed $limit - Default 10
	* @return string
	*/
	public static function wordcut($text, $limit = 10) {
		$contentTemp = explode(' ', $text); 
		//if (count($contentTemp) > $limit) 
		if (isset($contentTemp[$limit+1]))
			return implode(' ', array_slice($contentTemp, 0, $limit));
		return $text;				
	}

	/*
		FunÃ§Ã£o para substituir certos caracteres por outros
		$text = texto a ser alterado
		$remo_espaco = se isto for true os espaÃ§os sao tambem removidos (em caso de por algo tipo _ os espaÃ§os sao substituidos por _)
		
		A funÃ§Ã£o devolve o texto editado
		Exemplo: 
			echo replletras("sdfÃ§Ã©");
		
		Vai dar -> sdfce
	
	*/	
	/**
	 * This function will substitute characters with accents with a character with out it
	 * It can also remove spaces or replace them with what was specified in parameter $remo_espaco
	 * 
	 * @param string $text
	 * @param mixed $remo_espaco
	 * @return string
	 */
	public static function replletras($text, $remo_espaco = false) {	
		$muda = array(
					array('ã','á','à','â','å','ä'),'a',
					array('Ã','Á','À','Â','Å','Ä'),'A',
					array('é','è','ê','ë'),'e',
					array('É','È','Ê','Ë'),'E',
					array('ì','í','î','ï'),'i',
					array('Ì','Í','Î','Ï'),'I',
					array('ó','ò','ô','õ','ö','ø'),'o',
					array('Ó','Ò','Ô','Õ','Ö','Ø'),'O',
					array('ú','ù','û','ü'),'u',
					array('Ú','Ù','Û','Ü'),'U',
					array('ý','ÿ'),'y',
					'Ý','Y',
					'ç','c',
					'Ç','C',
					'æ','ae',
					'Æ','AE',
					'ñ','n',
					'Ñ','N'													
			);
		if ($remo_espaco) { //Desta maneira eu substituo o espaÃ§o com o q eu puser la
			$x = ($remo_espaco === true) ? '' : $remo_espaco;
			array_push($muda,' ',$x);			
		}

		for ($i = 0, $len = (count($muda) - 1); $i < $len;$i++) 				
			$text = str_replace($muda[$i],$muda[++$i],$text);		
		
		return $text;	
	}
	
	/**
	* This function might be faster than replletras
	* 
	* EDIT: With the new version it might be the same
	* 
	* @param string $str
	* @return string
	*/
	public static function remove_accents ( $str ) {
		$str = htmlentities(strtolower($str));		
		$str = preg_replace('/&(.)(acute|cedil|circ|ring|tilde|uml);/', "$1", $str);
		$str = preg_replace('/([^a-zA-Z0-9]+)/', ' ', html_entity_decode($str));
		$str = trim($str, '-');
		return $str;
	}
		
	/**
	 * With this function I can receive text with html tags and be certain that only the letters in this function will be changed
	 *
	 * @param string $text - MUST BE IN UTF-8 otherwise it might fail 
	 * @return string - Text with all the letters in the var $letters replaced with it's html entitie
	 */
	public static function htmlentities_specific($text) {		
		//-- This does not seem to be a good idea 
		//if (extension_loaded("mbstring")) //let's make usere that the text is in uft-8   
		//$text = mb_convert_encoding($text,"UTF-8");	  	  

		$letters = array(
			'ã','á','à','â','å','ä',
			'Ã','Á','À','Â','Å','Ä',					
			'é','è','ê','ë',
			'É','È','Ê','Ë',
			'ì','í','î','ï',
			'Ì','Í','Î','Ï',
			'ó','ò','ô','õ','ö','ø',
			'Ó','Ò','Ô','Õ','Ö','Ø',
			'ú','ù','û','ü',
			'Ú','Ù','Û','Ü',
			'ý','ÿ','Ý','ç','Ç',
			'æ','Æ','ñ','Ñ',
		);
		$entities = array(
				'&atilde;','&aacute;','&agrave;','&acirc;','&aring;','&auml;',
				'&Atilde;','&Aacute;','&Agrave;','&Acirc;','&Aring;','&Auml;',
				'&eacute;','&egrave;','&ecirc;','&euml;',
				'&Eacute;','&Egrave;','&Ecirc;','&Euml;',
				'&igrave;','&iacute;','&icirc;','&iuml;',
				'&Igrave;','&Iacute;','&Icirc;','&Iuml;',
				'&oacute;','&ograve;','&ocirc;','&otilde;','&ouml;','&oslash;',
				'&Oacute;','&Ograve;','&Ocirc;','&Otilde;','&Ouml;','&Oslash;',
				'&uacute;','&ugrave;','&ucirc;','&uuml;',
				'&Uacute;','&Ugrave;','&Ucirc;','&Uuml;',
				'&yacute;','&yuml;','&Yacute;','&ccedil;','&Ccedil;',
				'&aelig;','&AElig;','&ntilde;','&Ntilde;'
		);      
		$text = str_replace($letters,$entities,$text);  					
		return $text;			
	}
	
	/**
	* This method will return the percentage of caps in the given string
	* 
	* @param mixed $string
	* @return percentage of caps in given string
	*/
	public static function capspercentage($string) {
		$string = self::replletras($string);
		
		preg_match_all('/[A-Z]+/',$string,$caps);
		preg_match_all('/[a-z]+/',$string,$non_caps);

		$tcaps = strlen(implode('',$caps[0]));
		$tnon_caps = strlen(implode('',$non_caps[0]));

		if ($tcaps)
			return round((($tcaps / ($tcaps + $tnon_caps)) * 100),0);    
		return 0;
	}
	
	/**
	* Return last character of string
	* 
	* @param string $string
	* @return char
	*/
	public static function lastschar($string) {
		return $string[strlen($string) -1];	
	}	
}