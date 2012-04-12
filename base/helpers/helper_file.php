<?php 
/**
* Helper file
* File functions
* 
* @author AntonioCS
* @version 1.0
*/

class helper_file {


	/**
	* Check if the given path exists and if it doesn't, if the create parameter is true, it will create it. 
	* If the create parameter is false it will return the path that failed
	* 
	* @param string $dir
	* @param bool $create
	*/
	public static function check_if_dir_exists($dir, $create = true) {			
		if (!is_dir($dir)) {
			$dirs = explode('/',self::nix_slashes($dir));
			$dir_current = '';
					
			foreach ($dirs as $d) {
				$dir_current .= $d . '/';
				
				if (!is_dir($dir_current)) {
					if ($create) 					
						mkdir($dir_current);				
					else
						return $dir_current;
				}
			}
		}
		return true;
	}


	/*
	* 
	*  If the second parameter is true it will return / at the end
	*/
	/*
	 public static function nix_slashes(&$dir) {
		   $dir = str_replace('\\','/',$dir);
	 }
	 //*/
	 /**
	 * Simple method to just change \ to / 
	 * 
	 * @param string $dir directory path to which perform the switch
	 * @param boolean $addfinalslash if this is set to true it will add an end slash (defaults to false)
	 * @return the $dir with the slashes reversed
	 */
	 public static function nix_slashes($dir,$addfinalslash = false) {
		$dir = str_replace('\\','/',$dir);
		return ($addfinalslash ? self::end_slash($dir) : $dir);
	 }
  
	 /**
	 * Simple method to check and add a final slash to the directory given
	 * 
	 * @param mixed $dir path given
	 * @return $dir with an end slash
	 */
	 public static function end_slash($dir) {
		if (substr($dir, -1) != '/')
		  $dir .='/';
		return $dir;
	 }
  
	 /**
	 * Method to delete all files in a directory and in subdirectories 
	 * 
	 * @param mixed $dir path to the directory to perform the mass delelete
	 * @param mixed $recursive if this is true any directories found will also be deleted
	 */
	 public static function delete_all_files($dir,$recursive = false) {
		
		self::end_slash($dir);  	 	
		$files = scandir($dir);
		//remove . and ..
		array_shift($files);
		array_shift($files);
	  
		foreach ($files as $file) {
		  $file = $dir . $file;
		  if (is_dir($file)) {
			 if ($recursive) {
				self::delete_all_files($file,true);
				rmdir($file);
			 }
		  }
		  else {
			 if (is_file($file))
				unlink($file);
		  }
		}
	 }
	 
	 public static function delete_given_files(array $files) {
		$count_del = 0;
		$count_files = count($files);
		
		$bad_f = array();
		
		
		foreach ($files as $file) { 
			if (file_exists($file) && unlink($file))
				$count_del++;
			else 
				$bad_f[] = $file;
		}
		
		return ($count_del == $count_files) ? true : $bad_f;
	}

