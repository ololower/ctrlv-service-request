<?php

namespace ctrlv\ServiceRequest;

class ServiceResponse {

    private $code;

    public function __construct($code) {
        $this->code = $code;
    }

    public static function make($code) {
        return new self($code);
    }

    public function getCode() {
        return $this->code;
    }
}