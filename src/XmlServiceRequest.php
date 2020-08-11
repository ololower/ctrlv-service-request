<?php

namespace ctrlv\ServiceRequest;


class XmlServiceRequest extends ServiceRequest {

    public function __construct(array $data) {
        $this->format = 'xml';
        $this->params = $this->prepareValues($data);
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
            curl_setopt($ch, CURLOPT_POSTFIELDS, "request=" . $this->params);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Content-Type:application/xml']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            $data = curl_exec($ch);
            curl_close($ch);
        } else {
            // Рандомный вариант ответа для фейковых запросов
            $data = $this->getRandomResponse();
        }

        // Конвертировать ответ в массив
        $array_data = json_decode(json_encode(simplexml_load_string($data)), true);

        return $array_data;
    }


    /**
     * Приводит данные к формату, который нужен для отправки
     * @param array $data
     * @return string
     */
    private function prepareValues(array $data) : string {

        $prepared_data = [];

        foreach ($data as $key => $value) {
            if ($key == 'creditScore') {
                $map_values = [
                    'good' => 700,
                    'bad' => 300
                ];

                $prepared_data[$key] = $map_values[$value];
                continue;
            }
            if ($key == 'Salary') {
                $prepared_data['salary'] = $value;
                continue;
            }
            if ($key == 'dateOfBirth') {
                $start = new \DateTime($value);
                $end = new \DateTime();
                $interval = date_diff($start, $end);

                $prepared_data['age'] = $interval->y;
                continue;
            }
            $prepared_data[$key] = $value;
        }

        var_dump($prepared_data);
        return $this->convertArrayToXmlString($prepared_data);

    }

    /**
     * Конвертирует массив в xml строку для отправки запроса
     * @param $array
     * @return string
     */
    private function convertArrayToXmlString($array) : string {
        $xml = new \SimpleXMLElement('<userInfo/>');
        array_walk_recursive($array, [$xml, 'addChild']);
        return $xml->asXML();
    }


    /**
     * Рандомно возвращает один из возможных вариантов ответа
     * @return string
     */
    private function getRandomResponse() : string {
        $responses = [
            '<?xml version="1.0" encoding="UTF-8"?><userInfo version="1.6"><returnCode>1</returnCode><returnCodeDescription>SUCCESS</returnCodeDescription><transactionId>AC158457A86E711D0000016AB036886A03E7</transactionId></userInfo>',
            '<?xml version="1.0" encoding="UTF-8"?><userInfo version="1.6"><returnCode>0</returnCode><returnCodeDescription>REJECT</returnCodeDescription></userInfo>',
            '<?xml version="1.0" encoding="UTF-8"?><userInfo version="1.6"><returnCode>0</returnCode><returnCodeDescription>ERROR</returnCodeDescription><returnError>Lead not Found</returnError></userInfo>'
        ];
        return $responses[array_rand($responses)];
    }

}

