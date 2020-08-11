<?php

namespace ctrlv\ServiceRequest;

class JsonServiceRequest extends ServiceRequest {

    public function __construct(array $data) {
        $this->format = 'json';
        $this->params = json_encode($data);
    }

    /**
     * Отправить запрос
     * @return array
     * @throws \Exception
     */
    public function send() : array {

        if (empty($this->url)) {
            throw new \Exception('Адрес отправки запроса не задан');
        }

        if ($this->url != '#') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            $data = curl_exec($ch);
            curl_close($ch);
        } else {
            // Рандомный вариант ответа для фейковых запросов
            $data = $this->getRandomResponse();
        }

        // Конвертировать ответ в массив
        $array_data = json_decode($data, true);

        return $array_data;
    }

    /**
     * Рандомно возвращает один из возможных вариантов ответа
     * @return string
     */
    private function getRandomResponse() : string {
        $responses = [
            '{"SubmitDataResult":"success"}',
            '{"SubmitDataResult":"reject"}',
            '{"SubmitDataResult":"error", "SubmitDataErrorMessage":""}'
        ];
        return $responses[array_rand($responses)];
    }


}