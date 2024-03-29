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

use Illuminate\Support\Facades\Route;

Route::view('/', 'home');

Route::get('settings/get','SettingsController@getSettings');
Route::get('backups/get','BackupsController@getBackups')->name('backup-saved');
Route::get('backups/log','BackupsController@getBackupLog');
Route::get('backups/ftp-do/{id}','BackupsController@ftpDoBackup');
Route::get('hosts/get','HostsController@getHosts');
Route::get('host/edit/{id}/{saved?}','HostsController@editHost')->name('host-saved');

Route::post('hosts/create','HostsController@createHosts');
Route::post('hosts/delete','HostsController@deleteHosts');
Route::post('hosts/toggle','HostsController@toggleHost') ;
Route::post('hosts/edit', 'HostsController@editDoHost') ;
Route::post('backups/sql-do','BackupsController@sqlDoBackup');
Route::post('backups/get','BackupsController@getBackups');
Route::post('backups/ftp-do/{id}','BackupsController@ftpDoBackup');

