<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


/*
**** End Points Generales ****
*/

// Ingresar Usuarios
Route::get('/usuarios/ingresar', 'APIUsuarios@Ingresar');

// Registro Usuarios por correo
Route::get('/usuarios/registrar', 'APIUsuarios@Registrar');

// Verificar correo Usuarios
Route::get('/usuarios/verificar', 'APIUsuarios@Verificar');

// Verificar celular Usuarios
Route::get('/usuarios/verificarCel', 'APIUsuarios@VerificarCel');

//Email Verification Code
Route::get('/verify/{verification_code}', 'APIUsuarios@VerifyMail');

// Verificar id FB Usuarios
Route::get('/usuarios/verificarFB', 'APIUsuarios@VerificarFB');

// Prueba SMS
Route::get('/usuarios/enviarsms', 'APIUsuarios@SMS');
Route::get('/usuarios/verifyCode', 'APIUsuarios@VerificarSMS');

//lanzamiento de correos
Route::get('/mailsLauncher', 'MailsLauncher@mailsLauncher');