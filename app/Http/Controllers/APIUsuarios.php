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
use App\Library\DAO\Motos;
use App\Library\DAO\Contacto_emergencia;
use App\Library\DAO\Permisos_inter;
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

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $this->validate($request, [
                'correo' => 'required',
                'password' => 'required'
              ]);

        Log::info('[Ingresar] conn1');
            $correo = $request->input('correo');
            $password = $request->input('password');

            
            Log::info('[Ingresar] conn');


            $user = Usuarios::lookForByEmailandPassword($correo,$password)->get();
 
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

    public function Verificar(Request $request){

        Log::info('[Ingresar]');

        Log::info("[Ingresar] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $this->validate($request, [
                'correo' => 'required'
              ]);

            Log::info('[Ingresar] conn1');
            $correo = $request->input('correo');

            
            Log::info('[Ingresar] conn');


            $user = Usuarios::lookForByEmail($correo)->get();
 
            Log::info($user);

            if(count($user)>0){

                /***********************************************/
                /*$jwt_token = null;

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

                $jwt_token = JWTAuth::encode($payload);*/

                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDsuccess'), count($user));
                $responseJSON->data = $user;
                // $responseJSON->token = $jwt_token->get();

                /***********************************************/
                return json_encode($responseJSON);
                
                } else {
                
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBD'), count($user));
                $responseJSON->data = [];
                return json_encode($responseJSON);
                
            }
        }
        
    }

    public function Registrar(Request $request){
      
        Log::info('[APIUserNormal][registrar]');

        Log::info("[APIUserNormal][registar] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');
/*

            Validator::make($request->all(), [
                'nombre' => 'required',
                'apellido' => 'required',
                'correo' => 'required',
                'telefono' => 'required',
                'cel' => 'required',
              ])->validate();
  */    
              //Log::info('[APIUserNormal][registrar]2');
            $correo = $request->input('correo');
            $password = $request->input('password');
            $nombre = $request->input('nombre');
            $apellido = $request->input('apellido');
            $edad = $request->input('edad');
            $celular = $request->input('celular');
            $conductor = $request->input('conductor');
            $propietario = $request->input('propietario');            
            $motoClub = $request->input('motoClub');
            $marca = $request->input('marca');
            $submarca = $request->input('submarca');
            $modelo = $request->input('modelo');
            $motor = $request->input('motor');
            $vin = $request->input('vin');
            $cc = $request->input('cc');
            $ciudad = $request->input('ciudad');
            $placas = $request->input('placas');
            $compania = $request->input('compania');
            $poliza = $request->input('poliza');
            $seguro = $request->input('seguro');
            $sangre = $request->input('sangre');
            $alergia = $request->input('alergia');
            $organos = $request->input('organos');
            $contactoEmergenica = $request->input('contactoEmergencia');
            $parentezco = $request->input('parentezco');
            $celContacto = $request->input('celContacto');


        
            Log::info("[APIUserNormal][registar] Correo: ". $correo);
            Log::info("[APIUserNormal][registar] Password: ". $password);
            Log::info("[APIUserNormal][registar] Nombre: ". $nombre);
            Log::info("[APIUserNormal][registar] Apellido: ". $apellido);
            Log::info("[APIUserNormal][registar] Edad: ". $edad);
            Log::info("[APIUserNormal][registar] Celular: ". $celular);
            Log::info("[APIUserNormal][registar] Conductor: ". $conductor);
            Log::info("[APIUserNormal][registar] Propietario: ". $propietario);
            Log::info("[APIUserNormal][registar] Moto Club: ". $motoClub);
            Log::info("[APIUserNormal][registar] Marca: ". $marca);
            Log::info("[APIUserNormal][registar] Submarca: ". $submarca);
            Log::info("[APIUserNormal][registar] Modelo: ". $modelo);
            Log::info("[APIUserNormal][registar] Motor: ". $motor);
            Log::info("[APIUserNormal][registar] VIN: ". $vin);
            Log::info("[APIUserNormal][registar] CC: ". $cc);
            Log::info("[APIUserNormal][registar] Ciudad: ". $ciudad);
            Log::info("[APIUserNormal][registar] Placas: ". $placas);
            Log::info("[APIUserNormal][registar] Compania: ". $compania);
            Log::info("[APIUserNormal][registar] Poliza: ". $poliza);
            Log::info("[APIUserNormal][registar] Seguro: ". $seguro);
            Log::info("[APIUserNormal][registar] Sangre: ". $sangre);
            Log::info("[APIUserNormal][registar] Alergia: ". $alergia);
            Log::info("[APIUserNormal][registar] Organos: ". $organos);
            Log::info("[APIUserNormal][registar] Contacto de Emergencia: ". $contactoEmergenica);
            Log::info("[APIUserNormal][registar] Parentezco: ". $parentezco);
            Log::info("[APIUserNormal][registar] Cel de Contacto: ". $celContacto);
        
                
            $usuario = Usuarios::createUser( $correo, $password, $nombre, $apellido, $edad, $celular, $motoClub, $seguro, $sangre, $alergia, $organos);
            Log::info($usuario);

            $motos = Motos::createUser( $conductor, $propietario, $marca, $submarca, $modelo, $motor, $vin, $cc, $ciudad, $placas, $compania, $poliza);
            Log::info($motos);

            $contacto_emergencia = Contacto_emergencia::createUser( $contactoEmergenica, $parentezco, $celContacto);
            Log::info($contacto_emergencia);
    
            if($usuario[0]->save == 1 && $motos[0]->save == 1 && $contacto_emergencia[0]->save == 1){

                Log::info('[APIUsuarios][registar] Se registro el usuario en todas las tablas, creando permisos');

                $permisos_inter_object = Permisos_inter::createPermisoInter($usuario[0]->id);

                if ($permisos_inter_object[0]->save == 1) {

                    $permisos_inter_object = Permisos_inter::lookForByIdUsuarios($usuario[0]->id)->get();
                    $permisos_inter = array();
                    foreach($permisos_inter_object as $permiso){
                        $permisos_inter[] = $permiso["id_permisos"];
                    }
            
                    $jwt_token = null;
            
                    $factory = JWTFactory::customClaims([
                        'sub'   => $usuario[0]->id, //id a conciliar del usuario
                        'iss'   => config('app.name'),
                        'iat'   => Carbon::now()->timestamp,
                        'exp'   => Carbon::tomorrow()->timestamp,
                        'nbf'   => Carbon::now()->timestamp,
                        'jti'   => uniqid(),
                        'usr'   => $usuario[0],
                        'permisos' => $permisos_inter,
                    ]);
                    
                    $payload = $factory->make();
                    
                    $jwt_token = JWTAuth::encode($payload);
                    Log::info("[API][ingresar] new token: ". $jwt_token->get());
                    Log::info("[API][ingresar] Permisos: ");
                    Log::info($permisos_inter);
                    
                    $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                    $responseJSON->data = $usuario;
                    $responseJSON->token = $jwt_token->get();
                    return json_encode($responseJSON);

                }            
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), count($usuario));
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";
            
        } else {
            abort(404);
        }
    }


}

?>