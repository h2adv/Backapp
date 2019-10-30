<h2 style="margin-top:155px;">New Host</h2>

{{ Form::open(array('url' => 'hosts/create', 'method' => 'post', 'class' => 'create-host-form')) }}
<table class="table" style="margin: 50px 0">
    <tbody>
    <tr>
        <td>
            <span style="font-weight: bold">{{ Form::checkbox('active', 1) }} Active?</span>
        </td>
        <td>
            <span style="font-weight: bold">{{ Form::text('host_name', null, array('placeholder' => 'host_name')) }}</span>
        </td>
        <td>
            <span style="font-weight: bold">{{ Form::text('ftp_host', null, array('placeholder' => 'ftp_host')) }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span style="font-weight: bold">{{ Form::text('ftp_username', null, array('placeholder' => 'ftp_username')) }}</span>
        </td>
        <td>
            <span style="font-weight: bold">{{ Form::text('ftp_password', null, array('placeholder' => 'ftp_password')) }}</span>
        </td>
        <td>
            <span style="font-weight: bold">{{ Form::text('db_host', null, array('placeholder' => 'db_host')) }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span style="font-weight: bold">{{ Form::text('db_username', null, array('placeholder' => 'db_username')) }}</span>
        </td>
        <td>
            <span style="font-weight: bold">{{ Form::text('db_password', null, array('placeholder' => 'db_password')) }}</span>
        </td>
        <td>
            <span style="font-weight: bold">{{ Form::submit('Sibmit') }}</span>
        </td>
    </tr>
    </tbody>
</table>
{{ Form::close() }}
