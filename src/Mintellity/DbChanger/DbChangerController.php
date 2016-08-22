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
        // validate database name
        $validator = Validator::make(["databaseName" => $databaseName], [
            'databaseName' => 'required|alpha_num|max:30|min:2'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'message' => 'DB name is required and must be alphanumeric, min:2 and max:30',
                'result' => [],
                'status' => '512'], 512);
        }

        // check if used environment is allowed to create databases
        if(in_array(env('APP_ENV', 'local'), config('apitest.envs'))) {
            $databaseName = config('apitest.connection.prefix') . $databaseName;
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

        \Config::set($newConnection . ".host", config('apitest.connection.host'));
        \Config::set($newConnection . ".user", config('apitest.connection.user'));
        \Config::set($newConnection . ".password", config('apitest.connection.password'));
        \Config::set($newConnection . ".database", $databaseName);

        if($force) {
            DB::statement("DROP DATABASE IF EXISTS $databaseName");
        }

        DB::statement("CREATE DATABASE IF NOT EXISTS $databaseName");
        DB::setDatabaseName($databaseName);
    }
}