@extends('layouts.app')

@section('title', 'Vapehitz')
@section('pagestyle')
<style>
    #products tr th,
    #products tr td {
        font-size: 0.875rem;
    }
</style>
@endsection
@section('content')
@php $permission = json_decode(Auth::user()->group->permission);@endphp
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Product</h3>
            <div class="nk-block-des text-soft">
                <p>Manage Product</p>
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
                        @if(in_array("add_product", $permission))
                        <li>
                            <a href="/product/create" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em>
                                <span>New Product</span>
                            </a>
                        </li>
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
                <tbody>
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
    $(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#products', {
                processing: true,
                serverSide: true,
                autoWidth: false,
                // dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
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
                        render: function(data) {
                            return Intl.NumberFormat('De-de').format(data);
                        },
                        className: 'text-right'
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
            })
            $.fn.DataTable.ext.pager.numbers_length = 7;
        }
        NioApp.DataTable.init();

        // var productTable = $('#products').DataTable({

        // });
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
    })
</script>
@endsection