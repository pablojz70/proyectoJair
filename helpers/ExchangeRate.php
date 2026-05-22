<?php
class ExchangeRate
{
    private static function getDb()
    {
        return Database::getInstance();
    }

    public static function getRate()
    {
        $rate = self::getFromAPI();
        if ($rate !== null) {
            self::saveRate($rate, 'api');
            return $rate;
        }
        $last = self::getLastRate();
        return $last;
    }

    private static function getFromAPI()
    {
        $url = 'https://ve.dolarapi.com/v1/dolares/oficial';
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 4,
                'method' => 'GET',
                'header' => "Accept: application/json\r\nUser-Agent: Mozilla/5.0",
            ],
        ]);
        $response = @file_get_contents($url, false, $ctx);
        if ($response === false) {
            return null;
        }
        $data = json_decode($response, true);
        if (!$data) {
            return null;
        }
        $rate = $data['promedio'] ?? $data['avg'] ?? null;
        return $rate ? (float) $rate : null;
    }

    public static function getRateQuick()
    {
        $last = self::getLastRate();
        if ($last !== null) return $last;
        $rate = self::getFromAPI();
        if ($rate !== null) {
            self::saveRate($rate, 'api');
            return $rate;
        }
        return null;
    }

    public static function saveRate($rate, $source = 'manual')
    {
        $db = self::getDb();
        $stmt = $db->prepare("INSERT INTO exchange_rates (rate, source) VALUES (?, ?)");
        $stmt->execute([$rate, $source]);
    }

    public static function getLastRate()
    {
        $db = self::getDb();
        $stmt = $db->query("SELECT rate FROM exchange_rates ORDER BY id DESC LIMIT 1");
        $result = $stmt->fetch();
        return $result ? (float) $result['rate'] : null;
    }
}
