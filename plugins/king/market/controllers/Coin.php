<?php namespace King\Market\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use King\Market\Models\Market;
use Flash;
use Log;
use System\Models\File;
use DB;

use King\Market\Models\Coin as CoinModel;

use Vdomah\Excel\Classes\Excel;

class Coin extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ImportExportController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('King.Market', 'Market', 'coin');
    }

    public function onUpdateIcons()
    {
        $coins = CoinModel::with('icon')->get();
        DB::beginTransaction();
        try{
            foreach($coins as $coin) {

                $file = File::fromFile('/root/bitsee-docker/octobercms/storage/app/media/coins/'.$coin->icon_url);
                Log::info("GGGGGGGGEE");
                if($file) {
                    Log::info('HHHHHHHHH');
                    Log::info($coin->icon_url);

                    if($coin->icon) {

                        $coin->icon->delete();
    
                    }

                    $file->attachment_type = 'King\Market\Models\Coin';
                    $file->attachment_id = $coin->id;
                    $file->field = 'icon';
                    $file->is_public = 1;
                    $file->save();
                }

                
            }
        }catch(\Exception $e){
            DB::rollback();
        }
        DB::commit();

    }

    public function onUpdateCoins()
    {
        Excel::excel()->load(base_path() . '/storage/app/media/coins.xlsx', function($reader) {

            $results = $reader->all()->toArray();
            $updatenum = 0;
            $insertnum = 0;
            $errornum = 0;
            foreach ($results as $row => $data) {
                try {
    
                    if (!$title = array_get($data, 'base')) {
                        Flash::error($row.'  Missing base');
                        continue;
                    }
    
                    /*
                     * Find or create
                     */
                    $coin = CoinModel::make();
    
                    //if ($this->update_existing) {
                    $coin = $this->findDuplicateCoin($data) ?: $coin;
                    //}
    
                    $coinExists = $coin->exists;
    
                    /*
                     * Set attributes
                     */
                    $except = ['id','ID'];
    
                    foreach (array_except($data, $except) as $attribute => $value) {
                        $coin->{$attribute} = $value ?: null;
                    }
    
                    $coin->save();//forceSave();
    
                    /*
                     * Log results
                     */
                    if ($coinExists) {
                        $updatenum++;
                    }
                    else {
                        $insertnum++;
                    }
                }
                catch (Exception $ex) {
                    Flash::error($row.':   '.$ex->getMessage());
                    $errornum++;
                }
            }
            Flash::success('insert:  '.$insertnum.'update: '.$updatenum,'error:  '.$errornum);
        });


    }

    protected function findDuplicateCoin($data)
    {
        if ($id = array_get($data, 'id')) {
            return CoinModel::find($id);
        }

        $base = array_get($data, 'base');
        $coin = CoinModel::where('base', $base);

        return $coin->first();
    }

}