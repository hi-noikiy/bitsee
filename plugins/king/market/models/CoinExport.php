<?php namespace King\Market\Models;

use Backend\Models\ExportModel;
use ApplicationException;

/**
 * Post Export Model
 */
class CoinExport extends ExportModel
{
    public $table = 'coins';

    public function exportData($columns, $sessionKey = null)
    {
        $result = self::make()
            ->get()
            ->toArray()
        ;

        return $result;
    }
}
