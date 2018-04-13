<?php namespace King\Market;

use System\Classes\PluginBase;
use Backend;

use RainLab\Blog\Controllers\Posts as PostsController;

use RainLab\Blog\Models\Post as PostModel;

use King\Market\Models\SymbolApp;

use Event;

use Log;

use Flash;

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
    					'order'       => 50,
                        'sideMenu' => [
                                        'new_market' => [
                                            'label'       => '新建交易所',
                                            'icon'        => 'icon-plus',
                                            'url'         => Backend::url('king/market/market/create'),
                                            'permissions' => ['*']
                                        ],
                                        'markets' => [
                                            'label'       => '交易所',
                                            'icon'        => 'icon-copy',
                                            'url'         => Backend::url('king/market/market'),
                                            'permissions' => ['*']
                                        ],
                                        'symbols' => [
                                            'label'       => '经营币对',
                                            'icon'        => 'icon-list-ul',
                                            'url'         => Backend::url('king/market/symbol'),
                                            'permissions' => ['*']
                                        ],
                                        'coins' => [
                                            'label'       => '币种',
                                            'icon'        => 'icon-list-ul',
                                            'url'         => Backend::url('king/market/coin'),
                                            'permissions' => ['*']
                                        ],
                                        'users' => [
                                            'label'       => '用户',
                                            'icon'        => 'icon-list-ul',
                                            'url'         => Backend::url('king/market/user'),
                                            'permissions' => ['*']
                                        ]
                        ]
    			],

    	];
    }

        /**
     * Inject into Blog Posts
     */
    public function boot()
    {
        // Extend the controller
        PostsController::extendFormFields(function ($form, $model) {
            if (!$model instanceof PostModel) {
                return;
            }
            $form->addSecondaryTabFields([
                'author' => [
                    'tab'    => 'author',
                    'label' => '文章作者',
                    'placeholder' => '输入来源',
                    'span'   => 'left'
                ]
            ]);
        });
        Event::listen('list.Switch', function (&$modelClass, &$symbol, &$fieldvalue) {
            Log::info('HHHHHHH----------');
            Log::info($modelClass);
            if ($modelClass == 'King\Market\Models\Symbol') {
                $symbolapp = SymbolApp::where('symbol',$symbol)->first();

                if($symbolapp){
                    $symbolapp->published = $fieldvalue;
                    $symbolapp->save();
                    Flash::success('app update success');
                }

            }

        });
    }
}