	//function made by zcherus - #php channel freenode
	//Returns a human readable size 
	public static function sizehmr($size){        
		$i=0;
		$iec = array('B', 'KB','MB','GB','TB','PB','EB','ZB','YB');
		while (($size/1024)>1) {
			$size=round(($size/1024), 2);
			$i++;
		}
		return substr($size,0,strpos($size,'.')+4) . " " .$iec[$i];
	}
    
    
      /**
     * Convert a shorthand byte value from a PHP configuration directive to an integer value
     * @param    string   $value
     * @return   int
     * 
     * From: http://www.php.net/manual/en/faq.using.php#78405 (whith a few changes)
     */
    public static function convertBytes($value) {
        if (is_numeric($value)) {
            return $value;
        } 
        else {            
            $qty = substr($value,0,-1);
            $unit = strtolower(substr($value,-1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;
                break;
                case 'm':
                    $qty *= 1048576;
                break;
                case 'g':
                    $qty *= 1073741824;
                break;
            }
            return $qty;
        }
    }
	 
	/**
	 * Funcao par me facilitar a vida quando meto ficheiros no servidor
	 * 
	 * Parametros
	 * 		@param string $Fname - Nome do Input do Ficheiro
	 * 		@param string $path - Caminho para onde deve ir a imagem
	 * 		@param string $filename - Nome dado ao ficheiro apos ser transferido para o ficheiro. Valor default = NULL. Se o valor for null, a imagem no servidor tera o memo nome do ficheiro original
	 * 		@param string $widthHeight - Largura e altura. Valor default = NULL. A largura e altera deve ser passado no formato LarguraxAltura, e pode ser adicionado os parametros para o Image Magick (!,>,<). Se o IM ñ for usado é depois retirado
	 * 		@param mixed $extensoes - Extensoes permitdas para o ficheiro. Valor default = NULL. Poder ser um array com extensoes ou so uma extensao
	 * 		@param bool $thumb - Se quisermos um thumbnail pomos isto em 1. Valor default = 0
	 * 		@param string $widthHeight_thumb - Medidas do thumbnail. Valor default = NULL. � igual as medias da imagem normal
	 * 		@param string $thumb_ext - Valor a por na thumbnail. Valor default = NULL. Vai por o valor dado antes da extensao, ou seja, teste.jpg ficaria teste_t.jpg se for dado o _t
	 * 		@param bool $usarIM - Aqui dizemos se vamos usar o Image Magick ou �. Valor default = 0 (� usamos o IM)
	 * 
	 * Valores retornados pela funcao
	 * 		-1 Nao tem extensao
	 * 		-2 Extensao invalida segundo as extensoes dadas
	 * 		-3 Erro no programa convert (IMAGE MAGICK)
	 * 		-4 Tipo de extesao n suportada pelo PHP (funcoes de imagens do php)
	 * 		-5 Falho a abertura da imagem para processar com as fun�oes de imagem do phg
	 * 		-6 Erro ao tentar redimensionar a imagem (funcoes de imagens do php)
	 * 		-7 Erro ao tentar guardar a imagem (funcoes de imagens do php)
	 * 		-8 Erro ao criar o thumbnail
	 * 		-9 Erro ao guardar o thumbnail
	 * 
	 * 		-10 Erro ao mover o ficheiro
	 * 		-11 O ficheiro n existe (possivelmente n foi dado ficheiro)
	 *	Se tudo correu bem, retorna o caminho completo para a imagem. Se for tambem pedido o thumbnail, retorna um array em que a 1º casa é o caminho para a imagem e a 2º é para o thumbnail
	 *
	 * Possivel maneira de fazer o debug
	 *		$res = FileHandler(....);
			switch ($res) {
				case -1: 
					//Nao tem extensao
					break;
				case -2: 
					//Extensao invalida segundo as extensoes dadas
					break;
				case -3: 
					//Erro no programa convert (IMAGE MAGICK)
					break;
				case -4: 
					//Tipo de extesao n suportada pelo PHP (funcoes de imagens do php)
					break;
				case -5: 
					//Falho a abertura da imagem para processar com as fun�oes de imagem do php
					break;
				case -6: 
					//Erro ao tentar redimensionar a imagem (funcoes de imagens do php)
					break;
				case -7: 
					//Erro ao tentar guardar a imagem (funcoes de imagens do php)
					break;
				case -8: 
					//Erro ao criar o thumbnail
					break;
				case -9: 
					//Erro ao guardar o thumbnail
					break;					  
				case -10: 
					//Erro ao mover o ficheiro
					break;
				case -11: 
					//O ficheiro n existe					
					break;
				default: //Nao ocorreu erro nenhum :)
			}
			
			ou entao
			
			$res = FileHandler(....);
			if (is_numeric($res))	//erro
				codigo_de_erro;
			else //correu tudo bem
				codigo;
			
	 ******************************************************/
		
	public static function FileHandler($Fname,$path,$filename = NULL,$widthHeight = NULL,$extensoes = NULL,$thumb = false,$widthHeight_thumb = NULL,$thumb_ext = NULL,$usarIM = false) {
		$caminhos = array();	
		//vamo ver se o ficheiro existe
		if (isset($_FILES[$Fname]['tmp_name']) && $_FILES[$Fname]['tmp_name'] != "") {			
			//Vamos buscar a extensao
			/*if (preg_match("/\.(\w{3})$/",basename($_FILES[$Fname]['name']),$ext)) 			
				list(,$realext) = $ext;	//o 2º vai ser so a extensao sem o .			
			else
				return -1; //O ficheiro n tem extensao é impossivel ser valido*/
			
			$path_parts = pathinfo($_FILES[$Fname]['name']);
			$realext = $path_parts['extension'];
			$Fname = $path_parts['filename'];
			
				
			//$ext = explode(".",basename($_FILES[$Fname]['name']));
			//$realext = $ext[count($ext) -1];
						
			if ($extensoes) {//Vamos ver se temos que confirmar alguma extensao			
				if (is_array($extensoes)) {
					if (!in_array($realext, $extensoes)) 
							return -2;
				}							
				else 
					if ($realext != $extensoes)
						return -2;
			}
			
			if (!isset($filename)) 
				$filename = $Fname;
			else 
				if (!preg_match("/\.(\w{3})$/",$filename))  //O nome da imagem dada pelo utilizador n tem extensao
					$filename .= "." . $realext; //Vamos entao por a extensao do ficheiro original							
					
			$filename = helper_strings::replletras($filename,true); //Temos que fazer isto em caso do utilizador por nomes com acentos e etc (o que lixa o sistema todo)
			$path_e_ficheiro = $path . $filename;
			
			
			//Vamos mover o ficheiro
			if (@move_uploaded_file($_FILES[$Fname]['tmp_name'], $path_e_ficheiro)) {				
				if ($thumb) {//vamos ja criar o nome e o caminho para o thumbnail caso seja pedido
					$temp_ext = explode(".",$filename);
					$temp = $thumb_ext . "." . array_pop($temp_ext);							
										
					$thumb_new_ext = implode(".",$temp_ext) . $temp; //eu faço assim em caso de haver mais q 1 . 
					
					$thumb_caminhotodo = $path . $thumb_new_ext;
				}

				if (isset($widthHeight)) { //Se ñ existir ñ vale apena vir para aqui
					if ($usarIM) { //Vamos usar o image magik
						
						exec("convert \"" . $path_e_ficheiro . "\" -resize  \"" . $widthHeight . "\" -quality 100 \"" . $path_e_ficheiro ."\"",$out,$ref);
						if ($ref) 
							return -3;
						$caminhos[] = $path_e_ficheiro;
						
						if ($thumb) {										
							exec("convert \"" . $path_e_ficheiro . "\" -thumbnail  \"" . $widthHeight_thumb . "\" -quality 100 \"" . $thumb_caminhotodo ."\"",$out,$ref);
							if ($ref) 
								return -3;
							
							$caminhos[] = $thumb_caminhotodo;
						}
						
						
					}
					else { //vamos usar as funções do php						
						switch ($realext) {	//Vamos ver se esta extensao � suportada pelo php						
							case 'gif':
								if (!(imagetypes() & IMG_GIF)) 
									return -4;
								break;
							case 'jpg':
								if (!(imagetypes() & IMG_JPG)) 
									return -4;
								break;
							case 'png':
								if (!(imagetypes() & IMG_PNG)) 
									return -4;
								break;
							default:
								return -4;											
						}
												
						list($width, $height,$type) = getimagesize($path_e_ficheiro); //Vamos buscar o tamanho da imagem
						list($img_width,$img_height) = explode("x",str_replace(array("!","<",">"),"",$widthHeight));						
						
						
						ini_set("memory_limit","256M"); //so para o caso da imagem ser super hiper mega grande
						$im = imagecreatefromjpeg($path_e_ficheiro); //Vamos buscar a imagem
		
						if (!$im) //Falhou
							return -5;
						
						//Vamos ver se é necessario redimensionar a imagem q nos foi dada
						if ($width > $img_width || $height > $img_height) {		
							$imagem_nova = imagecreatetruecolor($img_width,$img_height);
							
							if (!imagecopyresampled($imagem_nova, $im, 0, 0, 0, 0, $img_width, $img_height, $width, $height)) 
								return -6;
										
							if (!saveImgdaTela($imagem_nova,$realext,$path_e_ficheiro))
								return -7;
								
							$caminhos[] = $path_e_ficheiro;
								
						}
						
						//Se for preciso um thumbnail	
						if ($thumb) {
							if (!$widthHeight_thumb) 
								$widthHeight_thumb = '150x150';							
							list($thumb_width,$thumb_height) = explode('x',str_replace(array('!','<','>'),'',$widthHeight_thumb));													
							$thumb_tela = imagecreatetruecolor($thumb_width,$thumb_height);
									
							//Parte do thumbnail
							if (!imagecopyresampled($thumb_tela, $im, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height)) 
								return -8;
							//Vamos agora ver e conseguimos guardar o thumbnail
							if (!saveImgdaTela($thumb_tela,$realext,$thumb_caminhotodo))
								return -9;	
								
							$caminhos[] = $thumb_caminhotodo;
												
						}
							
					}
				}
				
			}
			else 
				return -10;		
		}
		else 
			return -11;
		
		
		//n houve erros
		if ($thumb) //Retornamos o array todo se tivermos os 2 caminhos, ou seja se o thumb for pedido
			return $caminhos;
		return $caminhos[0]; //Retornamos so a string com o caminho para a imagem
		
		
	}
	
	//Pequena funcao para usar um determinado metodo para guardar a 'tela' (funcoes de imagem do php)
	private static function saveImgdaTela($tela,$ext,$caminho) {
		switch ($ext) {
			case "gif":
					$val = imagegif ($tela,$caminho);
				break;
			case "jpg":
					$val = imagejpeg($tela,$caminho);
				break;
			case "png":
					$val = imagepng($tela,$caminho);
				break;																
		}
		return $val;
	}
	

	//Simple method to force download of a file
	//From http://www.engineeringblog.indiabtech.com/2009/09/php-code-snippet-for-downloading-files/
	public static function force_download($file, $filename = null) {
		if (file_exists($file)) {
		   header('Content-length: '.filesize($file));
		   header('Content-Type: application/octet-stream');
		   header('Content-Disposition: attachment; filename="' . ($filename ? $filename : $file) . '"');
		   readfile($file);
		}		
	}  
}