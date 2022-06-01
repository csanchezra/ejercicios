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


  public function leer_archivo($file)
  {
    $i = 1;
    $archivo = fopen($file, "r");
    $player = rand(1, 2); // ganador aleatorio si no existen diferencias en todas las rondas

    while (!feof($archivo))
    {
      $get = trim(fgets($archivo));
      if ($i == 1)
      {
        $this->n = (int)$get;
        if ($this->n > 10000) // de n ser mayor a 10000 guarda los valores por defecto
        {
          fclose($archivo);
          $file = fopen("result.txt", "w");
          fwrite($file, $player . " " . $this->ini_points . PHP_EOL);
          fclose($file);
          exit();
        }
      }
      else
      {
        $parts = explode(" ", $get);
        $this->m1 += (int)$parts[0];
        $this->m2 += (int)$parts[1];
        $absolut = $this->m1 - $this->m2; // diferencia del acumulado

        if (abs($absolut) > $this->ini_points) // comparamos que la diferencia sea mayor que la anterior
        {
          if ($absolut > 0) // la diferencia es positiva gana jugador 1
          {
            $this->ini_points = abs($absolut);
            $player = 1;
          }
          elseif ($absolut < 0) // la diferencia es negativa gana jugador 2
          {
            $this->ini_points = abs($absolut);
            $player = 2;
          }
        }
      }
      $i++;
    }

    fclose($archivo);

    $file = fopen("result.txt", "w");
    fwrite($file, $player . " " . $this->ini_points . PHP_EOL);
    fclose($file);
  }
}

$encrypt = new Enc();


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

$encrypt->leer_archivo($file);
