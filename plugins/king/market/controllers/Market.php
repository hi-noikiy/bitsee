<?php namespace King\Market\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Ccxt\Currency;

use King\Market\Models\Coin;

use King\Market\Models\Symbol;

use King\Market\Models\SymbolApp;

use King\Market\Models\User;

use Flash;

use DB;

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

        Flash::success('交易所支持币种 获取成功');

        return;


    }

    public function onGetSymbols($recordId)
    {
        $model = $this->formFindModelObject($recordId);

        include '/var/www/html/vendor/ccxt/' . '/ccxt.php';

        date_default_timezone_set ('UTC');

        $backend = "\\ccxt\\".$model->backend;

        $exchange = new $backend(array (
            'enableRateLimit' => true,
        ));

        if (!method_exists($exchange,'loadMarkets')) {
                Flash::error('不存在 loadMarkets 函数');
                return 111;
        }
        try {

            $markets = $exchange->loadMarkets ();

            Symbol::where('market_id', $model->id)->delete();
            $old_symbols  = SymbolApp::where('market', $model->backend)->get();
            $old_symbols_transform = [];
            $market_transform = [];
            foreach($old_symbols as $old_symbol){
                $old_symbols_transform[$old_symbol->symbol] = $old_symbol->id;
            }

            $symbols = [];
            $symbolsApp = [];

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

                $market_transform[$market['symbol']] =  $market;


                if (array_key_exists($market['symbol'],$old_symbols_transform) ){

                }else{

                    $symbolsApp[] = ['symbol' => $market['symbol'] , 'market' => $model->backend];

                }

                $symbols[] =  $symbol;
                //$model->symbols()->create($symbol);
            }
            DB::beginTransaction();
            try{
                $tmp = new Symbol();

                $tmp->addAll($symbols);
                DB::commit();
                //Flash::success('网站交易所币对交易心思更新成功');
            }catch(\Exception $e){
                DB::rollback();
                Flash::error('网站交易所币对交易心思更新失败，没有更新app交易所经营币对');
                return;
            }
            DB::connection('mysqlapp')->beginTransaction();
            try{
                $tmpapp = new SymbolApp();

                $tmpapp->addAll($symbolsApp);
                foreach($old_symbols as $old_symbol) {
                    if (array_key_exists($old_symbol->symbol,$market_transform)) {

                    }else{
                        $old_symbol->published = 0;
                        $old_symbol->save();
                    }
                }
                Flash::success('网站交易所经营币对信息更新成功 app交易所币对交易心思更新成功');
                DB::connection('mysqlapp')->commit();
                return;
            }catch(\Exception $e){
                DB::connection('mysqlapp')->rollback();
                Flash::error('app交易所币对交易心思更新失败，网站交易所经营币对更新成功');
                return;
            }
            return;
        } catch (\ccxt\NetworkError $e) {

            Flash::error('服务器异常');

            echo '[Network Error] ' . $e->getMessage () . "\n";
        } catch (\ccxt\ExchangeError $e) {

            Flash::error('服务器异常');

            echo '[Exchange Error] ' . $e->getMessage () . "\n";
        } catch (Exception $e) {

            Flash::error('服务器异常');

            echo '[Error] ' . $e->getMessage () . "\n";
        }


    }
}