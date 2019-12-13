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

class Usuarios extends Model
{
    public $table = 'usuarios';
    public $timestamps = true;
    //protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    //public $attributes;

    public function scopeGetProfile($query, $id_user){

        Log::info("[Usuarios][scopeGetProfile]");

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

    public function scopeLookForByEmailandPassword($query, $user,$password){

        Log::info("[Usuario][scopeLookForByEmailandPassword]");
        Log::info("[Usuario][scopeLookForByEmailandPassword]" . $user);
        Log::info("[Usuario][scopeLookForByEmailandPassword]". $password);
        
        //activar log query
        DB::connection()->enableQueryLog();
  
        $pass = hash("sha256", $password);

        $sql =  $query->where([
          ['correo', '=', $user],
          ['password', '=', $pass]
        ])->get();

        //return true in the other one return 1
  
        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);
  
        return $sql;


    }

    public function scopeChangePassword($query, $celular,$password){
      
      Log::info("[Usuarios][scopeChangePassword]");
      DB::connection()->enableQueryLog();

      $pass = hash("sha256", $password);

      $sql = $query->where([
        ['celular', '=' , $celular]
        ])->update([
          'password' => $pass
        ]);

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;

  }

    public function scopeLookForByEmail($query, $user){

        Log::info("[Usuario][scopeLookForByEmail]");
        Log::info("[Usuario][scopeLookForByEmail]" . $user);

        return $query->where([
          ['correo', '=', $user]
        ]);

    }

    public function scopeLookForByCel($query, $user){

        Log::info("[Usuario][scopeLookForByCel]");
        Log::info("[Usuario][scopeLookForByCel]" . $user);

        return $query->where([
          ['celular', '=', $user]
        ]);

    }

    public function scopeLookForByIDfb($query, $user){

        Log::info("[Usuario][scopeLookForByEmail]");
        Log::info("[Usuario][scopeLookForByEmail]" . $user);

        return $query->where([
          ['id_userfb', '=', $user]
        ]);

    }

    public function scopeLookForVerify($query, $verification_code){
      Log::info("[Clientes][scopeLookForVerify]");
      return $query->where([
        ['verificacion_code', '=' ,$verification_code]
      ]);
    }

    public function scopeUpdateVerify($query, $verification_code){
      Log::info("[Clientes][scopeUpdateVerify]");
      return $query->where([
        ['verificacion_code', '=', $verification_code]
        ])->update(['verificacion' => 1]);
    }

    public function scopeCreateUser($query, $id_userfb, $correo, $password, $nombre, $apellido, $edad, $celular, $motoClub, $seguro, $sangre, $alergia, $organos){

      Log::info("[Usuarios][scopeCreateUser]");

      $usuarios = new Usuarios();

      $usuarios->id_userfb = $id_userfb;
      $usuarios->correo = $correo;
      $usuarios->password = hash("sha256", $password);
      $usuarios->nombre = $nombre;
      $usuarios->apellido = $apellido;
      $usuarios->correo = $correo;
      $usuarios->edad = $edad;
      $usuarios->celular = $celular;
      $usuarios->motoclub = $motoClub;
      $usuarios->seguro = $seguro;
      $usuarios->sangre = $sangre;
      $usuarios->alergia = $alergia;
      $usuarios->organos = $organos;

      $obj = Array();
      $obj[0] = new \stdClass();
      $obj[0]->save = $usuarios->save(); //return true in the other one return 1
      $obj[0]->id = $usuarios->id;

      return $obj;
    }

    public function scopeUpdateSalud($query, $id_user, $seguro, $sangre, $alergia, $organos){
      Log::info("[Salud][scopeUpdateSalud]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_usuarios', '=' , $id_user]
        ])->update([
          'seguro' => $seguro,
          'sangre' => $sangre,
          'alergia' => $alergia,
          'organos' => $organos
        ]);

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;
        
    }

    public function scopeUpdatePerfil($query, $id_user, $nombre, $apellido, $edad, $celular, $motoClub){
      Log::info("[Salud][scopeUpdatePerfil]");
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_usuarios', '=' , $id_user]
        ])->update([
          'nombre' => $nombre,
          'apellido' => $apellido,
          'edad' => $edad,
          'celular' => $celular,
          'motoclub' => $motoClub
        ]
        
        );

        //log query
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($last_query);

        return $sql;
        
    }
}
?>