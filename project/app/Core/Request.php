<?php


namespace App\Core;

class Request {

    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    
    public function isGet()
    {
        return $this->getMethod() === 'get';
    }
    public function isPost()
    {
        return $this->getMethod() === 'post';
    }


    public function getUrl()
    {
        $path = $_SERVER['REQUEST_URI'];
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        return $path;
    }

    public function getIdFromQuery()
    {
        return $_GET['id'] ?? null;
    }

    




    public function getbody()
    {
        $body = [];
        if($this->getMethod()==='get')
        {   
           
                $body['id'] = $_GET['id'];

        }

        if($this->getMethod()==='post')
        {   
                $rawData = file_get_contents("php://input");
                $jsonData = json_decode($rawData, true);
                
                if ($jsonData) {
                    foreach ($jsonData as $key => $value) {
                        $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                } else {
                    foreach ($_POST as $key => $value) {
                        $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
        }
        
        if($this->getMethod()==='patch'){
            $rawData = file_get_contents("php://input");
            $jsonData = json_decode($rawData, true);            
            if ($jsonData) {
                foreach ($jsonData as $key => $value) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            } else {
                foreach ($_POST as $key => $value) {
                    $body[$key] = filter_input(INPUT_PATCH, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
        if($this->getMethod()==='delete'){
            $rawData = file_get_contents("php://input");
            $jsonData = json_decode($rawData, true);            
            if ($jsonData) {
                foreach ($jsonData as $key => $value) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            } else {
                foreach ($_DELETE as $key => $value) {
                    $body[$key] = filter_input(INPUT_DELETE, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
        
        return $body;
    }

  


}