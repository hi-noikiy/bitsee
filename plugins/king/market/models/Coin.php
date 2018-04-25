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
        'base' => 'required',
        //'name' => 'required',
        //'slug' => 'required|between:3,64|unique:symbols',
        //'code' => 'nullable|unique:symbols',
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
        'icon' => \System\Models\File::class,
        'team_icon' => \System\Models\File::class,
    ];

    public $hasMany = [
        'members' => \King\Market\Models\Member::class
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

        return (mb_strlen($result,'utf-8') > 50) ? mb_substr($result,0,49,'utf-8') .'...' : $result;
    }

	
	public function beforeSave()
	{
        $this->content_html = self::formatHtml($this->content);
        $this->content_raw  = self::formatRaw($this->content);

        $this->team_html = self::formatHtml($this->team);
        $this->team_raw  = self::formatRaw($this->team);

    }
    
}
