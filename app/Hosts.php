<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


/**
 * @method static create(array $all)
 * @method static where(string $string, array|string|null $id)
 * @method static find($id)
 */
class Hosts extends Model
{

    public $incrementing = true;
    protected $table = 'hosts';
    protected $primaryKey = 'id';
    protected $fillable =
        [
            'id',
            'active',
            'host_name',
            'ftp_host',
            'ftp_slug',
            'ftp_port',
            'ftp_username',
            'ftp_password',
            'db_host',
            'db_host',
            'db_username',
            'db_password'
        ];

    public function store(Request $request)
    {
        // Validate the request...

        $hosts = new Hosts;
        $hosts->name = $request->name;
        $hosts->save();
    }

}
