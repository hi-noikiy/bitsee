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

    public function registerNavigation()
    {
    	return [
    			'market' => [
    					'label'       => '交易所管理',
    					'url'         => Backend::url('king/market/market'),
    					'icon'        => 'icon-pencil',
    					'iconSvg'     => 'plugins/king/market/assets/images/article.svg',
    					'permissions' => ['king.market.*'],
    					'order'       => 50
    			]
    	];
    }
}
