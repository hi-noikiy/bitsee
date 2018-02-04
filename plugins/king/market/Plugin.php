<?php namespace King\Market;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    	return [
            \King\Market\Components\DataCenter::class  => 'marketdata',
        ];
    }

    public function registerSettings()
    {
    }
}
