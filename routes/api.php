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