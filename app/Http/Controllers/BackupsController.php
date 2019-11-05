<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use FilesystemIterator;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class BackupsController extends Controller
{


    private $name;
    private $host;
    private $user;
    private $pass;
    private $dirc;
    private $host_sql;
    private $name_sql;
    private $user_sql;
    private $pass_sql;
    private $target;
    private $conn;
    private $file_name;
    private $backups_directory;

    public function __construct()
    {
        $this->file_name = config('app.script_file');
        $this->backups_directory = config('app.backups_directory');
    }

    /**
     * Show all of the users for the application.
     *
     * @return Response
     */
    public function getBackupsHistory()
    {
        $backups = DB::table('backups')->get();
        return view('backups', ['backups' => $backups]);
    }

    /**
     * Show all of the users for the application.
     *
     * @return Response
     */
    public function getBackups()
    {
        $hosts = DB::table('hosts')->get();
        return view('backup', ['hosts' => $hosts]);
    }

    /**
     * Show all of the users for the application.
     *
     */
    public function ftpDoBackup(Request $request)
    {
        $id = $request->route('id');
        $host = DB::table('hosts')->find($id);
        if($this->isLocal( $host )){
            $this->localFtpBackup($host);
        }else{
            $this->remoteFtpBackup($host);
        }
    }

    /**
     * @param $host
     */
    private function localFtpBackup($host)
    {

//        $file = storage_path().'/app/public/backup_h2adv.php';
//        $url_partial =  $host->ftp_host;
//
//        $url = 'http://'.$host->domain.'/backup_h2adv.php?backup_ftp=true' ;
//        $ftp_conn = ftp_connect($url_partial);
//
//        if(!ftp_login($ftp_conn, $host->ftp_username, $host->ftp_password))
//        {
//            die('Ftp login error');
//        }
//
//        if(ftp_put($ftp_conn, $host->ftp_directory."/backup_h2adv.php", $file, FTP_ASCII))
//        {
//
//            ftp_close($ftp_conn);
//            $data = json_encode(array(
//                'directory'=>$host->ftp_directory
//            ));
//
//            try {
//
//                $ch = curl_init();
//
//                curl_setopt($ch, CURLOPT_URL, $url);
//                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//
//                curl_setopt($ch, CURLOPT_POST, true);
//                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//                curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
//                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//                $response  = curl_exec($ch);
//
//                $err = curl_error($ch);
////                curl_close($ch);
//                var_dump($response);
//                return;
//
//                if ($response === false) {
//                    echo json_encode(curl_error($ch), curl_errno($ch));
//                }
//
//                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//                $json_response = json_decode($response);
//
//                echo json_encode($this->downloadBackup($json_response,$host));
//                return;
//
//            }catch(Exception $e){
//                echo json_encode($e->getMessage());
//                return;
//            }
//
//        }
//        else
//        {
//            echo json_encode(array('result'=>"Error uploading $file."));
//            return;
//        }

    }

    /**
     * @param $host
     */
    public function remoteFtpBackup($host){

        $file = storage_path().'/app/public/'.$this->file_name;
        $url_partial =  $host->ftp_host;
        $url = $host->domain.'/'.$this->file_name.'?backup_ftp=true';
        $ftp_conn = ftp_connect($url_partial);

        if(!ftp_login($ftp_conn, $host->ftp_username, $host->ftp_password))
        {
            die('Ftp login error');
        }

        if ($file_put = ftp_put($ftp_conn, $host->ftp_directory."/".$this->file_name, $file, FTP_ASCII))
        {
            $data = json_encode(array(
                'directory'=>$host->ftp_directory
            ));

            try {
                $response = $this->curlConnect($data, $url);

                echo json_encode($this->downloadBackup($response, $host));
                return;

            }catch(Exception $e){
                echo json_encode($e->getMessage());
                return;
            }
        }
        else
        {
            echo json_encode(array('result'=>"Error uploading $file."));
            return;
        }
    }

    /**
     * @param $json_response
     * @param $host
     * @return bool|mixed
     */
    public function downloadBackup($json_response, $host)
    {
        if($json_response->type == 'ftp') {
            $path='ftp';
        } else {
            $path= 'sql';
        }

        $dir = $_SERVER['DOCUMENT_ROOT'].'/'.$this->backups_directory.'/'.$host->host_slug.'/'.$path;
        $file = $dir.'/'.$json_response->filename;

        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }

        file_put_contents($file, fopen($json_response->filename_url, 'r'));

        if(file_exists($file)){
            return $this->cleanBackup($json_response->filename, $host);
        }
        else {
            return false;
        }
    }

    /**
     * @param $file
     * @param $host
     * @return mixed
     */
    public function cleanBackup($file, $host)
    {
        $url = $host->domain.'/'.$this->file_name.'?clean=true';

        $data = json_encode(array(
            'file'=>'./'.$file,
            'adsasd'=>'a sda sd'
        ));

        try {
            $response = $this->curlConnect($data, $url, true);
            echo json_encode($response);
            return true;
        } catch(Exception $e) {
            echo json_encode($e->getMessage());
            return false;
        }
    }

    public function curlConnect($data, $url, $opt = null)
    {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FAILONERROR, true );
        curl_setopt($ch, CURLOPT_HEADER, false );
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if($opt == null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
            ));
        }else{
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json'
            ));
        }
        $response = curl_exec($ch);

        if ($response === false) {
            return json_encode(curl_error($ch), curl_errno($ch));
        }

        return json_decode($response);
    }

    /**
     * @return bool
     */
    public function sqlDoBackup(Request $request)
    {

        $id = $request->input('id');
        $file = storage_path().'/app/public/backup_h2adv.php';

        $host = DB::table('hosts')->find($id);
//        $this->startBackup($id);
//        $file = 'remote/backup_h2adv.php';
        // upload file
        $ftp_conn = ftp_connect($host->ftp_host) or die("Could not connect to $host");
        if(!ftp_login($ftp_conn, $host->ftp_username, $host->ftp_password))
        {
            die('Ftp login error');
        }

        if (ftp_put($ftp_conn, $host->ftp_directory."/backup_h2adv.php", $file, FTP_ASCII))
        {
            $data = json_encode($host);

            // set URL and other appropriate options
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $host->ftp_host."/backup_h2adv.php?backup_sql=true");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($data)
                )
            );

            $response = curl_exec($ch);
            $abc = json_encode($response);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $json_response = json_decode($response);
            echo json_encode($this->downloadBackup($json_response,$host));
        }
        else
        {
            echo json_encode(array('result'=>"Error uploading $file."));
            return;
        }
    }

    /**
     * @param $host
     * @return bool
     */
    public function isLocal($host)
    {
        if($host->is_local == 1){
            return true;
        }else{
            return false;
        }
    }

}
