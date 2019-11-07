<?php

namespace App\Http\Controllers;

use http\Exception;
use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;


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
    private $id;
    /**
     * @var \Illuminate\Support\Collection
     */

    public function __construct()
    {
        $this->file_name = config('app.script_file');
        $this->backups_directory = config('app.backups_directory');
    }

    /**
     * Show all of the users for the application.
     *
     * @return View
     */
    public function getBackupsHistory()
    {
        $backups = DB::table('backups')->get();
        return view('backups', ['backups' => $backups]);
    }

    /**
     * Show all of the users for the application.
     *
     * @param Request $request
     * @return void
     */
    public function postBackups(Request $request)
    {
        return view('backup-saved');
    }

    /**
     * Show all of the users for the application.
     *
     * @param Request $request
     * @return View
     */
    public function getBackups(Request $request)
    {
        $message = '';
        $this->host = DB::table('hosts')->get();
        if($request->route('backup_saved')){
            $message = $request->route('backup_saved');
        }
        return view('backup', ['hosts' => $this->host, 'message' => $message]);
    }

    /**
     * Show all of the users for the application.
     * @param Request $request
     */
    public function ftpDoBackup(Request $request)
    {
        $id = $request->route('id');
        $this->host = DB::table('hosts')->find($id);
        if($this->isLocal()){
            $this->localFtpBackup();
        }else{
            $this->remoteFtpBackup();
        }
    }

    /**
     * @return RedirectResponse
     */
    public function remoteFtpBackup()
    {
        if ($this->sendBackupFile()){
            $data = json_encode(array(
                'directory'=>$this->host->ftp_directory
            ));
            try
            {
                $url = $this->host->domain.'/'.$this->file_name.'?backup_ftp=true';
                $response = $this->curlConnect($data, $url);
                $download = $this->downloadBackup($response);
                if(($download == true) && ($response->result == true)){
                    echo json_encode(['host'=>$this->host,'result'=>true]);
                }else{
                    echo json_encode(['host'=>$this->host,'result'=>false]);
                }
            }
            catch(Exception $e)
            {
                echo json_encode(['host'=>$this->host,'result'=>false,'error'=>$e->getMessage()]);
            }
        }
        else
        {
            echo json_encode(['host'=>$this->host,'result'=>false,'error'=>"Error uploading action file."]);
        }
    }

    /**
     * @return mixed
     */
    public function sendBackupFile()
    {
        $file = storage_path().'/app/public/'.$this->file_name;
        $url_partial =  $this->host->ftp_host;
        $ftp_conn = ftp_connect($url_partial);
        if(!ftp_login($ftp_conn, $this->host->ftp_username, $this->host->ftp_password))
        {
            die('Ftp login error');
        }
        $file_put = ftp_put($ftp_conn, $this->host->ftp_directory."/".$this->file_name, $file, FTP_ASCII);
        if($file_put){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $data
     * @param $url
     * @param null $opt
     * @return false|mixed|string
     */
    public function curlConnect($data, $url, $opt = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FAILONERROR, true );
        curl_setopt($ch, CURLOPT_HEADER, false );
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
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

            return json_decode(curl_error($ch));
        }

        return json_decode($response);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function sqlDoBackup(Request $request)
    {
        $id = $request->input('id');
        $file = storage_path().'/app/public/'.$this->file_name;
        $this->host = DB::table('hosts')->find($id);

        if ($this->sendBackupFile()){

            try {
                $dump = new Mysqldump('mysql:host='.$this->host->db_host.';dbname='.$this->host->db_name, $this->host->db_username, $this->host->db_password, ['compress' => Mysqldump::GZIP]);
                $dir = $_SERVER['DOCUMENT_ROOT'].'/'.$this->backups_directory.'/'.$this->host->host_slug.'/sql/';
                $t = dechex(substr_replace(substr((string)explode(" ",microtime())[0],2), '', -4));
                $backup_file = date("Y-m-d-H:i:s") . '['.$t.'].sql.zip';

                $file = $dir.'/'.$backup_file;
                $dump->start($file);

                if(!file_exists($dir)){
                    mkdir($dir, 0777, true);
                }
                if(file_exists($file)){
                    $cleaned = $this->cleanBackup($this->file_name);
                    if($cleaned == true){
                        echo json_encode(['host'=>$this->host,'result'=>true]);
                    }else{
                        echo json_encode(['host'=>$this->host,'result'=>false]);
                    }
                }
            } catch (\Exception $e) {
                echo 'mysqldump-php error: ' . $e->getMessage();
            }

            return;
        }
        else
        {
            echo json_encode(array('result'=>"Error uploading $file."));
            return;
        }
    }


    /**
     * @param $json_response
     * @return bool|mixed
     */
    public function downloadBackup($json_response)
    {
        if($json_response->type == 'ftp') {
            $path='ftp';
        } else {
            $path= 'sql';
        }

        $dir = $_SERVER['DOCUMENT_ROOT'].'/'.$this->backups_directory.'/'.$this->host->host_slug.'/'.$path;
        $file = $dir.'/'.$json_response->filename;

        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }

        file_put_contents($file, fopen($json_response->filename_url, 'r'));

        if(file_exists($file)){
            return $this->cleanBackup($json_response->filename);
        }
        else {
            return false;
        }
    }

    /**
     * @param $file
     * @param $this->host
     * @return mixed
     */
    public function cleanBackup($file)
    {
        $url = $this->host->domain.'/'.$this->file_name.'?clean=true';
        $data = json_encode(array(
            'file'=>'./'.$file,
        ));
        try {
            $response = $this->curlConnect($data, $url, true);
            return true;
        } catch(Exception $e) {
            echo json_encode($e->getMessage());
            return false;
        }
    }


    /**
     * @return bool
     */
    public function isLocal()
    {
        if($this->host->is_local == 1){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $this->host
     */
    public function localFtpBackup()
    {

//        $file = storage_path().'/app/public/backup_h2adv.php';
//        $url_partial =  $this->host->ftp_host;
//
//        $url = 'http://'.$this->host->domain.'/backup_h2adv.php?backup_ftp=true' ;
//        $ftp_conn = ftp_connect($url_partial);
//
//        if(!ftp_login($ftp_conn, $this->host->ftp_username, $this->host->ftp_password))
//        {
//            die('Ftp login error');
//        }
//
//        if(ftp_put($ftp_conn, $this->host->ftp_directory."/backup_h2adv.php", $file, FTP_ASCII))
//        {
//
//            ftp_close($ftp_conn);
//            $data = json_encode(array(
//                'directory'=>$this->host->ftp_directory
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
//                echo json_encode($this->downloadBackup($json_response,$this->host));
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

}
