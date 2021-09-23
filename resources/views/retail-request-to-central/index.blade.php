@extends('layouts.app')

@section('title', 'Vapehitz')
@section('content')
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <h4 class="nk-block-title fw-normal">Permintaan Barang ke Gudang Pusat</h4>
    </div>
</div>
<div class="nk-block nk-block-lg">
    <a href="{{url('/retail-request-to-central/create')}}" class="btn btn-primary"><em class="fas fa-plus"></em>&nbsp;Buat Baru</a>
    <p></p>
    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <div class="table-responsive">
                <table class="table table-striped" id="retail-request-to-central-table">
                    <thead class="text-center">
                        <tr>
                            <th>Nomor Proses</th>
                            <th>Tanggal Proses</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>
    $(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#retail-request-to-central-table', {
                processing: true,
                serverSide: true,
                destroy: true,
                autoWidth: false,
                // dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
                ajax: {
                    url: '/datatables/retail-request-to-central',
                    type: 'GET',
                },
                columns: [{
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
            })
            $.fn.DataTable.ext.pager.numbers_length = 7;
        }

        NioApp.DataTable.init();

    });
    $('#retail-request-to-central-table').on('click', 'tr .btn-delete', function(e) {
        e.preventDefault();
        // alert('click');
        const id = $(this).attr('data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "The data will be deleted",
            icon: 'warning',
            reverseButtons: true,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return axios.delete('/retail-request-to-central/' + id)
                    .then(function(response) {
                        console.log(response.data);
                    })
                    .catch(function(error) {
                        console.log(error.data);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops',
                            text: 'Something wrong',
                        })
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                // retailRequestToCentralTable.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Data has been deleted',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();

                    }
                })
            }
        })
    });
</script>
@endsection