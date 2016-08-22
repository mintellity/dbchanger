<?php
/**
 * Created by PhpStorm.
 * User: karim
 * Date: 8/22/16
 * Time: 11:51 AM
 */

namespace Mintellity\DbChanger;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DbChangerController extends BaseController
{
    public function buildDatabase(Request $request, $databaseName, $forceCreate = false)
    {
        // check if config was published
        if(empty(config('dbchanger'))) {
            return response()->json([
                'error' => 1,
                'message' => 'Please publish the DbChanger-config file. ( php artisan vendor:publish --provider="Mintellity\DbChanger\DbChangerServiceProvider" )',
                'result' => [],
                'status' => '512'], 512);
        }

        // validate database name
        $validator = Validator::make(["databaseName" => $databaseName], [
            'databaseName' => config('dbchanger.databaseNameValidation')
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'message' => 'DB name is required and must be alphanumeric, min:2 and max:30',
                'result' => [],
                'status' => '512'], 512);
        }

        // check if used environment is allowed to create databases
        if(count(config('dbchanger.envs')) == 0 || in_array(env('APP_ENV', 'local'), config('dbchanger.envs'))) {
            $databaseName = config('dbchanger.connection.prefix') . $databaseName;
            $this->setupNewDatabase($databaseName, $forceCreate);
            $migrationOptions = Schema::hasTable('migrations') ? ['--database' => $databaseName] : ['--database' => $databaseName, '--seed' => true ];
            Artisan::call('migrate', $migrationOptions);

            return response()->json([
                'error' => 1,
                'message' => 'Migration has been successfully created/reset',
                'result' => [],
                'status' => '200'], 200);
        }
        else {
            return response()->json([
                'error' => 1,
                'message' => 'API testing is not allowed within this application environment (' . env('APP_ENV') .').',
                'result' => [],
                'status' => '512'], 512);
        }
    }

    public function setupNewDatabase($databaseName, $force)
    {
        $newConnection = 'database.connections.'.$databaseName;
        \Config::set($newConnection , config('database.connections.' . env('DB_CONNECTION', 'mysql')));

        \Config::set($newConnection . ".host", config('dbchanger.connection.host'));
        \Config::set($newConnection . ".user", config('dbchanger.connection.user'));
        \Config::set($newConnection . ".password", config('dbchanger.connection.password'));
        \Config::set($newConnection . ".database", $databaseName);

        if($force) {
            DB::statement("DROP DATABASE IF EXISTS $databaseName");
        }

        DB::statement("CREATE DATABASE IF NOT EXISTS $databaseName");
        DB::setDatabaseName($databaseName);
    }
}