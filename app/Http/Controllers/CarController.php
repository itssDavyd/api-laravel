<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\Car;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

/**
 *
 * TODO: OJO se ve que se usa mucho la funcion load(user) esto es por que al ser una API sin vistas tenemos que indicarle a LARAVEL que cargue los datos del USUARIO, en caso de que estuviesemos usando vistas desde este proyecto lo haria por defecto.
 * Class CarController
 * @package App\Http\Controllers
 */
class CarController extends Controller
{
    /**
     * Carga el listado de todos los coches que existen en la BDD pero con la relacion del Usuario.
     * @param Request $request
     * @return array|false|string|null
     */
    public function index(Request $request)
    {
        $cars = Car::all()->load('user');
        return response()->json([
            'status' => 'success',
            'cars' => $cars
        ], 200);
    }

    /**
     * Este metodo es aquel que le pasas un id del coche y te devuelve los datos de ese coche en concreto. (1->id->1->result)
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $car = Car::find($id)->load('user');

        return response()->json([
            'status' => 'success',
            'car' => $car
        ], 200);
    }

    /**
     * Este metodo sirve para validar el token Authorization que llega por la cabecera de la REQUEST.
     * @param Request $request
     * @return array|false|string|null
     */
    public function validatorTheToken(Request $request)
    {
        $jwtAuth = new JwtAuth();
        $hash = $request->header('Authorization', null);
        //Obtenemos de la cabecera del metodo GET la key y si es correcta por que checkToken nos devuelve que es TRUE permite el acceso al usuario por que quiere decir que este usuario tiene el mismo token que el de la BDD.
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            return $hash;
        } else {
            return false;
        }
    }

    //Creamos el CRUD.

    /**
     * Metodo para guardar los coches en la base de datos.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response|null
     */
    public function store(Request $request)
    {
        //Guardar los coches (es el create)
        $validatorTheToken = $this->validatorTheToken($request);


        if ($validatorTheToken != false) {
            //Datos POST
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            //Conseguir el usuario identificado
            $jwtAuth = new JwtAuth();
            $user = $jwtAuth->checkToken($validatorTheToken, true);

            /*try {
                $request->merge($params_array);
                //Validacion
                $validatedData = $this->validate($request, [
                    'title' => 'required|min:5',
                    'description' => 'required',
                    'price' => 'required',
                    'status' => 'required'
                ]);
            } catch (ValidationException $e) {
                return $e->getResponse();
            }*/

            //Forma de validacion de Facades (propia de laravel) Y siempre tenemos los errores guardados y devueltos.
            $validatedData = Validator::make($params_array, [
                'title' => 'required|min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);

            if ($validatedData->fails()) {
                return response()->json($validatedData->errors(), 400);
            }


            //Guardar coche.
            $car = new Car();
            //Guardar el id (esta dentro de sub) del usuario.
            $car->user_id = $user->sub;
            $car->title = $params->title;
            $car->description = $params->description;
            $car->price = $params->price;
            $car->status = $params->status;

            $car->save();

            $data = [
                'status' => 'success',
                'car' => $car,
                'code' => 200
            ];


        } else {
            //Devolver error
            $data = [
                'status' => 'error',
                'message' => 'Login incorrecto',
                'code' => 300
            ];
        }
        return response()->json($data, 200);
    }

    /**
     * Actualiza de la BDD un registro
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $validatorTheToken = $this->validatorTheToken($request);

        if ($validatorTheToken != false) {
            //Recogemos datos de la REQUEST.
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            //Forma de validacion de Facades (propia de laravel) Y siempre tenemos los errores guardados y devueltos.
            $validatedData = Validator::make($params_array, [
                'title' => 'required|min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);

            if ($validatedData->fails()) {
                return response()->json($validatedData->errors(), 400);
            }

            //Actualizar coche
            $car = Car::where('id', '=', $id)->update($params_array);

            $data = [
                'status' => 'success',
                'car' => $params,
                'code' => 200
            ];

        } else {
            //Devolver error
            $data = [
                'status' => 'error',
                'message' => 'Login incorrecto',
                'code' => 300
            ];
        }
        return response()->json($data, 200);


    }

    /**
     * Borra de la BDD un registro.
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        $validatorTheToken = $this->validatorTheToken($request);

        if ($validatorTheToken != false) {
            //Comprobar que existe el registro.
            $car = Car::find($id);

            //Borrarlo.
            $car->delete();

            //Devolver el registro borrado.
            $data = [
                'status' => 'success',
                'car' => $car,
                'code' => 200
            ];

        } else {
            //Devolver error
            $data = [
                'status' => 'error',
                'message' => 'Login incorrecto',
                'code' => 400
            ];
        }
        return response()->json($data, 200);
    }
}
