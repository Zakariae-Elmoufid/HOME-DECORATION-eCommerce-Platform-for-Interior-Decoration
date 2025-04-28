<?php

namespace App\Services;

require_once __DIR__ . '/../../vendor/autoload.php';

use Stripe\Stripe;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey('sk_test_51RAgELH2nPPbXqXkVPvJUTDRyYguZujueuEi1dbXrR6NA7IrOyKldiiEKdnBpKMA7SrVFElluLNiZOpmsnh8o6F800mpbbJnDJ'); 
    }
}
