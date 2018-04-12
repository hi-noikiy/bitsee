<?php namespace King\Market\Models;

use Backend\Models\ImportModel;
use Backend\Models\User as AuthorModel;
use ApplicationException;
use Exception;

/**
 * Post Import Model
 */
class CoinImport extends ImportModel
{
    public $table = 'coins';

    /**
     * Validation rules
     */
    public $rules = [
        'base'   => 'required'
    ];

    protected $authorEmailCache = [];

    protected $categoryNameCache = [];


    public function importData($results, $sessionKey = null)
    {
        $firstRow = reset($results);

        /*
         * Import
         */
        foreach ($results as $row => $data) {
            try {

                if (!$title = array_get($data, 'base')) {
                    $this->logSkipped($row, 'Missing base');
                    continue;
                }

                /*
                 * Find or create
                 */
                $coin = Coin::make();

                if ($this->update_existing) {
                    $coin = $this->findDuplicateCoin($data) ?: $coin;
                }

                $coinExists = $coin->exists;

                /*
                 * Set attributes
                 */
                $except = ['id'];

                foreach (array_except($data, $except) as $attribute => $value) {
                    $coin->{$attribute} = $value ?: null;
                }

                $coin->save();//forceSave();

                /*
                 * Log results
                 */
                if ($coinExists) {
                    $this->logUpdated();
                }
                else {
                    $this->logCreated();
                }
            }
            catch (Exception $ex) {
                $this->logError($row, $ex->getMessage());
            }
        }
    }

    protected function findDuplicateCoin($data)
    {
        if ($id = array_get($data, 'id')) {
            return Coin::find($id);
        }

        $base = array_get($data, 'base');
        $coin = Coin::where('base', $base);

        return $coin->first();
    }
}
