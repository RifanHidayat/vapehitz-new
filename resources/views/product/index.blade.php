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
                <table class="datatable-init table table-striped">
                    <thead>
                        <tr class="text-center">
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Subkategori</th>
                            <th>Nama</th>
                            <!-- <th>Stok Pusat</th>
                            <th>Stok Retail</th>
                            <th>Stok Studio</th>
                            <th>Bad Stok</th> -->
                            <th>Berat</th>
                            <th>Harga Beli</th>
                            <!-- <th>Harga Jual Agn</th>
                            <th>Harga Jual Ret</th>
                            <th>Harga Jual Whs</th> -->
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr class="text-center">
                            <td>{{ $product->code }}</td>
                            <td>{{ $product->productCategory->name }}</td>
                            <td>{{ $product->productSubcategory->name }}</td>
                            <td>{{ $product->name }}</td>
                            <!-- <td>{{ $product->central_stock }}</td>
                            <td>{{ $product->retail_stock }}</td>
                            <td>{{ $product->studio_stock }}</td>
                            <td>{{ $product->bad_stock }}</td> -->
                            <td>{{ $product->weight }}</td>
                            <td>{{ $product->purchase_price }}</td>
                            <!-- <td>{{ $product->agent_price }}</td>
                            <td>{{ $product->retail_price }}</td>
                            <td>{{ $product->ws_price }}</td> -->
                            <td>
                                @if($product->status == 1)
                                <span class="badge badge-outline-success">Active</span>
                                @else
                                <span class="badge badge-outline-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" aria-label="Basic example">
                                    <a href="/product/edit/{{$product->id}}" class="btn btn-outline-light"><em class="fas fa-pencil-alt"></em></a>
                                    <a href="#" @click.prevent="deleteRow({{ $product->id }})" class="btn btn-outline-light"><em class="fas fa-trash-alt"></em></a>
                                    <a href="/product/show/{{$product->id}}" class="btn btn-outline-light"><em class="fas fa-eye"></em></a>
                                </div>
                                <!-- <em class="far fa-heart"></em>
                                <em class="fas fa-star"></em>
                                <em class="fab fa-facebook"></em> -->
                            </td>
                        </tr>
                        @endforeach
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
@endsection