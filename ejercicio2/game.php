<?php

class Enc
{
  public $m1;
  public $m2;
  public $n;
  public $ini_points;

  public function __construct()
  {
    $this->m1 = 0;
    $this->m2 = 0;
    $this->n = 0;
    $this->ini_points = 0;
  }


  public function leer_archivo()
  {
    $i = 1;
    $archivo = fopen("archivo2.txt", "r");

    while (!feof($archivo))
    {
      $get = trim(fgets($archivo));
      if ($i == 1)
      {
        $this->n = (int)$get;
      }
      else
      {

        $parts = explode(" ", $get);
        $this->m1 = (int)$parts[0];
        $this->m2 = (int)$parts[1];
        $absolut = $this->m1 - $this->m2;

        if (abs($absolut) > $this->ini_points)
        {
          if ($absolut > 0)
          {
            $this->ini_points = abs($absolut);
            $player = 1;
          }
          elseif ($absolut < 0)
          {
            $this->ini_points = abs($absolut);
            $player = 2;
          }
        }
      }
      $i++;
    }

    fclose($archivo);

    $file = fopen("result2.txt", "w");
    fwrite($file, $player . " " . $this->ini_points . PHP_EOL);
    fclose($file);
  }
}

$encrypt = new Enc();
$encrypt->leer_archivo();
