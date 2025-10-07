<?php

namespace App\Services;

class PayMongoService {
    protected $baseUrl = '';
    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = config('paymongo.secret')[config('paymongo.mode')];
    }
}