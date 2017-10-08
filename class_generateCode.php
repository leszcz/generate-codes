<?php
/* 
 * @file class_generateCode
 * @desc class to generate codes
 * @author Tomasz Leszczynński <tomekleszczynski94@gmail.com>
 * @url https://github.com/leszcz/generate-codes
 * @version 1.0
 */
 
class generateCode 
{
	/* Cache folder localization */ 
	private $cacheLocalization = "cache";
	
	/* Generating random string (code)
	 * @params $length - length of a generated code
	 * @return Generated code
	 */
	 
	private function generateString($length = 6)
	{
    	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$charsLength = strlen($chars);
    	$string = '';
		
    	for ($i = 0; $i < $length; $i++) {
        	$string .= $chars[rand(0, $charsLength - 1)];
    	}
    	return $string;
	}
	
	/* Console mode (PHP-CLI) to generate codes
	 * @params array from user console
	 * @return Filename
	 */
	 
	public function cli( array $params )
	{
		$checkParamsAndSave = $this->checkParamsAndSave( getopt($params) );
		return "Nazwa pliku: ".$checkParamsAndSave;

	}
	
	/* Form in web browser (PHP-CGI) to generate codes
	 * @params array $_POST
	 * @return URL to file or web form
	 */
	 
	public function cgi( array $params )
	{
		if( isset($params['s']) )
		{
			$checkParamsAndSave = $this->checkParamsAndSave( $params );
			
			return '<a href="'.$checkParamsAndSave.'" title="Lista kodów" alt="Lista kodów">Pobierz liste kodów</a>';
		} else {
			return $this->getForm();
		}
	}
	
	/* Function to generate a list of codes
	 * @params length and count of generated codes
	 * @return list of codes
	 */
	 
	private function generateCodes( $length = 6, $count = 100 )
	{
		$codes = [];
		for($i = 0; $i<$count; $i++)
		{
			$code = $this->generateString($length);
			if(!in_array($code, $codes))
			{
				$codes[] = $code;
			}
		}
		return $codes;
	}
	
	/* Save file with generated codes
	 * @params array $_POST
	 * @return URL to file or form
	 */
	 
	private function saveFileWithCodes( $filename, $count, $length )
	{
		$checksum = md5($length.$count);
		$cache = $this->cacheLocalization."/".$checksum;
		
		if( !file_exists($filename) )
		{
			$getCodes = $this->generateCodes($length, $count); // generate Codes

			if( file_exists($cache) ) {
				$saveFile = file_put_contents($filename, file_get_contents($cache), FILE_APPEND); // save file with cache content
			} else if( !file_exists($cache) ) {
				$saveFile = file_put_contents($filename, implode(PHP_EOL, $getCodes), FILE_APPEND); // save file with generated codes
				$saveCache = file_put_contents($cache, $filename, FILE_APPEND); // save cache file
			} 
			
			if( !$saveFile || !$saveCache ) {
				die("Błąd zapisu pliku");
			} else {
				return $filename;
			}
		}
	}
	
	/* Checking parameters and save a file
	 * @params array from CGI / CLI 
	 * @return file
	 */
	 
	private function checkParamsAndSave( array $params ) 
	{
		if( !is_array($params) )
			die("Parametry nie zostały dostarczone prawidłowo. Potrzebna jest tablica [array]");
		
		$length = intval($params['l']);
		$count = intval($params['c']);
		$filename = basename($params['f']);

		if( $length == 0 || $count == 0 || empty($filename) ) {
			echo "Popraw parametry!";
		} else {
			return $this->saveFileWithCodes($filename, $count, $length);
		}
	}
	
	/* Open form in web browser (PHP-CGI) to generate codes */
	
	protected function getForm()
	{
		return '
			<form action="'.$_SERVER['PHP_SELF'].'" method="POST">
				<label for="c">Ilość kodów</label>
				<input type="number" name="c" min="1" max="100000"><br />
				<label for="l">Długość kodów</label>
				<input type="number" name="l" min="1" max="10"><br />
				<label for="f">Nazwa pliku:</label>
				<input type="text" name="f" placeholder="kody.txt"><br />
				<input type="submit" name="s" value="Wygeneruj i zapisz">
			</form>';
	}
}