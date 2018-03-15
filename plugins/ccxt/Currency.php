<?php namespace Ccxt;

include '/var/www/html/vendor/ccxt/' . '/ccxt.php';

class Currency 
{
    static public function huobipro()
    {
        date_default_timezone_set ('UTC');
        $backend = "\\ccxt\\".$model->backend;
        $exchange = new $backend();
        $currencys = $exchange->publicGetCommonCurrencys()['data'];
        $result = [];
        foreach ($currency as $currencys) { 
            $result[] = $currency;
        }

        return $result;
    }
    static public function okex()
    {
        date_default_timezone_set ('UTC');
        $backend = "\\ccxt\\".$model->backend;
        $exchange = new $backend();
        $currencys = $exchange->webGetMarketsCurrencies()['data'];
        $result = [];
        foreach ($currency as $currencys) { 
            $result[] = $currency['symbol'];
        }

        return $result;

    }
    
}