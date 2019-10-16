<?php

namespace App\Library\DAO;
use Config;
use App;
use Log;
use Illuminate\Database\Eloquent\Model;

/*

update and insert doesnt need get->()


*/

class Usuarios extends Model
{
    public $table = 'usuarios';
    public $timestamps = false;
    //protected $dateFormat = 'U';
    //const CREATED_AT = 'created_at';
    //const UPDATED_AT = 'updated_at';
    //public $attributes;


    public function scopeLookForByEmailandPassword($query, $user,$password)
    {

        Log::info("[Usuario][scopeLookForByEmailandPassword]");
        Log::info("[Usuario][scopeLookForByEmailandPassword]" . $user);
        Log::info("[Usuario][scopeLookForByEmailandPassword]". $password);

        $pass = hash("sha256", $password);

        return $query->where([
          ['correo', '=', $user],
          ['password', '=', $pass]
        ]);

    }

    public function scopeLookForByEmail($query, $user)
    {

        Log::info("[Usuario][scopeLookForByEmail]");
        Log::info("[Usuario][scopeLookForByEmail]" . $user);

        return $query->where([
          ['correo', '=', $user]
        ]);

    }

    public function scopeCreateUser($query, $correo, $password, $nombre, $apellido, $edad, $celular, $motoClub, $seguro, $sangre, $alergia, $organos){

      Log::info("[Usuarios][scopeCreateUser]");

      $usuarios = new Usuarios();

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
}
?>