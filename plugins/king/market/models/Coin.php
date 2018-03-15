<?php namespace King\Market\Models;

use Str;
use Model;
use URL;
use October\Rain\Router\Helper as RouterHelper;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;
use Markdown;

use King\Market\Classes\TagProcessor;

use DB;

class Coin extends Model
{

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

    protected $fillable = [
        'base'
    ];

    public $belongsToMany = [
        'markets' => [
            'King\Market\Models\Market',
            'table' => 'coin_market'
        ]
    ];

    public $attachOne = [
        'icon' => \System\Models\File::class
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

    public static function formatHtml($input, $preview = false)
	{
		$result = Markdown::parse(trim($input));
	
		if ($preview) {
			$result = str_replace('<pre>', '<pre class="prettyprint">', $result);
		}
	
		$result = TagProcessor::instance()->processTags($result, $preview);
	
		return $result;
    }
    
    public static function formatRaw($input, $preview = false)
    {

        $result = strip_tags(trim($input));

        return $result;
    }

	
	public function beforeSave()
	{
        $this->content_html = self::formatHtml($this->content);
        $this->content_raw  = self::formatRaw($this->content);
    }
    
}
