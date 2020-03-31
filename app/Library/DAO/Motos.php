<?php

namespace App\Library\DAO;
use Config;
use App;
use Log;
use Illuminate\Database\Eloquent\Model;
use DB;


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

    public function scopeGetMotos($query, $id_user){

      Log::info("[Motos][scopeGetMotos]");

      // $pass = hash("sha256", $pass);


      //activar log query
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_usuarios', '=', $id_user],
      ])->get();

      //log query
      $queries = DB::getQueryLog();
      $last_query = end($queries);
      Log::info($last_query);

      return $sql;

    }

    public function scopeGetEditMotos($query, $vin){

      Log::info("[Motos][scopeGetMotos]");

      // $pass = hash("sha256", $pass);


      //activar log query
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['vin', '=', $vin],
      ])->get();

      //log query
      $queries = DB::getQueryLog();
      $last_query = end($queries);
      Log::info($last_query);

      return $sql;

    }

    public function scopeUpdateMoto($query, $vin, $motor, $placas){
      Log::info("[Salud][scopeUpdateMoto]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['vin', '=' , $vin]
        ])->update([
          'motor' => $motor,
          'placas' => $placas
        ]
        
        );

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;
        
    }

    public function scopeUpdateSeguro($query, $id_user, $compania, $poliza){
      Log::info("[Salud][scopeUpdateMoto]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_motos', '=' , $id_user]
        ])->update([
          'compania' => $compania,
          'poliza' => $poliza
        ]
        
        );

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;
        
    }

    public function scopeLookForByEmailandPassword($query, $user,$password){

        Log::info("[Motos][scopeLookForByEmailandPassword]");
        Log::info("[Motos][scopeLookForByEmailandPassword]" . $user);
        Log::info("[Motos][scopeLookForByEmailandPassword]". $password);

        $pass = hash("sha256", $password);

        return $query->where([
          ['correo', '=', $user],
          ['password', '=', $pass]
        ]);

    }

    public function scopeLookForByVin($query, $user){

        Log::info("[Motos][scopeLookForByVin]");
        Log::info("[Motos][scopeLookForByVin]" . $user);

        return $query->where([
          ['vin', '=', $user]
        ]);

    }

    public function scopeCreateUser($query, $id_usuarios, $conductor, $propietario, $marca, $submarca, $modelo, $motor, $vin, $cc, $ciudad, $placas, $compania, $poliza){

      Log::info("[Motos][scopeCreateUser]");

      $motos = new Motos();

      $motos->id_usuarios = $id_usuarios;
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

    public function scopeDeleteMoto($query, $vin){
      Log::info("[Salud][scopeDeleteMoto]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['vin', '=', $vin]
        ])->delete(); //return true in the other one return 1

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;
        
    }

}
?>