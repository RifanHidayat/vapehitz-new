@extends('layouts.app')

@section('title', 'Vapehitz')
@section('pagestyle')
<style>
    /* .dataTables_filter {
        text-align: right;
        width: 90%;
    }

    table tr th {
        font-size: 15px;
        /* color: black; */
    }

    table tr td {
        font-size: 13px;
        /* color: black; */
    }

    .pull-left {
        float: left !important;
    }

    .pull-right {
        float: right !important;
        margin-bottom: 20px;
    }

    .bottom {
        float: right !important;
    }

    */ #suppliers tr th,
    #suppliers tr td {
        font-size: 0.875rem;
    }
</style>
@endsection
@section('content')
@php $permission = json_decode(Auth::user()->group->permission);@endphp
<div class="components-preview">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Supplier</h3>
                <div class="nk-block-des text-soft">
                    <p>Manage Supplier</p>
                </div>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li>
                                <a href="#" class="btn btn-white btn-dim btn-outline-primary disabled" data-toggle="tooltip" data-placement="top" title="On Development">
                                    <em class="icon ni ni-download-cloud"></em>
                                    <span>Export</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-white btn-dim btn-outline-primary disabled" data-toggle="tooltip" data-placement="top" title="On Development">
                                    <em class="icon ni ni-reports"></em>
                                    <span>Reports</span>
                                </a>
                            </li>
                            @if(in_array("add_supplier", $permission))
                            <li><a href="/supplier/create" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>New Supplier</span></a></li>
                            @endif

                        </ul>
                    </div>
                </div>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div>
    <div class="nk-block nk-block-lg">
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <div class="table-responsive">
                    <table class="table table-striped" id="suppliers">
                        <thead class="text-center">
                            <tr>
                                <th>Code</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <!-- <th>No. Tlp</th> -->
                                <th>No. Tlp/Hp/Wa</th>
                                <th>Email</th>
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
                        return axios.delete('/supplier/' + id)
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
                                // invoicesTable.ajax.reload();
                            }
                        })
                    }
                })
            }
        }
    })
</script>
<script>
    $(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#suppliers', {
                processing: true,
                serverSide: true,
                autoWidth: false,
                // dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
                ajax: {
                    url: "{{url('/datatables/suppliers')}}",
                    type: 'GET',
                    //length: 2,
                },
                columns: [{
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    // {
                    //     data: 'telephone',
                    //     name: 'telephone'
                    // },
                    {
                        data: 'handphone',
                        name: 'handphone'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(row) {
                            if (row == '1')
                                return '<span class="badge badge-outline-success">Active</span>'
                            else
                                return '<span class="badge badge-outline-danger">Inactive</span>'
                        },
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },

                ]
            });
            $.fn.DataTable.ext.pager.numbers_length = 7;
        }

        NioApp.DataTable.init();
        // var supplierTable = $('#suppliers').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     autoWidth: false,
        //     // dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
        //     ajax: {
        //         url: "{{url('/datatables/suppliers')}}",
        //         type: 'GET',
        //         //length: 2,
        //     },
        //     columns: [{
        //             data: 'code',
        //             name: 'code'
        //         },
        //         {
        //             data: 'name',
        //             name: 'name'
        //         },
        //         {
        //             data: 'address',
        //             name: 'address'
        //         },
        //         {
        //             data: 'telephone',
        //             name: 'telephone'
        //         },
        //         {
        //             data: 'handphone',
        //             name: 'handphone'
        //         },
        //         {
        //             data: 'email',
        //             name: 'email'
        //         },
        //         {
        //             data: 'status',
        //             name: 'status',
        //             render: function(row) {
        //                 if (row == '1')
        //                     return '<span class="badge badge-outline-success">Active</span>'
        //                 else
        //                     return '<span class="badge badge-outline-danger">Inactive</span>'
        //             },
        //         },
        //         {
        //             data: 'action',
        //             name: 'action'
        //         },

        //     ]
        // });
        $('#suppliers').on('click', 'tr .btn-delete', function(e) {
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
                    return axios.delete('/supplier/' + id)
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
                    supplierTable.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Data has been deleted',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // window.location.reload();

                        }
                    })
                }
            })
        })
    });
</script>
@endsection