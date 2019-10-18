<?php

namespace App\Library\DAO;
use Config;
use App;
use Log;
use Illuminate\Database\Eloquent\Model;

/*

update and insert doesnt need get->()


*/

class Motos extends Model
{
    public $table = 'motos';
    public $timestamps = true;
    //protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    //public $attributes;


    public function scopeLookForByEmailandPassword($query, $user,$password)
    {

        Log::info("[Motos][scopeLookForByEmailandPassword]");
        Log::info("[Motos][scopeLookForByEmailandPassword]" . $user);
        Log::info("[Motos][scopeLookForByEmailandPassword]". $password);

        $pass = hash("sha256", $password);

        return $query->where([
          ['correo', '=', $user],
          ['password', '=', $pass]
        ]);

    }

    public function scopeCreateUser($query, $conductor, $propietario, $marca, $submarca, $modelo, $motor, $vin, $cc, $ciudad, $placas, $compania, $poliza){

      Log::info("[Motos][scopeCreateUser]");

      $motos = new Motos();

      $motos->conductor = $conductor;
      $motos->propietario = $propietario;
      $motos->marca = $marca;
      $motos->submarca = $submarca;
      $motos->modelo = $modelo;
      $motos->motor = $motor;
      $motos->vin = $vin;
      $motos->cc = $cc;
      $motos->ciudad = $ciudad;
      $motos->placas = $placas;
      $motos->compania = $compania;
      $motos->poliza = $poliza;

      $obj = Array();
      $obj[0] = new \stdClass();
      $obj[0]->save = $motos->save(); //return true in the other one return 1
      $obj[0]->id = $motos->id;

      return $obj;
  }
}
?>