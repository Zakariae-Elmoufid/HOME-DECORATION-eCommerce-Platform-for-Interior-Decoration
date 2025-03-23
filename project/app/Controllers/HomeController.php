<?php
namespace App\Controllers;
use App\Core\Controller;
use App\core\Request;
use App\core\Validator;
use App\Services\CategoryService;
use App\Services\ProductService;

class HomeController extends Controller{


    private $CategoryService;
    private $ProductService;

    public function __construct(){
        $this->CategoryService = new CategoryService() ; 
        $this->ProductService = new ProductService() ; 

    }

    public function index()
    {
        $categories =  $this->CategoryService->fechAll();
        $products = $this->ProductService->fetchAll();

        return $this->render('home', [
            'categories' => $categories,
            'products' => $products
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