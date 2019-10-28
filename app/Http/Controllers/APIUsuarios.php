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

        Log::info('[Ingresar] conn1');
            $correo = $request->input('correo');
            $password = $request->input('password');

            
            Log::info('[Ingresar] conn');


            $user = Usuarios::lookForByEmailandPassword($correo,$password);
 
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
                
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsBDFail'), count($user));
                $responseJSON->data = [];
                return json_encode($responseJSON);
                
            }
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
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.SendSMS'), count($obj[0]->status));
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            

            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsSendSMS'), count($obj[0]->status));
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
                    
                $responseJSON = new ResponseJSON(Lang::get('messages.successTrue'),Lang::get('messages.VerifiedCode'), count($obj[0]->status));
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            

            } else {
                $responseJSON = new ResponseJSON(Lang::get('messages.successFalse'),Lang::get('messages.errorsVerifiedCode'), count($obj[0]->status));
                $responseJSON->data = $obj;
                return json_encode($responseJSON);
        
            }

            // return $response;

        } else {
            abort(404);
        }
    }

    /*Email Verification*/
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

            $motos = Motos::createUser( $conductor, $propietario, $marca, $submarca, $modelo, $motor, $vin, $cc, $ciudad, $placas, $compania, $poliza);
            Log::info($motos);

            $contacto_emergencia = Contacto_emergencia::createUser( $contactoEmergenica, $parentezco, $celContacto);
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


}

?>