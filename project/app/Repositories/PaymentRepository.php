<?php

namespace App\repositories;

use App\Core\DatabaseConnection;
use App\Models\Payment;
use PDO;

class PaymentRepository  extends BaseRepository{
    
    private $table = "payments";

    public function create(array $data) {

          $payment_id =   $this->insert($this->table , $data);

          $data['id'] = $payment_id;
          $payment =  new Payment($data);
          return $payment;
    }

    public function getById(int $paymentId): ?Payment {
        $paymentData  = $this->findById($this->table ,$paymentId);

        if (!$paymentData) {
            return null;
        }
        
        return new Payment($paymentData);
    }

    public function getByOrderId(int $orderId) {
        $stmt = $this->query("SELECT * FROM payments WHERE order_id = ?" , [$orderId]);
        $paymentData  = $stmt->fetch(PDO::FETCH_ASSOC);
        dump($paymentData);
        if (!$paymentData) {
            return null;
        }
        
        return new Payment($paymentData);
    }


    
    

    public function getByPaymentIntentId(string $paymentIntentId): ?Payment {
        $paymentData  =  $this->findBy($this->table , ['payment_intent_id' => $paymentIntentId]);        
        if (!$paymentData) {
            return null;
        }
        
        return new Payment($paymentData);
    }

    // public function update(int $paymentId, array $data): bool {
    //     try {
    //         $this->db->beginTransaction();
            
    //         $updateFields = [];
    //         $bindValues = [':id' => $paymentId];
            
    //         foreach ($data as $field => $value) {
    //             $updateFields[] = "$field = :$field";
    //             $bindValues[":$field"] = $value;
    //         }
            
    //         $sql = "UPDATE payments SET " . implode(', ', $updateFields) . " WHERE id = :id";
            
    //         $stmt = $this->db->prepare($sql);
            
    //         foreach ($bindValues as $param => $value) {
    //             $stmt->bindValue($param, $value);
    //         }
            
    //         $result = $stmt->execute();
    //         $this->db->commit();
            
    //         return $result;
    //     } catch (\Exception $e) {
    //         $this->db->rollBack();
    //         throw $e;
    //     }
    // }
}