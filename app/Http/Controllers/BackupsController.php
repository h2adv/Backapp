<?php

namespace App\Http\Controllers;

use App\Backups;
use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
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
    private $message;

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
        $this->remoteFtpBackup();
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
                $download = $this->downloadFtpBackup($response);
                if(($download == true) && ($response->result == true)){
                    $this->message = ['host_id'=>$this->host->id,'type'=>'ftp','result'=>true];
                }else{
                    $this->message =  ['host_id'=>$this->host->id,'type'=>'ftp','result'=>$download];
                }
            }
            catch(\Exception $e)
            {
                $this->message =  ['host_id'=>$this->host->id,'type'=>'ftp','result'=>false,'message'=>$e->getMessage()];
            }
        }
        else
        {
            $this->message =  ['host_id'=>$this->host->id,'type'=>'ftp','result'=>false,'message'=>"Error uploading action file."];
        }
        echo json_encode($this->message);
        $this->registerBackup($this->message);
    }

    /**
     * @return mixed
     */
    public function sendBackupFile()
    {
        $file = storage_path().'/app/public/'.$this->file_name;
        $url_partial =  $this->host->ftp_host;
        $ftp_conn = ftp_connect($url_partial);
        $loggedIn = ftp_login($ftp_conn, $this->host->ftp_username, $this->host->ftp_password);

        if(true !== $loggedIn)
        {
            return false;
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
                $backup_file = 'Archive_sql_'.date("d-m-Y-H:i:s") . '['.$t.'].sql.zip';

                $file = $dir.'/'.$backup_file;
                $dump->start($file);

                if(!file_exists($dir)){
                    mkdir($dir, 0777, true);
                }
                if(file_exists($file)){
                    $cleaned = $this->cleanBackup($this->file_name);
                    if($cleaned == true){
                        echo json_encode(['host'=>$this->host,'type'=>'sql','result'=>true]);
                    }else{
                        echo json_encode(['host'=>$this->host,'type'=>'sql','result'=>false]);
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
    public function downloadFtpBackup($json_response)
    {

        $dir = $_SERVER['DOCUMENT_ROOT'].'/'.$this->backups_directory.'/'.$this->host->host_slug.'/ftp';
        $file = $dir.'/'.$json_response->filename;

        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }

        $file_put = file_put_contents($file, fopen($json_response->filename_url, 'r'));

        if(file_exists($file)){
            return $this->cleanBackup($json_response->filename);
        }
        else {
            return $file_put;
        }
    }

    /**
     * @param $file
     * @return mixed
     */
    public function cleanBackup($file)
    {
        $url = $this->host->domain.'/'.$this->file_name.'?clean=true';
        $data = json_encode(array(
            'file'=>'./'.$file,
        ));
        try {
            $this->curlConnect($data, $url, true);
            return true;
        } catch(\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    public function registerBackup($result){
        $backups = new Backups();
        $backups->host_id = $result['host_id'];
        $backups->type = ($result['type'] == 'ftp') ? 0 : 1;
        if(isset($result['message'])){
            $backups->message =  $result['message'];
        }
        $backups->save();
    }

}
