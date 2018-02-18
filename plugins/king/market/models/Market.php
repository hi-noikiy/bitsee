<?php namespace King\Market\Models;

use Model;
use Html;
use Markdown;
use Db;
use King\Market\Classes\TagProcessor;


/**
 * Model
 */
class Market extends Model
{
	use \October\Rain\Database\Traits\Validation;

	/*
	 * Validation
	 */
	public $rules = [
	];

	public $attachMany = [
			'content_images' => ['System\Models\File'],
			'featured_images' => ['System\Models\File']
	];
	public static function formatHtml($input, $preview = false)
	{
		$result = Markdown::parse(trim($input));
	
		if ($preview) {
			$result = str_replace('<pre>', '<pre class="prettyprint">', $result);
		}
	
		$result = TagProcessor::instance()->processTags($result, $preview);
	
		return $result;
	}
	
	public function beforeSave()
	{
		$this->content_html = self::formatHtml($this->content);
	}
	
	/*
	 * Disable timestamps by default.
	 * Remove this line if timestamps are defined in the database table.
	 */
	//public $timestamps = false;

	/**
	 * @var string The database table used by the model.
	 */
	public $table = 'markets';
}