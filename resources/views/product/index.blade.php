@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
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
        <a href="{{url('/product/create')}}" class="btn btn-outline-success">Tambah Produk</a>
        <p></p>
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <table class="table table-striped" id="products">
                    <thead>
                        <tr class="text-center">
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
    var productTable = $(function() {
        $('#products').DataTable({
            processing: true,
            serverSide: true,
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
                    name: 'purchase_price'
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
    });
</script>
@endsection