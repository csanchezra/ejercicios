<?php

class Enc
{
  public $m1;
  public $m2;
  public $n;
  public $str_1;
  public $str_2;
  public $message;

  public function __construct()
  {
    $this->m1 = 0;
    $this->m2 = 0;
    $this->n = 0;
    $this->str_1 = '';
    $this->str_2 = '';
    $this->message = '';
  }


  public function read_file($file)
  {
    $archivo = fopen($file, "r");
    $i = 1;

    while (!feof($archivo))
    {
      $get = trim(fgets($archivo));

      if ($i == 1)
      {
        $parts = explode(" ", $get);
        $this->m1 = (int)$parts[0];
        $this->m2 = (int)$parts[1];
        $this->n = (int)$parts[2];

        if (!is_numeric($this->m1) || !is_numeric($this->m2) || !is_numeric($this->n)) // se valida que la entrada de la primera linea este compuesta solo por numeros
        {
          $file = fopen("result.txt", "w");
          fclose($file);
          $this->save_result("NO");
          $this->save_result("NO");
          exit();
        }
      }
      elseif ($i == 2) $this->str_1 = trim($get);
      elseif ($i == 3) $this->str_2 = trim($get);
      elseif ($i == 4) $this->message = trim($get);

      $i++;
    }

    if (strlen($this->str_1) != $this->m1 || strlen($this->str_2) != $this->m2 || strlen($this->message) != $this->n) // no concuerdan los datos especificados de longitud con los datos ingresados
    {
      $file = fopen("result.txt", "w");
      fclose($file);
      $this->save_result("NO");
      $this->save_result("NO");
      exit();
    }

    fclose($archivo);

    $file = fopen("result.txt", "w");
    fclose($file);
  }

  public function find_coincidences()
  {
    $sus = "$1";
    $this->message =  preg_replace('/(.)\1+/mi', $sus, $this->message); // eliminamos todas las caracteres simultaneos repetidos

    /*
    Se valida que la longitud del mensaje este en el rango especificado y que 
    se encuentre dentro de la expresión regular (solo letras y numeros)
    */

    if (($this->n >= 3 && $this->n <= 5000) && (preg_match("/^[a-zA-Z0-9]+$/", $this->message)))
    {
      /*
      Se valida que si se encontró la primer coincidencia, 
      si no se encuentra busca la segundo string
      */
      if ($this->find_text($this->message, $this->str_1, $this->m1) == "NO")
      {
        $this->find_text($this->message, $this->str_2, $this->m2);
      }
      else
      {
        $this->save_result("NO");
      }
    }

    else
    {
      $this->save_result("NO");
      $this->save_result("NO");
      exit();
    }
  }

  private function find_text($message, $string, $length)
  {

    $text = "NO";

    /*
    Se valida que el la cadena se encuentra en el mensaje 
    y que la cadena tenga la longitud especificada
    */

    // if (str_contains($message, $string) && ($length >= 2 && $length <= 50)) funcion utilizada desde PHP 8
    if (strlen($string) <= strlen($message) && strpos($message, $string) !== false && ($length >= 2 && $length <= 50) && (preg_match("/^[a-zA-Z0-9]+$/", $message))) // si la cadena es menor igual al mensaje, si se encuentra la cadena en el mesaje y si la cadena esta en un rango permitido de 2 a 50 caracteres, que la cadena a buscar solo cuente con letra y numeros
    {
      $text = "SI";
    }

    $this->save_result($text);

    return $text;
  }

  public function save_result($string)
  {
    $file = fopen("result.txt", "a");

    fwrite($file, $string . PHP_EOL);

    fclose($file);
  }
}

$argumentos = getopt("f:");

if (!isset($argumentos["f"]))
{
  exit("Modo de uso:
-f Ingrese archivo con extension .txt");
}
$file = $argumentos["f"];

if (!file_exists($file))
{
  exit("El archivo especificado no existe, asegurese de ingresar un archivo existente en la misma carpeta");
}

$encrypt = new Enc();

$lines = count(file($file));

// echo $lines;

if ($lines != 4) // el archivo debera contener 4 lineas
{
  $text = "NO";
  $file = fopen("result.txt", "w");
  fclose($file);
  $encrypt->save_result($text);
  $encrypt->save_result($text);
  exit();
}

$encrypt->read_file($file);
$encrypt->find_coincidences();
