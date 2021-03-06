<div class="msg" style="display:none;">
  <?= @$this->session->flashdata('msg'); ?>
</div>

<div class="box box-solid box-danger">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-book"></i> Daftar Dosen</h3>
    <div class="box-tools pull-right">
    
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <div class="row">
      <div class="col-md-6" style="padding-bottom:9 ;">
          <button class="form-control btn btn-primary" onclick="dosen_tambah()"><i class="glyphicon glyphicon-plus-sign"></i> Tambah Data</button>
      </div>
      <div class="col-md-3">
          <a href="<?= base_url('dosen/export'); ?>" class="form-control btn btn-default"><i class="glyphicon glyphicon glyphicon-floppy-save"></i> Export Data Excel</a>
      </div>
      <div class="col-md-3">
          <button class="form-control btn btn-default" data-toggle="modal" data-target="#import-dosen"><i class="glyphicon glyphicon glyphicon-floppy-open"></i> Import Data Excel</button>
      </div>
    </div>
    <div class="form-group"></div>
    <table id="table" cellspacing="0" width="100%" class="table table-hover table-bordered table-striped">
      <thead>
        <tr>
          <th>No</th>
          <th class="center"><i class="glyphicon glyphicon-plus"></i></th>
          <th>NiP</th>
          <th>Nama Dosen</th>
          <th>Jabatan</th>
          <th>No Telp</th>
          <th>Foto</th>
          <th style="text-align: center;">Pilihan</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
  <div class="box-footer">
    Menampilkan daftar Dosen, mengedit dan menghapus klik tombol pada kolom pilihan.<br>
    <b>F7</b> = Tambah data.
  </div><!-- box-footer -->
</div>
<?= $modal_dosen; ?>
<?php show_my_confirm('konfirmasiHapus', 'hapus-dataDosen', 'Hapus Data Ini?', 'Ya, Hapus Data Ini'); ?>
<script>
  var dataTable;
$(document).ready(function() {
    dataTable = $('#table').DataTable( {
      "serverSide": true,
      "stateSave" : false,
      "bAutoWidth": true,
      "oLanguage": {
        "sSearch": "<i class='fa fa-search fa-fw'></i> Pencarian : ",
        "sLengthMenu": "_MENU_ &nbsp;&nbsp;Data Per Halaman ",
        "sInfo": "Menampilkan _START_ s/d _END_ dari <b>_TOTAL_ data</b>",
        "sInfoFiltered": "(difilter dari _MAX_ total data)", 
        "sZeroRecords": "Pencarian tidak ditemukan", 
        "sEmptyTable": "Data kosong", 
        "sLoadingRecords": "Harap Tunggu...", 
        "oPaginate": {
          "sPrevious": "Sebelumnya",
          "sNext": "Selanjutnya"
        }
      },
      "aaSorting": [[ 0, "desc" ]],
      "columnDefs": [ 
        {
          "targets": 'no-sort',
          "orderable": false,
        },
        { 
          "targets": [ -1 ],
          "orderable": false, 
        },
        { 
          "targets": [-2],
          "visible": false,
        },
        { 
          "targets": [ 1 ],
          "orderable": false, 
        },
          ],
      "sPaginationType": "simple_numbers", 
      "iDisplayLength": 10,
      "aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
      "ajax":{
        url :"<?= base_url('admin/masteranggota/dosen_list'); ?>",
        type: "post",
        error: function(){ 
          $(".my-grid-error").html("");
          $("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
          $("#my-grid_processing").css("display","none");
        }
      }
      <?php
      if ($this->session->flashdata('msg') != '') {
        echo "effect_msg();";
      }
    ?>
    } );

  });
  function effect_msg_form() {
      // $('.form-msg').hide();
      $('.form-msg').show(1000);
      setTimeout(function() { $('.form-msg').fadeOut(1000); }, 3000);
    }

    function effect_msg() {
      // $('.msg').hide();
      $('.msg').show(1000);
      setTimeout(function() { $('.msg').fadeOut(1000); }, 3000);
    }

  function reload_table()
  {
      dataTable.ajax.reload(null,false); //refresh table
  }

  function dosen_tambah()
  {
      save_method = 'tambahDosen';
      $('#form-dosen')[0].reset(); 
      $('#dosen').modal('show');
      $('.form-msg').html('');
      $('.modal-title').text('Tambah Dosen Baru'); 
      $('#label-foto').text('Upload Foto'); // merubah label
      $('#foto-preview').hide(); //menyembunyikan foto sebelumnya
  }

  function simpan()
{
    var url;

    if(save_method == 'tambahDosen') {
        url = "<?= site_url('admin/masteranggota/dosen_tambah')?>";
    } else {
        url = "<?= site_url('admin/masteranggota/dosen_proses_ubah')?>";
    }

    // ajax adding data to database
 
    var formData = new FormData($('#form-dosen')[0]);
    $.ajax({
        url : url,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function(data)
        {

            if(data.status) //jika berhasil
            {
                $('.form-msg').html(data.msg);
                effect_msg_form();
            }
            else
            {
                // $('#preview').remove();
                $('#dosen').modal('hide');
                $('.msg').html(data.msg);
                   effect_msg();
                reload_table();
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('gagal');
        }
    });
}
function dosen_ubah(nim)
{
    save_method = 'ubahDosen';
    $('#form-dosen')[0].reset();
    $('#dosen').modal('show'); 
    $('.form-msg').html('');
     // $('#preview').html('');
    $('#foto-preview').show(); //mengeluarkan foto sebelumny
    $('.modal-title').text('Ubah Data Dosen');

    $('#nip').attr('readonly',true);$('#jabatan').hide();

    //Ajax Load data from ajax
    $.ajax({
        url : "<?= site_url('admin/masteranggota/dosen_ubah')?>/" + nim,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="nip"]').val(data.nip);
            $('[name="nama_dosen"]').val(data.nama);
            $('[name="no_tlp"]').val(data.no_tlp);
            $('[name="foto_lama"]').val(data.foto);
            // $('#foto-preview').show(); // show photo preview modal

            if(data.foto)
            {
                $('#label-foto').text('Ubah foto'); // label foto upload
                $('#foto-preview div').html('<img src="<?= base_url()?>upload/anggota/'+data.foto+'" class="img-responsive">'); // show photo
                $('#foto-preview div').append('<input type="checkbox" name="remove_photo" value="'+data.foto+'"/> hapus foto ketika disimpan'); // remove photo

            }
            else
            {
                $('#label-foto').text('Upload Photo'); // label photo upload
                $('#foto-preview div').text('(No photo)');
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('gagal menampilkan data');
        }
    });
}
var nip;
  $(document).on("click", ".konfirmasiHapus-dosen", function() {
    nip = $(this).attr("data-id");
  })
  $(document).on("click", ".hapus-dataDosen", function() {
    var id = nip;
    
    $.ajax({
      method: "POST",
      url: "<?= base_url('admin/masteranggota/dosen_hapus'); ?>",
      data: "nip=" +id
    })
    .done(function(data) {
      $('#konfirmasiHapus').modal('hide');
      $('.msg').html(data);
      effect_msg();
      reload_table();
    })
  })

$(document).on('keydown', 'body', function(e){
        var charCode = ( e.which ) ? e.which : event.keyCode;

        if(charCode == 118) //F7
        {
            mahasiswa_tambah();
            return false;
        }
});
  </script>