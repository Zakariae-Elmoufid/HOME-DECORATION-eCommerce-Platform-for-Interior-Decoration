<?php 

namespace App\Models;

class UserAddress extends User{
    private $id;
    private $phone;
    private $address;
    private $city;
    private $postal_code;
    private $country;
    private $created_at;
    private $updated_at;

    public function __construct($data = []) {
        $dataArray = is_object($data) ? get_object_vars($data) : $data;
        parent::__construct($dataArray['username'],$dataArray['email'],$dataArray['created_at'],2,null,$dataArray['user_id']);
        $this->id = $dataArray['id'] ?? null;
        $this->phone = $dataArray['phone'] ?? null;
        $this->address = $dataArray['address'] ?? null;
        $this->city = $dataArray['city'] ?? null;
        $this->postal_code = $dataArray['postal_code'] ?? null;
        $this->country = $dataArray['country'] ?? null;
        $this->created_at = $dataArray['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $dataArray['updated_at'] ?? date('Y-m-d H:i:s');
    }

   
    public function getId() {
        return $this->id;
    }

    




    public function getPhone() {
        return $this->phone;
    }

    public function getAddress() {
        return $this->address;
    }

   
    public function getCity() {
        return $this->city;
    }

    public function getState() {
        return $this->state;
    }

    public function getPostalCode() {
        return $this->postal_code;
    }

    public function getCountry() {
        return $this->country;
    }

   

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function getFullName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFormattedAddress() {
        $parts = [
            $this->address,
            $this->address2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ];
        
        $parts = array_filter($parts, function($part) {
            return !empty($part);
        });
        
        return implode(', ', $parts);
    }
}