<?php namespace King\Market\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use King\Market\Models\Market;
use Flash;
use Log;
use System\Models\File;
use DB;

use King\Market\Models\Coin as CoinModel;

use King\Market\Models\Member;

use King\Market\Models\Symbol;

use Vdomah\Excel\Classes\Excel;

class Coin extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ImportExportController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $importExportConfig = 'config_import_export.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('King.Market', 'Market', 'coin');
    }

    public function onUpdateIcons()
    {
        $coins = CoinModel::with('icon')->get();
        $numadd = 0;
        $numupdate = 0;
        $numfilenotexist = 0;
        $numerror = 0;
        foreach($coins as $coin) {

            $file = new File();

            try{
                $file = $file->fromFile('./storage/app/media/coins/'.$coin->icon_url);
            }catch(\Exception $ex){
                $numfilenotexist++;
                continue;
            }
            if($file) {

                $flag = 0;
                DB::beginTransaction();
                try{
                    if($coin->icon) {

                        $coin->icon->delete();
                        $flag = 1;

                    }

                    $file->attachment_type = 'King\Market\Models\Coin';
                    $file->attachment_id = $coin->id;
                    $file->field = 'icon';
                    $file->is_public = 1;
                    $file->save();
                    DB::commit();
                    if($flag) {
                        $numupdate++;
                    }else{
                        $numadd++;
                    }
                }catch(\Exception $ex){
                    $numerror++;
                    DB::rollback();
                }

            
            }

            
        }
        if($numerror){
            Flash::error('insert:  '.$numadd.'  update: '.$numupdate.'  error:  '.$numerror.'  notexist '.$numfilenotexist);
        }else{
            Flash::success('insert:  '.$numadd.'  update: '.$numupdate.'  error:  '.$numerror.'  notexist '.$numfilenotexist);
        }
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
                        if ($attribute == 'begin' || $attribute == 'end') {
                            $value = date('Y-m-d H:i:s',($value == '未知')?strtotime('1980-01-01') : strtotime($value));
                        }
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

    public function onUpdateMemberIcon()
    {
        $members = Member::with('icon')->get();
        $numadd = 0;
        $numupdate = 0;
        $numfilenotexist = 0;
        $numerror = 0;
        foreach($members as $member) {

            $file = new File();

            try{
                $file = $file->fromFile('./storage/app/media/members/'.$member->avatar);
            }catch(\Exception $ex){
                $numfilenotexist++;
                continue;
            }
            if($file) {

                $flag = 0;
                DB::beginTransaction();
                try{
                    if($member->icon) {

                        $member->icon->delete();
                        $flag = 1;

                    }

                    $file->attachment_type = 'King\Market\Models\Member';
                    $file->attachment_id = $member->id;
                    $file->field = 'icon';
                    $file->is_public = 1;
                    $file->save();
                    DB::commit();
                    if($flag) {
                        $numupdate++;
                    }else{
                        $numadd++;
                    }
                }catch(\Exception $ex){
                    $numerror++;
                    DB::rollback();
                }

            
            }

            
        }
        if($numerror){
            Flash::error('insert:  '.$numadd.'  update: '.$numupdate.'  error:  '.$numerror.'  notexist '.$numfilenotexist);
        }else{
            Flash::success('insert:  '.$numadd.'  update: '.$numupdate.'  error:  '.$numerror.'  notexist '.$numfilenotexist);
        }
    }

    public function onUpdateMembers()
    {
        Excel::excel()->load(base_path() . '/storage/app/media/members.xlsx', function($reader) {

            $results = $reader->all()->toArray();
            $updatenum = 0;
            $insertnum = 0;
            $errornum = 0;
            foreach ($results as $row => $data) {
                try {
    
                    if (!$title = array_get($data, 'name')) {
                        Flash::error($row.'  Missing name');
                        continue;
                    }
    
                    /*
                     * Find or create
                     */
                    $member = Member::make();
    
                    //if ($this->update_existing) {
                    $member = $this->findDuplicateMember($data) ?: $member;
                    //}
    
                    $memberExists = $member->exists;
    
                    /*
                     * Set attributes
                     */
                    $except = ['id','ID'];
    
                    foreach (array_except($data, $except) as $attribute => $value) {
                        $member->{$attribute} = $value ?: null;
                    }
    
                    $member->save();//forceSave();
    
                    /*
                     * Log results
                     */
                    if ($memberExists) {
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

    public function onUpdateCoinMarket()
    {
        $coins = CoinModel::all();
        $markets = Market::all(); 
        foreach ($coins as $coin) {
            # code...
            foreach ($markets as $market) {
                # code...
                $symbol = Symbol::where('base',$coin->base)->where('market_id',$market->id)->first();
                if ($symbol->exists) {

                    $market->coins()->detach($coin->id);
                    $market->coins()->attach($coin->id);

                }
            }
        

        }
    }

    protected function findDuplicateCoin($data)
    {
        // if ($id = array_get($data, 'id')) {
        //     return CoinModel::find($id);
        // }
        // 忽略 id 以 base 为准
        $base = array_get($data, 'base');
        $coin = CoinModel::where('base', $base);

        return $coin->first();
    }

    protected function findDuplicateMember($data)
    {
        // if ($id = array_get($data, 'id')) {
        //     return CoinModel::find($id);
        // }
        // 忽略 id 以 base 为准
        $name = array_get($data, 'name');
        $base = array_get($data, 'base');
        $member = Member::where('base', $base)->where('name',$name);

        return $member->first();
    }

}