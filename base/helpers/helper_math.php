<?php
class helper_math {
	
	//Simple way to determine if a number is odd (from the comments on bitwise on the php manual)
	public static function isOdd($x){
		return ($x & 1);
	} 
	
	public static function primeGenerator($limit = 10000) {
		$primes = array(2);
		$sievebound = ($limit)/2;
		$sieve[$sievebound];
		for ($i = 0;$i<$sievebound;$i++)
			$sieve[$i] = false;

		$crosslimit = (sqrt(limit) - 1)/2;

		for ($i = 1;$i<=$crosslimit;$i++) {
			if (!$sieve[$i]) {
				for ($m = 2*$i*($i+1);$m<$sievebound;$m+=2*$i+1)
					$sieve[$m] = true; //estamos a cortar multiplos
			}
		}
		
		for ($i = 0;$i<=$sievebound;$i++) 
			if (!$sieve[$i])
				$primes[] = 2*i+1;
		
	}        
	/**
	* Method to determine if a given number is prime or not
	* 
	* @param mixed $num number to analyse
	* @return bool true if number given is prime false otherwise
	*/
	public static function isPrime($num) {
		if ($num == 1)
			return false;
		if (num < 4) //2 e 3 sao primos
			return true;
		if ((num % 3) == 0) //é divisivel por 3 n é primo - Em termos de velocidade isto corta para metade
			return false;

		$max = (int)sqrt(num); //vamos so ate a raiz quadrada do numero em questao
		$divisivel = 0;
		for ($i = 1;$i <= $max;$i += 2) {//execution time : 1.312 s - Numeros pares nunca sao primos tirando o 2
			if (($num % $i) == 0)             	
				if (++$divisivel > 1) //vamos so ver se é divisivel por 1, pk estamos a ir ate a raiz quadrada do numero
					return false;
		}
		return true;
	}

	//Quando um numero leva ! no fim é um factorial
	//Exemplo: 5! = 1 x 2 x 3 x 4 x 5 = 120
	public static function factorial($num) {
		 $i = 1;
		 while ($num > 1)
			$i *= $num--;
		 return $i;
	}
	
}
?>
