<?php

namespace ctrlv\ServiceRequest;

abstract class ServiceRequest {

    /**
     * @var string адрес для отправки запроса
     */
    protected $url;

    /**
     * Параметры запроса
     * @var string
     */
    protected $params;

    /**
     * @var string формат данных (xml/json)
     */
    public $format;

    /**
     * Отправка запроса
     * @return mixed
     */
    abstract public function send() : array;

    /**
     * Установить адрес отправки запроса
     * @param $string
     * @return $this
     */
    public function setUrl($string) {
        $this->url = $string;
        return $this;
    }
    /**
     *
     * @param $data array Массив данных для отправки
     * @param string|null $format Формат отправляемых данных
     * @return ServiceRequest
     * @throws \Exception
     */
    public static function make($data, $format = null) : ServiceRequest {
        switch ($format) {
            case 'json':
                return new JsonServiceRequest($data);
                break;
            case 'xml':
                return new XmlServiceRequest($data);
                break;
            default:
                throw new \Exception("Отсутсвует класс для работы с форматом данных ({$format})");
        }
    }

    /**
     * @return ServiceResponse
     * @throws \Exception
     */
    public function getResponse() : ServiceResponse {
        // Имитировать отправку данных (CURL).
        $response = $this->send();

        if ($this->getFormat() == 'xml') {
            $response_code = strtolower($response['returnCodeDescription']);
        }

        if ($this->getFormat() == 'json') {
            $response_code = strtolower($response['SubmitDataResult']);
        }

        return ServiceResponse::make($response_code);
    }

    /**
     * Возвращает формат данных в запросе
     * @return mixed
     * @throws \Exception
     */
    private function getFormat() {
        if (empty($this->format)) {
            throw new \Exception("Формат данных запроса не определен");
        }

        return $this->format;
    }


}