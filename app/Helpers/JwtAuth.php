<?php


namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\DB;
use App\Models\User;


class JwtAuth
{
    public $key;

    public function __construct()
    {
        $this->key = 'dfernandez';
    }

    public function signup($email, $password, $getToken = null)
    {

        $user = User::where('email', '=', $email)->first();

        if (is_object($user)) {
            if (password_verify($password, $user->password)) {
                $signup = true;
            } else {
                $signup = false;
            }

            if ($signup) {
                //Generar token y devolverlo
                $token = [
                    'sub' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'iat' => time(),
                    'exp' => time() + (7 * 24 * 60 * 60)
                ];
                $jwt = JWT::encode($token, $this->key, 'HS256');
                $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));


                if (is_null($getToken)) {
                    return $jwt;
                } else {
                    return $decoded;
                }


            } else {
                return ['status' => 'error', 'message' => 'Login ha fallado'];
            }
        }
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;
        $decoded = null;

        try {
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));

        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if (is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }

        if ($getIdentity) {
            return $decoded;
        }

        return $auth;

    }

}
