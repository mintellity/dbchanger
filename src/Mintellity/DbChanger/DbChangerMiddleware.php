<?php

namespace Mintellity\DbChanger;

use App\Entities\Customer;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DbChangerMiddleware
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $dbchangerIdentifier = $request->header(config('dbchanger.headerParameter'));

        if (!is_null($dbchangerIdentifier))
        {
            $databaseName = config('dbchanger.connection.prefix') . $dbchangerIdentifier;
            if(in_array(env('APP_ENV', 'local'), config('dbchanger.envs'))) {
                $this->setupNewDatabase($databaseName);
                $databaseExists = Schema::hasTable('migrations') ? true : false;
                if(!$databaseExists) {
                    return response()->json([
                        'error' => 1,
                        'message' => 'Your desired database doesn\'t exist. Please run the create database request before or remove the '. config('dbchanger.headerParameter') .' header.',
                        'result' => [],
                        'status' => '512'], 512);
                }

            }
            else {
                return response()->json([
                    'error' => 1,
                    'message' => 'API testing is not allowed within this application environment (' . env('APP_ENV') .'). Please add the current environment to the accepted list or remove the header parameter on your request.',
                    'result' => [],
                    'status' => '512'], 512);
            }
        }
        
        return $next($request);
    }

    public function setupNewDatabase($databaseName)
    {
        $newConnection = 'database.connections.'.env('DB_CONNECTION', 'mysql');
        \Config::set($newConnection . ".host", config('dbchanger.connection.host'));
        \Config::set($newConnection . ".user", config('dbchanger.connection.user'));
        \Config::set($newConnection . ".password", config('dbchanger.connection.password'));
        \Config::set($newConnection . ".database", $databaseName);
        DB::setDatabaseName($databaseName);
    }
}
