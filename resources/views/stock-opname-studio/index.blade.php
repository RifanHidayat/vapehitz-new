@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">Stock Opname Retail</h4>
            <!-- <div class="nk-block-des">
                <p>You can alow display form in column as example below.</p>
            </div> -->
        </div>
    </div>
    <a href="{{url('/studio-stock-opname/create')}}" class="btn btn-primary">Tambah Data</a>
    <p></p>
    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <div class="table-responsive">
                <table class="table table-striped" id="stockOpname">
                    <thead>
                        <tr class="text-center">
                            <th>Nomor Stock Opname</th>
                            <th>Tanggal Stock Opname</th>
                            <th>Keterangan</th>
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
        const stockOpnameTable = $('#stockOpname').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            "autoWidth": false,
            ajax: {
                url: '/datatables/stock-opname-studio',
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
                    data: 'note',
                    name: 'note'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ]
        });
        $('#stockOpname').on('click', 'tr .btn-delete', function(e) {
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
                    return axios.delete('/studio-stock-opname/' + id)
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
    });
</script>
@endsection