<?php namespace King\Market\Models;

use Str;
use Model;
use URL;
use King\Market\Models\Market;
use October\Rain\Router\Helper as RouterHelper;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;

use DB;

class SymbolApp extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'symbols';
    protected $connection = 'mysqlapp';
    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /*
     * Validation
     */
    public $rules = [
        'symbol' => 'required',
    ];

    /**
     * @var array Attributes that support translation, if available.
     */
    public $translatable = [
        'name',
    ];

    protected $guarded = [];

    public function beforeValidate()
    {
        // Generate a URL slug for this model
        if (!$this->exists && !$this->slug) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function addAll(Array $data)
    {
        $rs = DB::connection('mysqlapp')->table($this->getTable())->insert($data);
        return $rs;
    }

    public function afterDelete()
    {
        $this->markets()->detach();
    }

    public function getMarketCountAttribute()
    {
        return $this->markets()->count();
    }

    /**
     * Sets the "url" attribute with a URL to this object
     * @param string $pageName
     * @param Cms\Classes\Controller $controller
     */
    public function setUrl($pageName, $controller)
    {
        $params = [
            'id'   => $this->id,
            'slug' => $this->slug,
        ];

        return $this->url = $controller->pageUrl($pageName, $params);
    }

    public function users()
    {
       return $this->belongsToMany('King\Market\Models\User', 'symbols_users' ,'symbol_id','user_id');
    }
}
