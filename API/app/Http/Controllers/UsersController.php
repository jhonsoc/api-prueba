<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\Relation;
use App\Models\Account;
use App\Http\Controllers\Controller as apiController;
use Hash;

class UsersController extends apiController
{
   /***** lista de usuarios y por id ****/
    function list(Request $request){
        $db = new Users();
        try{
            if(isset($request->id)){
                $users = Users::select( 'users.id as idUser',
                'users.first_name',
                'users.last_name',
                'users.identification',
                'users.email',
                'users.pass',
                'users.created_at as user_created',
                'users.updated_at',
                    'account.id as idAccount',
                    'account.value',
                    'account.type',
                    'account.state',
                    'account.created_at as account_created'
                )
                ->join('user_account', 'users.id', '=', 'user_account.id_user')
                ->join('account', 'account.id', '=', 'user_account.id_account')
                ->where('users.id','=',$request->id)
                ->whereNull('users.deleted_at')// usuario no eliminado
                ->get()->toArray();
                /* validacion que exista el usuario por id */
                if(empty($users)){
                    return \Response::json(['User not exist'], 500);
                }
            }else{
                $users = Users::select( 
                    'users.id as idUser',
                    'users.first_name',
                    'users.last_name',
                    'users.identification',
                    'users.email',
                    'users.pass',
                    'users.created_at as user_created',
                    'users.updated_at',
                        'account.id as idAccount',
                        'account.value',
                        'account.type',
                        'account.state',
                        'account.created_at as account_created'
                    )
                ->join('user_account', 'users.id', '=', 'user_account.id_user')
                ->join('account', 'account.id', '=', 'user_account.id_account')
                ->whereNull('users.deleted_at')// usuario no eliminado
                ->get()->toArray();
            }
            /* valida que no venga vacio los usuarios */
            if(!empty($users)){
                /* variable final */
                $data = [];
                /* validador de usuarios el id */
                $validate_user = 0;
                foreach ($users as $item) {
                    /* se inicializan las variables */
                    $response = [];
                    $user_data = [];
                    $account_data = [];
                    $total=0;
                    $count = 0;
                    /* se crea el arreglo de usuario */
                    $user_ = array(
                        'idUser'=> $item['idUser'],
                        'first_name'=> $item['first_name'],
                        'last_name'=> $item['last_name'],
                        'identification'=> $item['identification'],
                        'email'=> $item['email'],
                        'pass'=> $item['pass'],
                        'user_created'=> $item['user_created']
                    );
                    /* se valida los usuarios */
                    if($validate_user != $item['idUser']){
                        /* se buscan las cuentas para este usuario */
                        foreach ($users as $accounts) {
                            if($item['idUser'] == $accounts['idUser']){
                                $state_ = "";
                                if($accounts['state'] == 0)
                                    $state_ = "Activa";
                                if($accounts['state'] == 1)
                                    $state_ = "Inactiva";
                                if($accounts['state'] == 2)
                                    $state_ = "Congelada";
                                if($accounts['state'] == 3)
                                    $state_ = "Traslado";


                                $account_ = array(
                                    'idAccount'=> $accounts['idAccount'],
                                    'value'=> $accounts['value'],
                                    'type'=> $accounts['type'] == 0 ? 'Ahorro':'Corriente',
                                    'state'=> $state_,
                                    'dateCreated'=> $accounts['account_created']
                                );
                                $count++;
                                /* se guardan en arry push la cuentas */
                                array_push($account_data,$account_);
                                /* se suma el total de las cuentas */
                                $total+=$accounts['value'];
                            }
                        }
                        /* se cambia el id usuario validador */
                        $validate_user = $item['idUser'];
                        /* se crea el usuario en el arreglo */
                        array_push($user_data,$user_);
    
                        $response = array(
                            'user'=>$user_data,
                            '# accounts' =>$count,
                            'total money' => $total,
                            'accounts'=>$account_data
                        );
                        /* se llena el arreglo final */
                        array_push($data,$response);
                    }
                }
                return response()->json(['data' => $data]);
            }else{
                return \Response::json(['Empty Users']);
            }
        }catch (Exception $e) {
            \Log::info('Error list users: '.$e);
            return \Response::json(['list error'], 500);
        }
    }
    /***** funcion para registrar user ****/

