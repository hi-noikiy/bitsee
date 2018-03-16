<?php namespace King\Market\Models;

use Str;
use Model;
use URL;
use King\Market\Models\Market;
use October\Rain\Router\Helper as RouterHelper;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;

use DB;

class SymbolUser extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'symbols_users';
    protected $connection = 'mysqlapp';
    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /*
     * Validation
     */
    public $rules = [
        'market' => 'required',
    ];

    // public $belongsTo = [
    //     'market' => ['King\Market\Models\Market', 'key' => 'market_id', 'otherKey' => 'id']
    // ];

    protected $guarded = [];
}
