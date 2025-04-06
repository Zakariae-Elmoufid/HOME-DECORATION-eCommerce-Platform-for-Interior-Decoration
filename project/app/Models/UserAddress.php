<?php 

namespace App\Models;

class UserAddress {
    private $id;
    private $user_id;
    private $first_name;
    private $last_name;
    private $email;
    private $phone;
    private $address;
    private $city;
    private $postal_code;
    private $country;
    private $created_at;
    private $updated_at;

    public function __construct($data = []) {
        $dataArray = is_object($data) ? get_object_vars($data) : $data;

        $this->id = $dataArray['id'] ?? null;
        $this->user_id = $dataArray['user_id'] ?? null;
        $this->first_name = $dataArray['first_name'] ?? null;
        $this->last_name = $dataArray['last_name'] ?? null;
        $this->email = $dataArray['email'] ?? null;
        $this->phone = $dataArray['phone'] ?? null;
        $this->address = $dataArray['address'] ?? null;
        $this->city = $dataArray['city'] ?? null;
        $this->postal_code = $dataArray['postal_code'] ?? null;
        $this->country = $dataArray['country'] ?? null;
        $this->created_at = $dataArray['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $dataArray['updated_at'] ?? date('Y-m-d H:i:s');
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }


    public function getFirstName() {
        return $this->first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function getEmail() {
        return $this->email;
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