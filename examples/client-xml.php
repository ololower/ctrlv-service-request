<?php

require_once __DIR__. '/../vendor/autoload.php';

use ctrlv\ServiceRequest\ServiceRequest;

$data = [
    "firstName"     => "Vasya",
    "lastName"      => "Pupkin",
    "dateOfBirth"   => "1984-07-31",
    "Salary"    	=> "1000",
    "creditScore"   => "good"
];


try {

    $request = ServiceRequest::make($data, 'xml');

    // Получение ответа.
    // Для фейковой отправки устанавливаем url = #
    // Для реальной отправки задаем реальный url
    $response = $request->setUrl('#')->getResponse();

    // Определение статуса (в зависимости от ответа)
    print_r($response->getCode());

} catch (Exception $e) {
    print_r($e->getMessage());
}

