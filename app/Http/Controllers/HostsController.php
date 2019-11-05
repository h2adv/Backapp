<?php

namespace App\Http\Controllers;

use App\Hosts;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;

class HostsController extends Controller
{

    /**
     * Show all of the users for the application.
     *
     * @return Response
     */
    public function getHosts()
    {
        $hosts = DB::table('hosts')->get();
        return view('hosts', ['hosts' => $hosts]);
    }

    public function toggle(Request $request)
    {
        $data = $request->json()->all();
        $id = $data['id'];
        return response()->json([
            'id' => $id,
        ]);
    }

    public function createHosts(Request $request)
    {
        Hosts::create($request->all());
        return redirect('/hosts/get');
    }

    public function deleteHosts(Request $request)
    {
        $id = $request->input('id');
        if(Hosts::where('id', $id)->delete()){
            echo json_encode(array('response' => true));
        }
        else{
            echo json_encode(array('response' => false));
        }
    }


    public function editHost(Request $request)
    {
        $id = $request->id;
        $saved = $request->saved;

        $host = DB::table('hosts')->find($id);
        return view('host-edit', ['host' => $host,'saved'=>$saved]);
    }

    public function editDoHost(Request $request)
    {
        $host =  Hosts::find($request->id);
        $host->id = $request->id;
        $host->active = $request->active;
        $host->domain = $request->domain;
        $host->domain = $request->domain;
        $host->domain = $request->domain;
        $host->host_name = $request->host_name;
        $host->ftp_host = $request->ftp_host;
        $host->host_slug = $request->host_slug;
        $host->ftp_username = $request->ftp_username;
        $host->ftp_password = $request->ftp_password;
        $host->ftp_directory = $request->ftp_directory;
        $host->db_host = $request->db_host;
        $host->is_local = $request->is_local;
        $host->local_path = $request->local_path;
        if($host->save()){
            return redirect()->route('host-saved', ['id'=>$request->id,'saved'=>true]);
        }
    }

}
