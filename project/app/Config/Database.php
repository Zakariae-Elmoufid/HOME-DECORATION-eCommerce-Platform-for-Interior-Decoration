<?php
namespace App\config;
require __DIR__ . "/../../vendor/autoload.php";
use Dotenv\Dotenv;
use PDO;
use PDOException;

class Database
{

    private static $pdoSinglton;

    public static function getConnection()
    {


        if (self::$pdoSinglton != null) {
            return self::$pdoSinglton;
        } else {

           
            try {
                
                $dotenv = Dotenv::createImmutable(__DIR__.'/../../');
                $dotenv->load();
        
                
                $dsn = "mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_NAME'];
                $pdo_instance = new PDO($dsn, $_ENV['DB_USER'],$_ENV['DB_PASSWORD']);
                self::$pdoSinglton =  $pdo_instance ;
                return self::$pdoSinglton;
            } catch (PDOException $e) {
                print_r($_ENV);
                echo "Connection error:".$e->getMessage();
            }


        }


    }
}