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
        foreach ($currencys as $currency) { 
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
        foreach ($currencys as $currency) { 
            $result[] = $currency['symbol'];
        }

        return $result;

    }
    static public function bitfinex2($backend)
    {
        date_default_timezone_set ('UTC');
        $backend = "\\ccxt\\".$backend;
        $exchange = new $backend();
        $markets = $exchange->loadMarkets ();
        $result = [];
        foreach ($markets as $market) { 
            $flag =1;
            foreach ($result as $item){
                if ($item == $market['base']){
                    $flag = 0;
                }
            }
            if($flag){
                $result[] = $market['base'];
            }
        }

        return $result;

    }

    static public function binance($backend)
    {
        date_default_timezone_set ('UTC');
        $backend = "\\ccxt\\".$backend;
        $exchange = new $backend();
        $markets = $exchange->loadMarkets ();
        $result = [];
        foreach ($markets as $market) { 
            $flag =1;
            foreach ($result as $item){
                if ($item == $market['base']){
                    $flag = 0;
                }
            }
            if($flag){
                $result[] = $market['base'];
            }
        }

        return $result;

    }
    
}