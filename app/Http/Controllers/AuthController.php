<?php

namespace App\Http\Controllers;

use App\Models\TaskModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController
{

    public function register(Request $request) {

        // validate data
            $validation = validator($request->json()->all(),[
                'email' => 'required|email|unique:App\Models\UserModel,email',
                'username' => 'required|unique:App\Models\UserModel,username',
                'first_name' => 'required',
                'last_name' => 'required',
                'password' => 'required',
            ]);

            if ($validation->fails()) {
                $response = [
                    'statusCode' => 400,
                    'status' => 'failed',
                    'message' => $validation->errors(),
                    'data' => []
                ];
        
                return response(json_encode($response),400);
            }
        // end validate data

        // inserting data
            $userData = [
                'email' => $request->json()->get('email'),
                'username' => $request->json()->get('username'),
                'first_name' => $request->json()->get('first_name'),
                'last_name' => $request->json()->get('last_name'),
                'password' => $request->json()->get('password'),
            ];

            $dataHasInserted = $this->userModel->addUserData($userData);
            
            if (!$dataHasInserted) {
                $response = [
                    'statusCode' => 500,
                    'status' => 'failed',
                    'message' => 'an error occurred in the server',
                    'data' => []
                ];
        
                return response(json_encode($response),500);
            }

            $response = [
                'statusCode' => 201,
                'status' => 'ok',
                'message' => 'Your registration is successfully',
                'data' => []
            ];
            
            return response(json_encode($response),201);
        // end inserting data
        
    }

    public function login(Request $request) {

        // validate data
            $validation = validator($request->json()->all(),[
                'username' => 'required',
                'password' => 'required',
            ]);

            if ($validation->fails()) {
                $response = [
                    'statusCode' => 400,
                    'status' => 'failed',
                    'message' => $validation->errors(),
                    'data' => []
                ];
        
                return response(json_encode($response),400);
            }
        // end validate data

        $requestData = [
            'username' => $request->json()->get('username'),
            'password' => $request->json()->get('password'),
        ];

        // check email is registered

            $userData = $this->userModel->getUserData($requestData['username']);

            if ($userData == null) {
                $response = [
                    'statusCode' => 400,
                    'status' => 'failed',
                    'message' => ["email" => ["username or email is not registered"]],
                    'data' => []
                ];
        
                return response(json_encode($response),400);
            }

        // end check email is registered

        // check password
            
            if (!password_verify($requestData['password'],$userData->password)) {
                $response = [
                    'statusCode' => 400,
                    'status' => 'failed',
                    'message' => ["password" => ["password invalid"]],
                    'data' => []
                ];
        
                return response(json_encode($response),400);
            }

        // end check password
        
        // generate token jwt
        $payload = [
            'userId' => $userData->id_user,
            'username' => $userData->username,
            'email' => $userData->email,
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
        ];

        $jwtToken = JWT::encode($payload,env('JWT_SECRET'),'HS256');
        // end generate token jwt

        $response = [
            'statusCode' => 200,
            'status' => 'failed',
            'message' => '',
            'data' => [
                'token' => $jwtToken
            ]
        ];

        return response(json_encode($response),200);
        

    }
}
