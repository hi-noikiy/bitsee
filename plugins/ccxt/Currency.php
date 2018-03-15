<?php namespace Ccxt;

include '/var/www/html/vendor/ccxt/' . '/ccxt.php';

class Currency 
{
    static public function huobipro($backend)
    {
        date_default_timezone_set ('UTC');
        $backend = "\\ccxt\\".$backend;
        $exchange = new $backend();
        $currencys = $exchange->publicGetCommonCurrencys()['data'];
        $result = [];
        foreach ($currency as $currencys) { 
            $result[] = $currency;
        }

        return $result;
    }
    static public function okex($backend)
    {
        date_default_timezone_set ('UTC');
        $backend = "\\ccxt\\".$backend;
        $exchange = new $backend();
        $currencys = $exchange->webGetMarketsCurrencies()['data'];
        $result = [];
        foreach ($currency as $currencys) { 
            $result[] = $currency['symbol'];
        }

        return $result;

    }
    
}