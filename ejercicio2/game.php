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


  public function leer_archivo($file, $lines)
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

        if (!is_numeric($get))
        {
          $this->save_random();
          $this->mensaje_error("El numero de rondas no es numérico");
        }

        if ($this->n > 10000) // de n ser mayor a 10000 guarda los valores por defecto
        {
          $this->save_random();
          $this->mensaje_error("El número de rondas es mayor a lo permitido 10000");
        }

        if ($lines - 1 < $this->n)
        {
          $this->save_random();
          $this->mensaje_error("El numero de rondas especificado es menor a la entrada");
        }

        if ($lines - 1 > $this->n)
        {
          $this->save_random();
          $this->mensaje_error("El numero de rondas especificado es mayor a la entrada");
        }

        // se se valida que el numero de rondas especificado sea el mismo que tiene el archivo

      }
      else
      {
        $parts = explode(" ", $get);
        $valores_ronda = count($parts);
        if ($valores_ronda > 2)
        {
          echo ("Los valores de la ronda " . ($i - 1) . " es mas de lo permitido= " . $valores_ronda);
          break;
        }

        if (!is_numeric($parts[0]))
        {
          $player = 2;
          echo ("El valor de la ronda " . ($i - 1) . " para el jugador 1 no es numérico, termina el conteo");
          break;
        }

        if (!is_numeric($parts[1]))
        {
          $player = 1;
          echo ("El valor de la ronda " . ($i - 1) . " para el jugador 2 no es numérico, termina el conteo");
          break;
        }

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

  public function mensaje_error($mensaje)
  {
    exit($mensaje);
  }

  public function save_random()
  {
    $file = fopen("result.txt", "w");
    fwrite($file, rand(1, 2) . " " . 0 . PHP_EOL);
    fclose($file);
    // exit();
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
  $encrypt->save_random(rand(1, 2), 0);
  $encrypt->mensaje_error("El archivo no cuenta con las lineas permitidas");
}

$encrypt->leer_archivo($file, $lines);
