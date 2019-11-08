
$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // TOGGLE HOST
    $('.host-active').on('click',function () {

        if($(this).prop("checked")){
            $(this).attr('value',1)
        }else{
            $(this).attr('value',0)
        }

        let id = $(this).parent("form").attr('id');
        let obj = $('#'+id+'.toggle-form');
        let form = obj[0];
        let data = obj.serialize();
        let url = form.action;
        console.log(form);
        console.log(url);
        console.log(data);

        form.ajaxForm({
            type: "POST",
            dataType: "json",
            url: url,
            data: data, // serializes the form's elements.
            success: function (data) {
                console.log('Submission was successful.');
                console.log(data);
            },
            error: function (data) {
                console.log('An error occurred.');
                console.log(data);
            },
        });

    });

    $('.host-delete').on('click',function () {

        let url = $(this).attr('data-url');
        let id = {id: $(this).attr('data-id')};
        let data =  JSON.stringify(id);
        console.log(url);
        console.log(id);

        $.ajax({
            type: 'POST',
            dataType: "json",
            data: id,
            url: url,
            success: function (data) {
                let obj = data;
                if(obj.response === true) {
                    location.reload();
                }else{
                    console.log('Error');
                }
            },
            error: function(data) {
                console.log('Error');
            }
        });
    });


    $('.host-edit').on('click',function () {

        let url = $(this).attr('data-url');
        let id = {id: $(this).attr('data-id')};
        let data =  JSON.stringify(id);
        console.log(url);
        console.log(id);

        $.ajax({
            type: 'POST',
            dataType: "json",
            data: id,
            url: url,
            success: function (data) {
                let obj = data;
                if(obj.response === true) {
                    location.reload();
                }else{
                    console.log('Error');
                }
            },
            error: function(data) {
                console.log('Error');
            }
        });
    });

    // SET AS LOCAL AN HOST
    $('.is-local').on('click', function(){
        if ($(this).is(':checked')) {
            $('.remote').hide();
            $('.localhost').show();

            $(this).val(1);
        }else{
            $('.remote').show();
            $('.localhost').hide();
            $(this).val(0);
        }
    });
    if ($('.is-local').is(':checked')) {
        $('.localhost').show();
        $('.remote').hide();
    }else{
        $('.remote').show();
        $('.localhost').hide();

    }


    $('.backup-ftp-do').on('click',function () {
        $('#loading').show();
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: '/backups/ftp-do/'+$(this).attr('data-id'),
            success: function (data) {
                let error = data.message;
                console.log(data);

                if(data.result === true) {
                    $('#loading').hide();
                    showMessageBackup(data.host_id)
                }else{
                    $('#loading').hide();
                }
            },
            error: function(data) {
                $('#loading').hide();
            }
        });
    });

    $('.backup-mysql-do').on('click',function () {
        $('#loading').show();
        let data = {id: $(this).attr('data-id')};
        $.ajax({
            type: 'POST',
            dataType: "json",
            data: data,
            url: '/backups/sql-do',
            success: function (data) {
                let error = data.message;
                console.log(data);

                if(data.result === true) {
                    $('#loading').hide();
                    showMessageBackup(data.host_id);
                }else{
                    $('#loading').hide();
                    showMessageBackup(data.host_id,data.message);
                }
            },
            error: function(data) {
                $('#loading').hide();
                showMessageBackup(data.host_id,data.message);
            }
        });
    });

});

function loadingStart(){
    $('#loading').show();
}

function loadingEnd(){
    $('#loading').hide();
}

function showMessageBackup(id,error) {
    let message_box = $('.message-backup');
    message_box.show();
    message_box.html();
    if(error) {
        message_box.addClass('alert-warning');
        message_box.text('Backup error on id: '+id);
        message_box.append('<p>'+error+'</p>');
    }else{
        message_box.addClass('alert-success');
        message_box.text('Backup done on id: '+id);
    }
}
