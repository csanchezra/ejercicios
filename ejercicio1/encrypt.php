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


  public function read_file()
  {
    $archivo = fopen("archivo.txt", "r");
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

    /* Por cada linea se busca la coincidenacia*/

    $this->find_text($this->message, $this->str_1, $this->m1);
    $this->find_text($this->message, $this->str_2, $this->m2);
  }

  private function find_text($message, $string, $length)
  {

    $text = "NO";

    if (str_contains($message, $string) && ($length >= 2 && $length <= 50))
    {
      $text = "SI";
    }

    $this->save_result($text);
  }

  private function save_result($string)
  {
    $file = fopen("result.txt", "a");

    fwrite($file, $string . PHP_EOL);

    fclose($file);
  }
}

$encrypt = new Enc();
$encrypt->read_file();
$encrypt->find_coincidences();