    function create(Request $request){
        try{
            $newUser = new Users();
            $newAccount = new Account();
            $newRelation = new Relation();
            /* valida que no exista esa misma identificacion */
            $validate_identification = $newUser::all()->where('identification','=',$request->identification);
            if($validate_identification->count()>0){
                return \Response::json(['identification exist'], 500);
            }

            /* valida los campos obligatorios */
            $request->validate([
                "first_name"=> "required",
                "last_name"=> "required",
                "identification"=> "required",
                "email"=> "required",
                "pass"=> "required",
                "type"=> "required",
                "state"=> "required",
            ]);
            /* se arma el objeto user */
            $newUser->first_name = $request->first_name;
            $newUser->last_name = $request->last_name;
            $newUser->identification = $request->identification;
            $newUser->email = $request->email;
            #guarda encriptada la contraseña
            $newUser->pass = \Hash::make($request->pass);

            /* datos de cuenta */
            $newAccount->value = 0;
            $newAccount->type  = $request->type > 1 ? 0 : $request->type;
            $newAccount->state = $request->state > 3 ? 0 : $request->state; 

            if($newUser->save()){
                if($newAccount->save()){
                    /* datos de relacion */
                    $newRelation->id_user = $newUser->id;
                    $newRelation->id_account = $newAccount->id;
                    if($newRelation->save()){
                        return \Response::json(['user create and account new']);
                    }
                }
                return \Response::json(['relation not created']);
            }else{
                return \Response::json(['user not created'], 500);
            }
        }catch (Exception $e) {
            \Log::info('Error list products: '.$e);
            return \Response::json(['not list'], 500);
        }
    }
    /***** funcion para registrar actualizar user ****/

    function update(Request $request){
        try{
            $db = new Users();
            /* valida los campos oblogatorios y que exista el usuario */
            if(!isset($request->id)){
                return \Response::json(['id not sent'], 500);
            }
            $updateUser = $db::find($request->id);
            if(!isset($updateUser)){
                return \Response::json(['id don´t exist'], 500);
            }
            $request->validate([
                'id' => 'required'
            ]);
            /* validar identificacion que no se repita */
            $validate_identification = $db::all()->where('identification','=',$request->identification);
            if($validate_identification->count()>0){
                return \Response::json(['identification exist'], 500);
            }

            /* se arma el objeto para actualizar si no llegan variables no se actualizan */
            if(isset($request->first_name))
                $updateUser->first_name = $request->first_name;

            if(isset($request->last_name))
                $updateUser->last_name = $request->last_name;
            
            if(isset($request->identification))
                $updateUser->identification = $request->identification;
            
            if(isset($request->email))
                $updateUser->email = $request->email;

                $date = Carbon::now();
                $date = $date->modify('-5 hours');//hora colombia

                $updateUser->updated_at = $date->format('Y-m-d H:i:s');
            
            if($updateUser->save()){
                return \Response::json(['user update']);
            }else{
                return \Response::json(['user not updated' => false], 500);
            }
        }catch (Exception $e) {
            \Log::info('Error update user: '.$e);
            return \Response::json(['not update'], 500);
        }
    }
    /***** funcion para eliminar user ****/
    function delete(Request $request){
        try{
            $db = new Users();
            $date = Carbon::now();
            $date = $date->modify('-5 hours');//hora colombia

            $user = $db::find($request->id);
            $user ->deleted_at = $date->format('Y-m-d H:i:s');

            if($user->save()){
                return \Response::json(['user deleted']);
            }else{
                return \Response::json(['user not deleted'], 500);
            }
 
        }catch (Exception $e) {
            \Log::info('Error delete user: '.$e);
            return \Response::json(['not deleted'], 500);
        }
    }

    /***** funcion para recargar una cuenta user ****/
    function recharge(Request $request){
        try{
            $db = new Users();
            $db_ = new Relation();
            $db__ = new Account();
            if(!isset($request->identification))
                return \Response::json(['identification not isset']);
            if(!isset($request->account))
                return \Response::json(['account not isset']);
            /* se busca por identificacion el id del usuario */
            $user = $db::all()->where('identification','=',$request->identification);

            if($user->count()==0)
                return \Response::json(['identification not exist']);
            
            foreach ($user as $item) {
                $idUser =$item->id;
            }
            /* se guarda el id del usuario */
            
            $validateAccount = $db_::all()
                                    ->where('id_user','=',$idUser)
                                    ->where('id_account','=',$request->account);

            if($validateAccount->count() == 0)
                return \Response::json(['account not associated with this user']);

            /* ya validado que si existe la cuenta y esta asociada a este usuario se hace la recarga */
            $recharge = $db__::find($request->account);
            
            $date = Carbon::now();
            $date = $date->modify('-5 hours');//hora colombia
            $recharge->updated_at = $date->format('Y-m-d H:i:s');

            if(!isset($request->amount))
                return \Response::json(['amount not exist']);
            
            if($request->amount <= 0)
                return \Response::json(['the amount must be greater than 0']);
                
            /* se agrega el monto */
            $recharge->value = $recharge->value + $request->amount;
            if($recharge->save()){
                return \Response::json(['account recharge']);
            }else{
                return \Response::json(['account not recharge' => false], 500);
            }
        }catch (Exception $e) {
            \Log::info('Error recharge account: '.$e);
            return \Response::json(['not recharge'], 500);
        }
    }

