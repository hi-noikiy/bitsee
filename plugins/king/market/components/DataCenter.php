<?php namespace King\Market\Components;

use Lang;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\ComponentBase;

/**
 * Password reset workflow
 *
 * When a user has forgotten their password, they are able to reset it using
 * a unique token that, sent to their email address upon request.
 */
class DataCenter extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'market data',
            'description' => 'get data from market'
        ];
    }

    public function defineProperties()
    {
        return [
        ];
    }

    //
    // AJAX
    //

    /**
     * Trigger the password reset email
     */
    public function onMarketData($symbol)
    {
        include '/var/www/html/vendor/ccxt/' . '/ccxt.php';

        date_default_timezone_set ('UTC');

        $exchange = new \ccxt\okcoinusd (array (
            'apiKey' => 'cee877fc-94ea-4c9e-bd84-7fdff9d1a770',
            'secret' => 'D43A8C426064C18B75923E8123C73560',
            'verbose' => true,
        ));

        try {

            return $exchange->fetch_ticker ($symbol);

        } catch (\ccxt\NetworkError $e) {
            echo '[Network Error] ' . $e->getMessage () . "\n";
        } catch (\ccxt\ExchangeError $e) {
            echo '[Exchange Error] ' . $e->getMessage () . "\n";
        } catch (Exception $e) {
            echo '[Error] ' . $e->getMessage () . "\n";
        }
    }

    public function onRun() 
    {
        $this->data = $this->onMarketData('BTC/USD');
    }
}
