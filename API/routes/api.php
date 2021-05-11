<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/* usuarios */
/* lista de productos v1/list/products y tambien por id*/
Route::get('v1/list/bank/users/list/{id?}',[UsersController::class, 'list']);
/* creacion de productos */
Route::post('v1/bank/create/user',[UsersController::class, 'create']);
/* actualizar user */
Route::post('v1/bank/update/user',[UsersController::class, 'update']);
/* eliminar user */
Route::get('v1/bank/delete/user/{id}',[UsersController::class, 'delete']);

/* servicios bancarios */
/* recargar una cuenta se pide identificacion y numero de la cuenta*/
Route::post('v1/bank/account/recharge',[UsersController::class, 'recharge']);
/* transferencia una cuenta a otra cuenta*/
Route::post('v1/bank/account/transfer',[UsersController::class, 'transfer']);