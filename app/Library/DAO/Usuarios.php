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
    //public $timestamps = true;
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
          ['user', '=', $user],
          ['password', '=', $pass]
        ]);

    }
}
?>