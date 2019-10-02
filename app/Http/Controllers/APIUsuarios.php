<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Lang; //Lenguaje
use App; //Config gral. app
use Config; //Config gral. app
use Auth;
use carbon\Carbon; //Manipuleo de Fechas
use Illuminate\Support\Facades\Log; //Login
use Session;
use Validator; //Validar formularios
use App\Library\DAO\Usuarios;
use App\Library\VO\ResponseJSON;
use JWTAuth;
use JWTFactory;
use Tymon\JWTAuth\PayloadFactory;
use Tymon\JWTAuth\Exceptions\JWTException;

class APIUsuarios extends Controller
{

    public function Ingresar(Request $request){
  
        Log::info('[Ingresar]');

        Log::info("[Ingresar] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {
            $this->validate($request, [
                'usuario' => 'required',
                'password' => 'required'
              ]);

        Log::info('[Ingresar] conn1');
            $usuario = $request->input('usuario');
            $password = $request->input('password');

            
            Log::info('[Ingresar] conn');


            $user = Usuarios::lookForByEmailandPassword($usuario,$password)->get();
 
            Log::info($user);

            if(count($user)>0){

                /***********************************************/
                $jwt_token = null;

                $factory = JWTFactory::customClaims([
                'sub' => $user->first()->id, //id a conciliar del usuario
                'iss' => config('app.name'),
                'iat' => Carbon::now()->timestamp,
                'exp' => Carbon::tomorrow()->timestamp,
                'nbf' => Carbon::now()->timestamp,
                'jti' => uniqid(),
                'usr' => $user
                ]);

                $payload = $factory->make();

                $jwt_token = JWTAuth::encode($payload);

                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDsuccess'), count($user));
                $responseJSON->data = $user;
                $responseJSON->token = $jwt_token->get();

                /***********************************************/
                return json_encode($responseJSON);
                
                } else {
                
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBD'), count($user));
                $responseJSON->data = [];
                return json_encode($responseJSON);
                
            }
        }
    }
}

?>