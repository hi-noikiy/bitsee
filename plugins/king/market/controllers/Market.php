<?php namespace King\Market\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

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

            return $exchange->fetch_markets ();

        } catch (\ccxt\NetworkError $e) {
            echo '[Network Error] ' . $e->getMessage () . "\n";
        } catch (\ccxt\ExchangeError $e) {
            echo '[Exchange Error] ' . $e->getMessage () . "\n";
        } catch (Exception $e) {
            echo '[Error] ' . $e->getMessage () . "\n";
        }


    }
}