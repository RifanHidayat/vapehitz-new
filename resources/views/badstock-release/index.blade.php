@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title fw-normal">Pengeluaran Barang Bad Stock</h4>
        </div>
    </div>
    <div class="nk-block nk-block-lg">
        <a href="{{url('/badstock-release/create')}}" class="btn btn-primary"><em class="fas fa-plus"></em>&nbsp;Buat Baru</a>
        <p></p>
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <div class="table-responsive">
                    <table class="table table-striped" id="badstockRelease">
                        <thead>
                            <tr>
                                <th>Nomor Proses</th>
                                <th>Tanggal Proses</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        methods: {
            deleteRow: function(id) {
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
                        return axios.delete('/badstock-release/' + id)
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
            }
        }
    })
</script>
<script>
    var badstockReleaseTable = $(function() {
        $('#badstockRelease').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/datatables/badstock-release',
                type: 'GET',
                // length: 2,
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
                    data: 'action',
                    name: 'action'
                },

            ]
        });
    })
</script>
@endsection