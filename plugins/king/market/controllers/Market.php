<?php namespace King\Market\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Ccxt\Currency;

use King\Market\Models\Coin;

use King\Market\Models\Symbol;

use King\Market\Models\SymbolApp;

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

    public function onGetCoins($recordId)
    {
        $model = $this->formFindModelObject($recordId);

        $backend = $model->backend;

        $symbols = Currency::$backend($backend);

        foreach($symbols as $symbol){

            $coin = Coin::where('base',$symbol)->first();
            if ($coin) {
                $model->coins()->detach($coin->id);
            } else {
                $coin = Coin::create([
                    'base' => $symbol
                ]);
            }

            $model->coins()->attach($coin->id);
        }

        return $symbols;


    }

    public function onGetSymbols($recordId)
    {
        $model = $this->formFindModelObject($recordId);

        include '/var/www/html/vendor/ccxt/' . '/ccxt.php';

        date_default_timezone_set ('UTC');

        $backend = "\\ccxt\\".$model->backend;

        $exchange = new $backend();

        if (!method_exists($exchange,'loadMarkets')) {
                return;
        }

        try {

            $markets = $exchange->loadMarkets ();

            Symbol::where('market_id', $model->id)->delete();
            SymbolApp::where('market', $model->backend)->delete();

            $symbols = [];
            $symbolsapp = [];

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

                $symbolapp['symbol'] = $market['symbol'];
                $symbolapp['market'] = $market['backend'];

                $symbols[] =  $symbol;
                $symbolsapp[] = $symbolapp;

                //$model->symbols()->create($symbol);
            }

            $tmp = new Symbol();
            $tmpapp = new SymbolApp();

            $tmp->addAll($symbols);
            $tmpapp->addAll($symbolsapp);

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