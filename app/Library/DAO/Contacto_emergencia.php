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

class Contacto_emergencia extends Model
{
    public $table = 'contacto_emergencia';
    public $timestamps = true;
    //protected $dateFormat = 'U';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    //public $attributes;

    public function scopeGetContactemerg($query, $id_user){

      Log::info("[Contacto_emergencia][scopeGetContactemerg]");

      // $pass = hash("sha256", $pass);


      //activar log query
      DB::connection()->enableQueryLog();

      $sql = $query->where([
        ['id_contacto', '=', $id_user],
      ])->get();

      //log query
      $queries = DB::getQueryLog();
      $last_query = end($queries);
      Log::info($last_query);

      return $sql;

    }


    public function scopeLookForByEmailandPassword($query, $user,$password){

        Log::info("[Contacto_emergencia][scopeLookForByEmailandPassword]");
        Log::info("[Contacto_emergencia][scopeLookForByEmailandPassword]" . $user);
        Log::info("[Contacto_emergencia][scopeLookForByEmailandPassword]". $password);

        $pass = hash("sha256", $password);

        return $query->where([
          ['correo', '=', $user],
          ['password', '=', $pass]
        ]);

    }

    public function scopeCreateUser($query, $id_usuarios, $contactoEmergenica, $parentezco, $celContacto){

      Log::info("[Contacto_emergencia][scopeCreateUser]");

      $contactos_emergencia = new Contacto_emergencia();

      $contactos_emergencia->id_usuarios = $id_usuarios;
      $contactos_emergencia->contacto = $contactoEmergenica;
      $contactos_emergencia->parentezco = $parentezco;
      $contactos_emergencia->cel_contacto = $celContacto;

      $obj = Array();
      $obj[0] = new \stdClass();
      $obj[0]->save = $contactos_emergencia->save(); //return true in the other one return 1
      $obj[0]->id = $contactos_emergencia->id;

      return $obj;
  }
}
?>