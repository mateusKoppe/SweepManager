<?php

namespace App\Controllers;

use App\Models\StudantModel;
use App\Services\Auth;

class StudantController extends Controller
{
    public function create($params)
    {
        $user = Auth::requireLogin();
        $class_id = $params['class'];
        if($user->id !== $class_id) {
            Auth::unautorized();
        }
        $studant = new StudantModel();
        $studant->name = $this->body->name;
        $studant->class = $class_id;
        $success = $studant->save();
        if($success){
            $this->json($studant->getContentData(), 201);
        } else {
            $this->json(null, 400);
        }
    }

    public function list($params)
    {
        $user = Auth::requireLogin();
        $class_id = $params['class'];
        if($user->id !== $class_id) {
            Auth::unautorized();
        }
        $studants = StudantModel::listByClassId($class_id);
        if($studants === false){
            $this->json([
                'error' => "class not found"
            ], 404);
            exit();
        }
        $this->json($studants);
    }

    public function updateMultiple($params)
    {
        $user = Auth::requireLogin();
        $class_id = $params['class'];
        if($user->id !== $class_id) {
            Auth::unautorized();
        }
        $body_studants = $this->body->studants;
        $studants_success = [];
        foreach($body_studants as $studant_data) {
            $studant = new StudantModel();
            $studant->name = $studant_data->name;
            $studant->times = $studant_data->times;
            $studant->class = $class_id;
            $studant->id = $studant_data->id;
            $success = $studant->update();
            if($success) {
              $studants_success[] = [
                "id" => $studant->id,
                "name" => $studant->name,
                "times" => $studant->times,
                "class" => $studant->class
              ];
            }
        }
        $this->json($studants_success, 200);
    }

    public function delete($params)
    {
        $user = Auth::requireLogin();
        $class_id = $params['class'];
        if($user->id !== $class_id) {
            Auth::unautorized();
        }
        $studant = new StudantModel();
        $studant->id = $params["id"];
        $studant->class = $class_id;
        if($studant->destroy()){
          $this->json(['id' => $params["id"]], 200);
        } else {
          $this->status(409);
        }
    }

}