    /***** funcion para transferir una cuenta a otra ****/
    function transfer(Request $request){
        try{
            $db = new Users();
            $db_ = new Relation();
            $db__ = new Account();
            if(!isset($request->identification_send))
                return \Response::json(['identification send not isset']);

            if(!isset($request->account_send))
                return \Response::json(['account send not isset']);

            if(!isset($request->identification_receives))
                return \Response::json(['identification receives not isset']);

            if(!isset($request->account_receives))
                return \Response::json(['account receives not isset']);

            /* se valida que si es el mismo usuario que desea enviar dinero de una cuenta a otra que tenga debe ser cuentas distintas */
            if($request->identification_send == $request->identification_receives ){
                if($request->account_send==$request->account_receives){
                    return \Response::json(['you cannot transfer to the same account']);
                }
            }
                
            /* se busca por identificacion el id del usuario que envia */
            $user_send = $db::all()->where('identification','=',$request->identification_send);
            if($user_send->count()==0)
                return \Response::json(['identification send not exist']);
                
            /* se guarda el id del usuario que envia */
            foreach ($user_send as $item) {
                $idUser_send = $item->id;
            }

            /* se busca por identificacion el id del usuario que recibe */
            $user_receives = $db::all()->where('identification','=',$request->identification_receives);
            if($user_receives->count()==0)
                return \Response::json(['identification receives not exist']);

            /* se guarda el id del usuario que recibe */
            foreach ($user_receives as $item) {
                $idUser_receives = $item->id;
            }
            /* se valida que la cuenta si este ligada al usuario que envia */
            $validateAccount_send = $db_::all()
                                    ->where('id_user','=',$idUser_send)
                                    ->where('id_account','=',$request->account_send);

            if($validateAccount_send->count() == 0)
                return \Response::json(['account not associated with this user send']);

            /* se valida que la cuenta si este ligada al usuario que recibe */
            $validateAccount_receives = $db_::all()
                                ->where('id_user','=',$idUser_receives)
                                ->where('id_account','=',$request->account_receives);

            if($validateAccount_receives->count() == 0)
                return \Response::json(['account not associated with this user receives']);

            /* ya validado que si existe la cuenta y esta asociada a este usuario que envia se descuenta el monto a enviar */

            /* cuenta que envia */
            $transfer_send = $db__::find($request->account_send);
            /* cuenta que recibe */
            $transfer_receives = $db__::find($request->account_receives);
            
            $date = Carbon::now();
            $date = $date->modify('-5 hours');//hora colombia
            $transfer_send->updated_at = $date->format('Y-m-d H:i:s');
            $transfer_receives->updated_at = $date->format('Y-m-d H:i:s');

            if(!isset($request->amount))
                return \Response::json(['amount not exist']);
            
            /* se valida que el monto sea mayor a 0 y aparte que se valide que puede transferir esa cantidad de dinero */
            if($request->amount <= 0)
                return \Response::json(['the amount must be greater than 0']);
 
            if(doubleval($request->amount) >= $transfer_send->value)

                return \Response::json(['insufficient funds']);
             
            /* se descuenta el monto que envia */
            $transfer_send->value = $transfer_send->value - $request->amount;
            /* se agrega el monto que recibe */
            $transfer_receives->value = $transfer_send->value + $request->amount;
            if($transfer_send->save()){
                if($transfer_receives->save()){
                    return \Response::json(['transfer success']);
                }else{
                    $transfer_send->value = $transfer_send->value + $request->amount;
                    if($transfer_send->save()){
                        return \Response::json(['transfer error, refund of money']);
                    }else{
                        return \Response::json(['refund of money error contact the administrator']);
                    }
                }
                
            }else{
                return \Response::json(['account not send' => false], 500);
            }
        }catch (Exception $e) {
            \Log::info('Error recharge account: '.$e);
            return \Response::json(['not recharge'], 500);
        }
    }
}
