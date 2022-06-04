<?php

class Enc
{
  public $m1;
  public $m2;
  public $n;
  public $str_1;
  public $str_2;
  public $message;
  public $total_mensajes;

  public function __construct()
  {
    $this->m1 = 0;
    $this->m2 = 0;
    $this->n = 0;
    $this->str_1 = '';
    $this->str_2 = '';
    $this->message = '';
    $this->total_mensajes = 0;
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

        $this->numeric($parts[0], "m1");
        $this->numeric($parts[1], "m2");
        $this->numeric($parts[2], "n");

        $this->m1 = (int)$parts[0];
        $this->m2 = (int)$parts[1];
        $this->n = (int)$parts[2];

        $this->longitud($this->m1, 2, 50, "m1");
        $this->longitud($this->m2, 2, 50, "m2");
        $this->longitud($this->n, 3, 5000, "n");
      }

      elseif ($i == 2) $this->str_1 = trim($get);
      elseif ($i == 3) $this->str_2 = trim($get);
      elseif ($i == 4) $this->message = trim($get);

      $i++;
    }


    if ($this->m1 == 0  || $this->m2 == 0  || $this->n == 0  || $this->str_1 == '' || $this->str_2 == '' || $this->message == '')
      $this->mensaje_error("La entrada del archivo no corresponde al especificado");

    if (!(preg_match('/^[a-zA-Z0-9]+$/', $this->message)))
    {
      $this->mensaje_error("El mensaje incluye caracteres inválidos, deberán ser [a-zA-Z0-9]");
    }

    $this->caracteres_duplicados($this->str_1, "1");
    $this->caracteres_duplicados($this->str_2, "2");

    if (strlen($this->str_1) != $this->m1 || strlen($this->str_2) != $this->m2 || strlen($this->message) != $this->n) // no concuerdan los datos especificados de longitud con los datos ingresados
    {
      $this->mensaje_error("La longitud especificada en los parametros de la primera linea no corresponden a las longitudes de las entradas");
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



    /*
      Se valida que si se encontró la primer coincidencia, 
      si no se encuentra busca la segundo string
      */

    $this->find_text($this->message, $this->str_1, $this->m1);
    $this->find_text($this->message, $this->str_2, $this->m2);

    if ($this->total_mensajes > 1)
      $this->mensaje_error("Se encontro mas de una intrucción en el mensaje");
  }

  private function find_text($message, $string, $length)
  {

    $text = "NO";

    /*
    Se valida que el la cadena se encuentra en el mensaje 
    y que la cadena tenga la longitud especificada
    */

    // if (str_contains($message, $string) && ($length >= 2 && $length <= 50)) funcion utilizada desde PHP 8
    if (strpos($message, $string) !== false) //  si se encuentra la cadena en el mensaje
    {
      $text = "SI";
      $this->total_mensajes++;
    }

    $this->save_result($text);

    return $text;
  }

  private function numeric($dato, $variable)
  {
    if (!is_numeric($dato))
    {
      $this->mensaje_error("El parámetro " . $variable . " no es númerico");
    }
  }

  private function longitud($dato, $lim_inf, $lim_sup, $variable)
  {
    if ($dato < $lim_inf)
    {
      $this->mensaje_error("El parámetro " . $variable . " es menor a " . $lim_inf);
    }
    elseif ($dato > $lim_sup)
    {
      $this->mensaje_error("El parámetro " . $variable . " es mayor a " . $lim_sup);
    }
  }

  private function caracteres_duplicados($dato, $variable)
  {
    $sus = "$1";
    $dato_limpio =  preg_replace('/(.)\1+/mi', $sus, $dato); // eliminamos todas las caracteres simultaneos repetidos
    if (strlen($dato) > strlen($dato_limpio))
      echo ("La instrucción " . $variable . " contiene caracteres repetidos simultaneos");
  }

  private function mensaje_error($mensaje)
  {
    $text = "NO";
    $file = fopen("result.txt", "w");
    fclose($file);
    $this->save_result($text);
    $this->save_result($text);
    exit($mensaje);
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
