<?php
namespace App\Controllers;
use App\Core\Controller;
use App\core\Request;
use App\core\Validator;

class HomeController extends Controller{


    public function index()
    {
        return $this->render('home', [
            'name' => 'Zakaria'
        ]);
    }

    public function create(Request $request){
        $data = $request->getBody();
        $validator = new Validator($data);
        
        $validator->setRules([
            'name' => 'required|min:8',
            'email' => 'required|email',
        ]);
        $validator->setMessages([
            'name.required' => 'The name field cannot be empty.',
            'name.min' => 'The name must have at least 3 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.'
        ]);
        $errors = [];
        $oldData = $data;
        if (!$validator->validate()) {
            $errors = $validator->getErrors();
            return $this->render('home', [
                'errors' => $errors,
                'old' => $oldData
            ]);
            
        }

        return $this->render('home', [
            'success' => 'Données créées avec succès'
        ]);

    }

}