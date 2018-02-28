<?php namespace King\Market\Models;

use Str;
use Model;
use URL;
use October\Rain\Router\Helper as RouterHelper;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;

use DB;

class Coin extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\NestedTree;

    public $table = 'coins';
    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|between:3,64|unique:symbols',
        'code' => 'nullable|unique:symbols',
    ];

    public $belongsToMany = [
        'markets' => [
            'King\Market\Models\Market',
            'table' => 'coin_market'
        ]
    ];

    public function beforeValidate()
    {
        // Generate a URL slug for this model
        if (!$this->exists && !$this->slug) {
            $this->slug = Str::slug($this->name);
        }
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
}
