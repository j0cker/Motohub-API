<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Lang; //Lenguaje
use App; //Config gral. app
use App\Library\CLASSES\SMS;
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
use App\Library\CLASSES\QueueMails;

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

            Log::info('[Ingresar] Conectado');
            $correo = $request->input('correo');
            $password = $request->input('password');

            Log::info("[APIUsuarios][Ingresar] Correo: ". $correo);
            Log::info("[APIUsuarios][Ingresar] Password: ". $password);

            $user = Usuarios::lookForByEmailandPassword($correo,$password);
            Log::info($user);

            if(count($user)>0){

                $permisos_inter_object = Permisos_inter::lookForByIdUsuarios($user->first()->id_usuarios)->get();
                $permisos_inter = array();
                foreach($permisos_inter_object as $permiso){
                    $permisos_inter[] = $permiso["id_permisos"];
                }

                /***********************************************/
                $jwt_token = null;

                $factory = JWTFactory::customClaims([
                'sub' => $user->first()->id_usuarios, //id a conciliar del usuario
                'iss' => config('app.name'),
                'iat' => Carbon::now()->timestamp,
                'exp' => Carbon::tomorrow()->timestamp,
                'nbf' => Carbon::now()->timestamp,
                'jti' => uniqid(),
                'usr'   => $user->first(),
                'permisos' => $permisos_inter,
                ]);

                $payload = $factory->make();

                $jwt_token = JWTAuth::encode($payload);

                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDsuccess'), count($user));
                $responseJSON->data = $user;
                $responseJSON->token = $jwt_token->get();

                /***********************************************/
                return json_encode($responseJSON);
                
            } else {
                
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), count($user));
                $responseJSON->data = [];
                return json_encode($responseJSON);
                
            }
        }
    }

    public function ChangePassword(Request $request){
  
        Log::info('[APIUsuarios][ChangePassword]');

        Log::info("[APIUsuarios][ChangePassword] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $this->validate($request, [
                'celular' => 'required',
                'password' => 'required'
              ]);

            Log::info('[APIUsuarios][ChangePassword] Conectado');
            $celular = $request->input('celular');
            $password = $request->input('password');

            
            Log::info('[APIUsuarios][Ingresar] conn');


            $usuario = Usuarios::changePassword($celular,$password);
 
            Log::info($usuario);
            if($usuario == 1){

                Log::info('[APIUsuarios][ChangePassword] Se actualizo los datos de la moto en la tabla Motos');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsChangePass'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";
        }
    }

    public function Verificar(Request $request){

        Log::info('[Verificar Correo]');

        Log::info("[verificar Correo] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $this->validate($request, [
                'correo' => 'required'
              ]);

            Log::info('[Verificar Correo] conn1');
            $correo = $request->input('correo');

            
            Log::info('[Verificar Correo] conn');


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

    public function VerificarFB(Request $request){

        Log::info('[VerificarFB]');

        Log::info("[VerificarFB] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $this->validate($request, [
                'id_userfb' => 'required'
              ]);

            Log::info('[VerificarFB] conn1');
            $id_userfb = $request->input('id_userfb');

            
            Log::info('[VerificarFB] conn');


            $user = Usuarios::lookForByIDfb($id_userfb)->get();
 
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

    public function VerificarCel(Request $request){

        Log::info('[Verificar Celular]');

        Log::info("[verificar Celular] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $this->validate($request, [
                'celular' => 'required'
              ]);

            $celular = $request->input('celular');


            $user = Usuarios::lookForByCel($celular)->get();
 
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

    public function UpdateSalud(Request $request){
      
        Log::info('[APIUserNormal][UpdateSalud]');

        Log::info("[APIUserNormal][UpdateSalud] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');
            

            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            
            $token = $request->input('token');
            $id_user = $request->input('id_user');
            $seguro = $request->input('seguro');
            $sangre = $request->input('sangre');
            $alergia = $request->input('alergia');
            $organos = $request->input('organos');

            Log::info("[GetProfile][GetProfile] Token: ". $token);
            Log::info("[GetProfile][GetProfile] ID User: ". $id_user);
            Log::info("[APIUserNormal][registar] Seguro: ". $seguro);
            Log::info("[APIUserNormal][registar] Sangre: ". $sangre);
            Log::info("[APIUserNormal][registar] Alergia: ". $alergia);
            Log::info("[APIUserNormal][registar] Organos: ". $organos);
        
                
            $usuario = Usuarios::updateSalud($id_user, $seguro, $sangre, $alergia, $organos);
            Log::info($usuario);
            if($usuario == 1){

                Log::info('[APIUsuarios][UpdateSalud] Se actualizo los datos de salud en la tabla Usuarios');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
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

    public function UpdatePerfil(Request $request){
      
        Log::info('[APIUserNormal][UpdatePerfil]');

        Log::info("[APIUserNormal][UpdatePerfil] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');
            

            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            
            $token = $request->input('token');
            $id_user = $request->input('id_user');
            $nombre = $request->input('nombre');
            $apellido = $request->input('apellido');
            $edad = $request->input('edad');
            $celular = $request->input('celular');
            $motoClub = $request->input('motoClub');

            Log::info("[GetProfile][GetProfile] Token: ". $token);
            Log::info("[GetProfile][GetProfile] ID User: ". $id_user);
            Log::info("[APIUserNormal][registar] Nombre: ". $nombre);
            Log::info("[APIUserNormal][registar] Apellido: ". $apellido);
            Log::info("[APIUserNormal][registar] Edad: ". $edad);
            Log::info("[APIUserNormal][registar] Celular: ". $celular);
            Log::info("[APIUserNormal][registar] Moto Club: ". $motoClub);        
                
            $usuario = Usuarios::updatePerfil($id_user, $nombre, $apellido, $edad, $celular, $motoClub);
            Log::info($usuario);
            if($usuario == 1){

                Log::info('[APIUsuarios][UpdatePerfil] Se actualizo los datos de usuario en la tabla Usuarios');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
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

    public function UpdateMoto(Request $request){
      
        Log::info('[APIUserNormal][UpdatePerfil]');

        Log::info("[APIUserNormal][UpdatePerfil] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');
            

            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            
            $token = $request->input('token');
            $id_user = $request->input('id_user');
            $motor = $request->input('motor');
            $placas = $request->input('placas');

            Log::info("[GetProfile][GetProfile] Token: ". $token);
            Log::info("[GetProfile][GetProfile] ID User: ". $id_user);
            Log::info("[APIUserNormal][registar] Nombre: ". $motor);
            Log::info("[APIUserNormal][registar] Apellido: ". $placas);       
                
            $usuario = Motos::updateMoto($id_user, $motor, $placas);
            Log::info($usuario);
            if($usuario == 1){

                Log::info('[APIUsuarios][UpdatePerfil] Se actualizo los datos de la moto en la tabla Motos');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
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

    public function UpdateSeguro(Request $request){
      
        Log::info('[APIUserNormal][UpdatePerfil]');

        Log::info("[APIUserNormal][UpdatePerfil] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');
            

            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            
            $token = $request->input('token');
            $id_user = $request->input('id_user');
            $compania = $request->input('compania');
            $poliza = $request->input('poliza');

            Log::info("[GetProfile][GetProfile] Token: ". $token);
            Log::info("[GetProfile][GetProfile] ID User: ". $id_user);
            Log::info("[APIUserNormal][registar] Compania∫: ". $compania);
            Log::info("[APIUserNormal][registar] Poliza: ". $poliza);       
                
            $usuario = Motos::updateSeguro($id_user, $compania, $poliza);
            Log::info($usuario);
            if($usuario == 1){

                Log::info('[APIUsuarios][UpdatePerfil] Se actualizo los datos de la moto en la tabla Motos');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($usuario));
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
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

    public function VerificarVin(Request $request){

        Log::info('[Verificar Vin]');

        Log::info("[verificar Vin] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $this->validate($request, [
                'vin' => 'required'
              ]);

            $vin = $request->input('vin');


            $user = Motos::lookForByVin($vin)->get();
 
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

    public function SMS(Request $request){

        Log::info('[APIUsuarios][SMS]');

        Log::info("[APIUsuarios][SMS] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')){

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $celular = $request->input('celular');
            Log::info('[APIUsuarios][VerificarSMS] Celular: ' . $celular);

            $sms = new SMS();
            $status = $sms->verifyNumber('+52'. $celular);
            Log::info('[APIUsuarios][SMS] Mensaje enviado');

            $obj = Array();
            $obj[0] = new \stdClass();
            $obj[0]->status = $status; //return true in the other one return 1

            Log::info('[APIUserNormal][VerificarSMS] Status de Retorno: ' . $status);

            if($status === 'pending'){
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.SendSMS'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            

            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsSendSMS'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            }



        } else {
            abort(404);
        }
    }

    public function VerificarSMS(Request $request){

        Log::info('[APIUsuarios][VerificarSMS]');

        Log::info("[APIUsuarios][VerificarSMS] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')){

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            $code = $request->input('code');
            $celular = $request->input('celular');
            Log::info('[APIUsuarios][VerificarSMS] Código: ' . $code);
            Log::info('[APIUsuarios][VerificarSMS] Celular: ' . $celular);

            $sms = new SMS();
            $status = $sms->verifyCode($code, '+52'.$celular);
            Log::info('[APIUsuarios][VerificarSMS] Verificacion de Código');

            
            $obj = Array();
            $obj[0] = new \stdClass();
            $obj[0]->status = $status; //return true in the other one return 1

            Log::info('[APIUserNormal][VerificarSMS] Status de Retorno: ' . $status);

            if($status === 'approved'){
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.VerifiedCode'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            

            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsVerifiedCode'), 0);
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            }

            // return $response;

        } else {
            abort(404);
        }
    }

    public function VerifyMail($verification_code, Request $request){
        Log::info('[Index][Verify]');
        if($request->isMethod('GET')) {
          $clientes = Usuarios::lookForVerify($verification_code)->get();
          $title = Config::get('app.name');
          $lang = Config::get('app.locale');
          if(count($clientes[0]) > 0){
            if($clientes[0]->verificacion==1){
              return view('layouts.index.verification',["title" => $title, "lang" => $lang, "verify" => Lang::get('messages.wasVerified')]);
            } else {
              $addVerify = Usuarios::updateVerify($verification_code);
              if($addVerify==1){
                //enviar correo de cuenta verificada
                Log::info("[Index][Verify] se enviara correo electrónico para cuenta verificada al usuario: ". $clientes[0]->c_id ." ".$clientes[0]->c_nombre."");
                //Send to queue email list of administrator mail
                $data["name"] = $clientes[0]->c_nombre;
                $data["user_id"] = $clientes[0]->c_id;
                $data["tipo"] = "";
                $data['to'] = $clientes[0]->c_correo;
                $data['subject'] = "MotoHub: Tu cuenta ha sido verificada";
                $data['body'] = "Muchas Felicidades! Tu cuenta de Vash ha sido verificada con éxito";
                $data['priority'] = "5";
                $mail = new App\Library\classes\queueMails($data);
                $mail->customMailUnique();
                return view('layouts.index.verification',["title" => $title, "lang" => $lang, "verify" => Lang::get('messages.verified')]);
              } else {
                return view('layouts.index.verification',["title" => $title, "lang" => $lang, "verify" => Lang::get('messages.errorsBD')]);
              }
            }
          } else {
            return view('layouts.index.verification',["title" => $title, "lang" => $lang, "verify" => Lang::get('messages.notVerified')]);
          }
        } else {
          abort(404);
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
            $id_userfb = $request->input('id_userfb');
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


            Log::info("[APIUserNormal][registar] ID User FB: ". $id_userfb);
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
        
                
            $usuario = Usuarios::createUser( $id_userfb, $correo, $password, $nombre, $apellido, $edad, $celular, $motoClub, $seguro, $sangre, $alergia, $organos);
            Log::info($usuario);

            // id de usuario
            // Log::info('[APIUsuarios][registar] Id: ' . $usuario[0]->id);
            $id_usuarios = $usuario[0]->id;

            $motos = Motos::createUser( $id_usuarios, $conductor, $propietario, $marca, $submarca, $modelo, $motor, $vin, $cc, $ciudad, $placas, $compania, $poliza);
            Log::info($motos);

            $contacto_emergencia = Contacto_emergencia::createUser( $id_usuarios, $contactoEmergenica, $parentezco, $celContacto);
            Log::info($contacto_emergencia);
    
            if($usuario[0]->save == 1 && $motos[0]->save == 1 && $contacto_emergencia[0]->save == 1){

                Log::info('[APIUsuarios][registar] Se registro el usuario en todas las tablas, creando permisos');

                $permisos_inter_object = Permisos_inter::createPermisoInter($usuario[0]->id);

                if ($permisos_inter_object[0]->save == 1) {



                    /* Mandar Email Bienvenida */

                    $data["name"] = $nombre;
                    //Send to queue email list of administrator mail
                    $data["user_id"] = $usuario[0]->id;
                    $data["tipo"] = "Motociclista";
                    $data['email'] = $correo;
                    $data['password'] = $password;
                    $data['verification_code'] = 1234;
                    //$data['body'] = "".Lang::get('messages.emailSubscribeBody')."".$email."";
                    //$data['subject'] = Lang::get('messages.emailSubscribeSubject');
                    //$data['priority'] = 1;
                    $mail = new QueueMails($data);
                    $mail->welcome();

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

    public function AddMoto(Request $request){
      
        Log::info('[APIUserNormal][AddMoto]');

        Log::info("[APIUserNormal][AddMoto] Método Recibido: ". $request->getMethod());


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
            $id_usuarios = $request->input('id_usuarios');
            $conductor = $request->input('conductor');
            $propietario = $request->input('propietario');            
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


            Log::info("[APIUserNormal][AddMoto] ID Usuario: ". $id_usuarios);
            Log::info("[APIUserNormal][AddMoto] Conductor: ". $conductor);
            Log::info("[APIUserNormal][AddMoto] Propietario: ". $propietario);
            Log::info("[APIUserNormal][AddMoto] Marca: ". $marca);
            Log::info("[APIUserNormal][AddMoto] Submarca: ". $submarca);
            Log::info("[APIUserNormal][AddMoto] Modelo: ". $modelo);
            Log::info("[APIUserNormal][AddMoto] Motor: ". $motor);
            Log::info("[APIUserNormal][AddMoto] VIN: ". $vin);
            Log::info("[APIUserNormal][AddMoto] CC: ". $cc);
            Log::info("[APIUserNormal][AddMoto] Ciudad: ". $ciudad);
            Log::info("[APIUserNormal][AddMoto] Placas: ". $placas);
            Log::info("[APIUserNormal][AddMoto] Compania: ". $compania);
            Log::info("[APIUserNormal][AddMoto] Poliza: ". $poliza);

            $motos = Motos::createUser( $id_usuarios, $conductor, $propietario, $marca, $submarca, $modelo, $motor, $vin, $cc, $ciudad, $placas, $compania, $poliza);
            Log::info($motos);
    
            if($motos[0]->save == 1){

                Log::info('[APIUsuarios][AddMoto] Se registro el usuario en todas las tablas, creando permisos');

                    $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), count($motos));
                    $responseJSON->data = $motos;
                    // $responseJSON->token = $jwt_token->get();
                    return json_encode($responseJSON);         
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), count($motos));
                $responseJSON->data = $motos;
                return json_encode($responseJSON);
        
            }
    
            return "";
            
        } else {
            abort(404);
        }
    }

    public function DeleteMoto(Request $request){
      
        Log::info('[APIUserNormal][DeleteMoto]');

        Log::info("[APIUserNormal][DeleteMoto] Método Recibido: ". $request->getMethod());


        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');
            

            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            
            $token = $request->input('token');
            $id_user = $request->input('id_user');
            $vin = $request->input('vin');

            Log::info("[APIUserNormal][DeleteMoto] Token: ". $token);
            Log::info("[APIUserNormal][DeleteMoto] ID User: ". $id_user);
            Log::info("[APIUserNormal][DeleteMoto] Nombre: ". $vin);
                
            $usuario = Motos::deleteMoto($vin);
            Log::info($usuario);
            if($usuario == 1){

                Log::info('[APIUsuarios][DeleteMoto] Se ha eliminado los datos de la moto en la tabla Motos');
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDdata'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
    
            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), 0);
                $responseJSON->data = $usuario;
                return json_encode($responseJSON);
        
            }
    
            return "";
            
        } else {
            abort(404);
        }

    }

    public function GetProfile(Request $request) {
     
        Log::info('[GetProfile]');

        Log::info("[GetProfile] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            
            $token = $request->input('token');
            $id_user = $request->input('id_user');

            Log::info("[GetProfile][GetProfile] Token: ". $token);
            Log::info("[GetProfile][GetProfile] ID User: ". $id_user);

            try {

                // attempt to verify the credentials and create a token for the user
                $token = JWTAuth::getToken();
                $token_decrypt = JWTAuth::getPayload($token)->toArray();

                Log::info("Token permisos: " . print_r($token_decrypt,true));

                if(in_array(1, $token_decrypt["permisos"])){
                    // $id_usuarios = $token_decrypt["usr"]->id_usuarios;   
                    $usuario = Usuarios::getProfile($id_user);
                
                    Log::info($usuario);
            
                    if(count($usuario)>0){
                    
                    $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDsuccess'), count($usuario));
                    $responseJSON->data = $usuario;
                    return json_encode($responseJSON);
            
                    } else {
            
                    $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBD'), count($usuario));
                    $responseJSON->data = [];
                    return json_encode($responseJSON);
            
                    }

                } else{
                    $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBD'), 0);
                    $responseJSON->data = [];
                    return json_encode($responseJSON);
                }
        
              } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        
                //token_expired
            
                Log::info('[APIEmpresas][GetIdiomaObtener] Token error: token_expired');
        
                return redirect('/');
          
              } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        
                //token_invalid
            
                Log::info('[APIEmpresas][GetIdiomaObtener] Token error: token_invalid');
        
                return redirect('/');
          
              } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        
                //token_absent
            
                Log::info('[APIEmpresas][GetIdiomaObtener] Token error: token_absent');
        
                return redirect('/');
          
              }

        }
    }

    public function GetMotos(Request $request) {
     
        Log::info('[APIUsuarios][GetMotos]');

        Log::info("[APIUsuarios][GetMotos] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            
            $token = $request->input('token');
            $id_user = $request->input('id_user');

            Log::info("[APIUsuarios][GetMotos] Token: ". $token);
            Log::info("[APIUsuarios][GetMotos] ID User: ". $id_user);

            try {

                // attempt to verify the credentials and create a token for the user
                $token = JWTAuth::getToken();
                $token_decrypt = JWTAuth::getPayload($token)->toArray();

                if(in_array(1, $token_decrypt["permisos"])){
                    // $id_usuarios = $token_decrypt["usr"]->id_usuarios;   
                    $usuario = Motos::getMotos($id_user);
                
                    Log::info($usuario);
        
                    if(count($usuario)>0){
                    
                    $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDsuccess'), count($usuario));
                    $responseJSON->data = $usuario;
                    return json_encode($responseJSON);
            
                    } else {
            
                    $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBD'), count($usuario));
                    $responseJSON->data = [];
                    return json_encode($responseJSON);
            
                    }

                } else{
                    $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBD'), count($usuario));
                    $responseJSON->data = [];
                    return json_encode($responseJSON);
                }
        
              } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        
                //token_expired
            
                Log::info('[APIEmpresas][GetIdiomaObtener] Token error: token_expired');
        
                return redirect('/');
          
              } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        
                //token_invalid
            
                Log::info('[APIEmpresas][GetIdiomaObtener] Token error: token_invalid');
        
                return redirect('/');
          
              } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        
                //token_absent
            
                Log::info('[APIEmpresas][GetIdiomaObtener] Token error: token_absent');
        
                return redirect('/');
          
              }

        }
    }

    public function GetContactemerg(Request $request) {
     
        Log::info('[APIUsuarios][GetContactemerg]');

        Log::info("[APIUsuarios][GetContactemerg] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: *');
            header('Access-Control-Allow-Headers: *');

            Validator::make($request->all(), [
                'token' => 'required'
            ])->validate();
            
            $token = $request->input('token');
            $id_user = $request->input('id_user');

            Log::info("[APIUsuarios][GetContactemerg] Token: ". $token);
            Log::info("[APIUsuarios][GetContactemerg] ID User: ". $id_user);

            try {

                // attempt to verify the credentials and create a token for the user
                $token = JWTAuth::getToken();
                $token_decrypt = JWTAuth::getPayload($token)->toArray();

                if(in_array(1, $token_decrypt["permisos"])){
                    // $id_usuarios = $token_decrypt["usr"]->id_usuarios;   
                    $usuario = Contacto_emergencia::getContactemerg($id_user);
                
                    Log::info($usuario);
            
                    if(count($usuario)>0){
                    
                    $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.BDsuccess'), count($usuario));
                    $responseJSON->data = $usuario;
                    return json_encode($responseJSON);
            
                    } else {
            
                    $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBD'), count($usuario));
                    $responseJSON->data = [];
                    return json_encode($responseJSON);
            
                    }

                } else{
                    $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBD'), 0);
                    $responseJSON->data = [];
                    return json_encode($responseJSON);
                }
        
              } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        
                //token_expired
            
                Log::info('[APIEmpresas][GetIdiomaObtener] Token error: token_expired');
        
                return redirect('/');
          
              } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        
                //token_invalid
            
                Log::info('[APIEmpresas][GetIdiomaObtener] Token error: token_invalid');
        
                return redirect('/');
          
              } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        
                //token_absent
            
                Log::info('[APIEmpresas][GetIdiomaObtener] Token error: token_absent');
        
                return redirect('/');
          
              }

        }
    }


}

?>