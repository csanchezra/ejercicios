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
      }
      elseif ($i == 2) $this->str_1 = $get;
      elseif ($i == 3) $this->str_2 = $get;
      elseif ($i == 4) $this->message = $get;

      $i++;
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
    }
  }

  private function find_text($message, $string, $length)
  {

    $text = "NO";

    /*
    Se valida que el la cadena se encuentra en el mensaje 
    y que la cadena tenga la longitud especificada
    */
    if (str_contains($message, $string) && ($length >= 2 && $length <= 50))
    {
      $text = "SI";
    }

    $this->save_result($text);

    return $text;
  }

  private function save_result($string)
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
$encrypt->read_file($file);
$encrypt->find_coincidences();
