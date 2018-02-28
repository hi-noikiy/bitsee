<?php namespace King\Market\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use King\Market\Models\Market;
use Flash;

class Coin extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('King.Market', 'Market', 'coin');
    }


}