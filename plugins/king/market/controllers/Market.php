<?php namespace King\Market\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

use King\Market\Models\Symbol;

class Market extends Controller
{
    public $implement = [
       'Backend\Behaviors\ListController',
       'Backend\Behaviors\FormController',
       'Backend.Behaviors.RelationController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('King.Market', 'market', 'market');
    }

    public function onGetSymbols($recordId)
    {
        $model = $this->formFindModelObject($recordId);

        include '/var/www/html/vendor/ccxt/' . '/ccxt.php';

        date_default_timezone_set ('UTC');

        $backend = "\\ccxt\\".$model->backend;

        $exchange = new $backend();

        if (!method_exists($exchange,'fetch_markets')) {
                return;
        }

        try {

            $markets = $exchange->fetch_markets ();

            Symbol::where('market_id', $model->id)->delete();

            $symbols = [];

            foreach ($markets as $market) {
                # code...
                $symbol['symbol'] = $market['symbol'];
                $symbol['slug']  = $market['id'];
                $symbol['base']   = $market['base'];
                $symbol['maker']  =  $market['maker'];
                $symbol['taker'] =   $market['taker'];
                $symbol['lot']  =   $market['lot'];
                $symbol['quote'] =   $market['quote'];
                $symbol['limits_amount_max'] = $market['limits']['amount']['max'];
  
                $symbol['limits_amount_min'] = $market['limits']['amount']['min'];
                $symbol['limits_cost_max']  = $market['limits']['cost']['max'];
                $symbol['limits_cost_min'] = $market['limits']['cost']['min'];

                $symbol['limits_price_max'] = $market['limits']['price']['max'];
                $symbol['limits_price_min'] = $market['limits']['price']['min'];
                $symbol['precision_amount'] = $market['precision']['amount'];
                $symbol['precision_price'] = $market['precision']['price'];
                $symbol['market_id'] = $model->id;

                $symbols[] =  $symbol;

                //$model->symbols()->create($symbol);
            }

            $tmp = new Symbol();

            $tmp->addAll($symbols);

            return $symbols;

            

        } catch (\ccxt\NetworkError $e) {
            echo '[Network Error] ' . $e->getMessage () . "\n";
        } catch (\ccxt\ExchangeError $e) {
            echo '[Exchange Error] ' . $e->getMessage () . "\n";
        } catch (Exception $e) {
            echo '[Error] ' . $e->getMessage () . "\n";
        }


    }
}