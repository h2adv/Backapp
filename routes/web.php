<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Input;

Route::get('/', function () {
    return view('home');
});

Route::get('settings/get', 'SettingsController@getSettings');
Route::get('backups/get', 'BackupsController@getBackups');
Route::get('backups/history', 'BackupsController@getBackupsHistory');
Route::get('hosts/get', 'HostsController@getHosts');
Route::get('host/edit/{id}/{saved?}', 'HostsController@editHost');

Route::post('hosts/create', 'HostsController@createHosts');
Route::post('hosts/delete', 'HostsController@deleteHosts');
Route::post('hosts/toggle', 'HostsController@toggle') ;
Route::post('hosts/edit', 'HostsController@editDoHost') ;
Route::post('backups/ftp-do', 'BackupsController@ftpDoBackup');
Route::post('backups/sql-do', 'BackupsController@sqlDoBackup');
