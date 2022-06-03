<?php

class Enc
{
  public $m1;
  public $m2;
  public $n;
  public $ini_points;
  public $random_winner;

  public function __construct()
  {
    $this->m1 = 0;
    $this->m2 = 0;
    $this->n = 0;
    $this->ini_points = 0;
    $this->random_winner = rand(1, 2);
  }


  public function leer_archivo($file, $lines)
  {
    $i = 1;
    $archivo = fopen($file, "r");
    $player = $this->random_winner; // ganador aleatorio si no existen diferencias en todas las rondas

    while (!feof($archivo))
    {
      $get = trim(fgets($archivo));
      if ($i == 1)
      {

        $this->n = (int)$get;
        if (!is_numeric($this->n)) $this->save($this->random_winner, 0); // se valida que el valor de rondas sea numerico
        if ($lines - 1 != $this->n) $this->save($this->random_winner, 0); // se se valida que el numero de rondas especificado sea el mismo que tiene el archivo
        if ($this->n > 10000) // de n ser mayor a 10000 guarda los valores por defecto
        {
          fclose($archivo);
          $this->save($player, $this->ini_points);
          exit();
        }
      }
      else
      {
        $parts = explode(" ", $get);

        if (!is_numeric($parts[0]) || !is_numeric($parts[1])) $this->save($player, $this->ini_points); // se valida que el valor de la ronda se numerico, gana el que tenia la mayor diferencia en la ronda anterior si no se encuentra numero en la ronda

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
  }

  public function save($player, $points)
  {
    $file = fopen("result.txt", "w");
    fwrite($file, $player . " " . $points . PHP_EOL);
    fclose($file);
    exit();
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

$lines = count(file($file));

if ($lines > 10001 || $lines < 2) // condicion si el archivo contiene mas lineas de las permitidas
{
  $encrypt->save(rand(1, 2), 0);
}

$encrypt->leer_archivo($file, $lines);
