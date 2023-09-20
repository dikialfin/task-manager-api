<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends BaseController
{
    
    public function getUserProfile(Request $request) {

        $user = JWT::decode($request->header('authorization'), new Key(env('JWT_SECRET'),'HS256'));

        $userProfileData = $this->userModel->getUserData($user->username);

        if ($userProfileData == null) {
            $response = [
                'statusCode' => 404,
                'status' => 'failed',
                'message' => ['userData' => 'user data not found'],
                'data' => []
            ];
    
            return response(json_encode($response),404);
        }

        $response = [
            'statusCode' => 200,
            'status' => 'success',
            'message' => '',
            'data' => [
                'userData' => [
                    'email' => $userProfileData->email,
                    'username' => $userProfileData->username,
                    'first_name' => $userProfileData->first_name,
                    'last_name' => $userProfileData->last_name,
                    'created_at' => $userProfileData->created_at,
                ]
            ]
        ];

        return response(json_encode($response),200);

    }

    public function createTask(Request $request) {

        // validasi data

        $validation = validator($request->json()->all(),[
            'title' => 'required',
            'description' => 'required',
            'deadline' => 'required|date',
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

        // end validasi data

        $userId = JWT::decode($request->header('authorization'), new Key(env('JWT_SECRET'),'HS256'))->userId;
        $taskData = $request->json()->all();
        $taskData['id_user'] = $userId;

        // insert data

        if ($this->taskModel->addTask($taskData)) {
            $response = [
                'statusCode' => 200,
                'status' => 'success',
                'message' => '',
                'data' => []
            ];
    
            return response(json_encode($response),200);
        }

        $response = [
            'statusCode' => 500,
            'status' => 'failed',
            'message' => ['task' => 'an error occurred in the server'],
            'data' => []
        ];

        return response(json_encode($response),500);

        // end insert data

    }

    public function getTask(Request $request) {

        $userId = JWT::decode($request->header('authorization'), new Key(env('JWT_SECRET'),'HS256'))->userId;

        $taskData = $this->taskModel->getAllTask($userId);

        if ($taskData == null) {
            $response = [
                'statusCode' => 404,
                'status' => 'failed',
                'message' => ['task' => 'task not found'],
                'data' => []
            ];
    
            return response(json_encode($response),404);
        }

        $response = [
            'statusCode' => 200,
            'status' => 'success',
            'message' => '',
            'data' => ['task' => $taskData]
        ];

        return response(json_encode($response),200);

    }

    public function getTaskById(Request $request, $id_task) {

        $dataTask = $this->taskModel->getTaskById($id_task);

        if ($dataTask == null) {
            $response = [
                'statusCode' => 404,
                'status' => 'failed',
                'message' => ['task' => 'task not found'],
                'data' => []
            ];
    
            return response(json_encode($response),404);
        }

        $response = [
            'statusCode' => 200,
            'status' => 'success',
            'message' => '',
            'data' => [
                'task' => $dataTask
            ]
        ];

        return response(json_encode($response),200);

    }

    public function editTask(Request $request, $id_task) {


        // validasi data

        $validation = validator($request->json()->all(),[
            'title' => 'required',
            'description' => 'required',
            'deadline' => 'required|date',
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

        // end validasi data

        $userId = JWT::decode($request->header('authorization'), new Key(env('JWT_SECRET'),'HS256'))->userId;
        $taskData = [
            'id_user' => $userId,
            'id_task' => $id_task,
            'title' => $request->json()->get('title'),
            'description' => $request->json()->get('description'),
            'deadline' => $request->json()->get('deadline'),
        ];

        // update data

        if ($this->taskModel->updateTask($taskData)) {
            $response = [
                'statusCode' => 200,
                'status' => 'success',
                'message' => '',
                'data' => []
            ];
    
            return response(json_encode($response),200);
        }

        // end update data

        $response = [
            'statusCode' => 500,
            'status' => 'failed',
            'message' => ['task' => 'failed to updating task'],
            'data' => []
        ];

        return response(json_encode($response),500);


    }

    public function deleteTask(Request $request, $id_task) {

        $userId = JWT::decode($request->header('authorization'), new Key(env('JWT_SECRET'),'HS256'))->userId;

        if ($this->taskModel->deleteTask($id_task, $userId)) {
            $response = [
                'statusCode' => 200,
                'status' => 'success',
                'message' => '',
                'data' => []
            ];
    
            return response(json_encode($response),200);
        }

        $response = [
            'statusCode' => 500,
            'status' => 'failed',
            'message' => ['task' => 'failed to deleting task'],
            'data' => []
        ];

        return response(json_encode($response),500);

    }

    public function setTaskDone(Request $request, $id_task) {

        $userId = JWT::decode($request->header('authorization'), new Key(env('JWT_SECRET'),'HS256'))->userId;

        if ($this->taskModel->setTaskDone($userId,$id_task)) {
            $response = [
                'statusCode' => 200,
                'status' => 'success',
                'message' => '',
                'data' => []
            ];
    
            return response(json_encode($response),200);
        }

        $response = [
            'statusCode' => 500,
            'status' => 'failed',
            'message' => ['task' => 'failed to updating task'],
            'data' => []
        ];

        return response(json_encode($response),500);

    }

}