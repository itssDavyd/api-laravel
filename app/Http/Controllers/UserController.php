<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $json = $request->input('json', null);
        $params = json_decode($json);
        $data = [];

        //Comprobamos las existencias de lo que llega por el REQUEST DEL POST
        $email = (!is_null($json) && isset($params->email) ? $params->email : null);
        $name = (!is_null($json) && isset($params->name) ? $params->name : null);
        $surname = (!is_null($json) && isset($params->surname) ? $params->surname : null);
        $role = 'ROLE_USER';
        $password = (!is_null($json) && isset($params->password) ? $params->password : null);

        if (!is_null($email) && !is_null($name) && !is_null($surname) && !is_null($password)) {

            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $user->surname = $surname;
            $user->role = $role;

            //Hash de la contraseña cifrada.
            $pwd = password_hash($password, PASSWORD_BCRYPT);
            $user->password = $pwd;

            //Comprobar duplicidad del usuario.
            $isset_user = User::where('email', '=', $email)->first();

            //Tambien se podria con el $isset_user->exists() para comprobar si existe en la bdd, si es asi tira error si no deja guardar.
            $arr_users = json_decode(json_encode($isset_user), true);

            if (!empty($arr_users) && count($arr_users) != 0) {
                //No guarda
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Usuario duplicado no puede registrarse'
                ];
            } else {
                //Guarda
                $user->save();

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha registrado correctamente'
                ];
            }

        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Usuario no creado'
            ];
        }
        return response()->json($data, 200);
    }

    public function login(Request $request)
    {

        $jwtAuth = new JwtAuth();

        //recibir los datos por POST.
        $json = $request->input('json', null);
        $params = json_decode($json);

        //Validaciones de si llega el dato y si existe el parametro.
        $email = (!is_null($json) && isset($params->email) ? $params->email : null);
        $password = (!is_null($json) && isset($params->password) ? $params->password : null);
        $getToken = (!is_null($json) && isset($params->getToken) ? $params->getToken : null);

        //cifrar password
        if (!is_null($email) && !is_null($password) && (is_null($getToken) || $getToken == 'false')) {
            $signup = $jwtAuth->signup($email, $password);

        } elseif (!is_null($getToken)) {
            $signup = $jwtAuth->signup($email, $password, $getToken);

        } else {
            $signup = [
                'status' => 'error',
                'message' => 'Envia tus datos por POST'
            ];
        }

        return response()->json($signup, 200);

    }
}
