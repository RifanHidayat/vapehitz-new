@extends('layouts.app')

@section('title', 'Vapehitz')
<style>
    .dataTables_filter {
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
</style>
@section('content')
@php $permission = json_decode(Auth::user()->group->permission);@endphp
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <!-- <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em class="icon ni ni-arrow-left"></em><span>Manage</span></a></div> -->
        <h2 class="nk-block-title fw-normal">Master Data Produk</h2>
        <div class="nk-block-des">
            <p class="lead">Manage Produk</p>
        </div>
    </div>
</div><!-- .nk-block -->
<div class="nk-block nk-block-lg">
    <!-- <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Tambah Kategori Barang</h4>
                <div class="nk-block-des">
                    <p>You can alow display form in column as example below.</p>
                </div>
            </div>
        </div> -->
    @if(in_array("add_product", $permission))
    <a href="{{url('/product/create')}}" class="btn btn-outline-primary"><em class="fas fa-plus"></em>&nbsp;Tambah Produk</a>
    @endif
    <p></p>
    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
            <table class="table table-striped" id="products">
                <thead class="text-center">
                    <tr>
                        <th>Kode</th>
                        <th>Kategori</th>
                        <th>Subkategori</th>
                        <th>Nama</th>
                        <th>Berat</th>
                        <th>Harga Beli</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                </tbody>
            </table>
        </div>
    </div>
</div><!-- .nk-block -->

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
                        return axios.delete('/product/' + id)
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
    var productTable = $('#products').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
        ajax: {
            url: '/datatables/products',
            type: 'GET',
            // length: 2,
        },
        columns: [{
                data: 'code',
                name: 'code'
            },
            {
                data: 'product_category.name',
                name: 'productCategory.name'
            },
            {
                data: 'product_subcategory.name',
                name: 'productSubcategory.name'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'weight',
                name: 'weight'
            },
            {
                data: 'purchase_price',
                name: 'purchase_price',
                className: 'text-right',
                render: function(data) {
                    return Intl.NumberFormat('de-DE').format(data);
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    if (data == '1') {
                        return '<span class="badge badge-outline-success">Active</span>';
                    } else {
                        return '<span class="badge badge-outline-danger">Inactive</span>';
                    }
                },
            },
            {
                data: 'action',
                name: 'action'
            },

        ]
    });
    $('#products').on('click', 'tr .btn-delete', function(e) {
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
                return axios.delete('/product/' + id)
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
                productTable.ajax.reload();
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
    });
</script>
@endsection