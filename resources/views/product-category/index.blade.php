@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <!-- <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em class="icon ni ni-arrow-left"></em><span>Manage</span></a></div> -->
            <h2 class="nk-block-title fw-normal">Kategori Barang</h2>
            <div class="nk-block-des">
                <p class="lead">Manage Kategori Barang</p>
            </div>
        </div>
    </div><!-- .nk-block -->
    <a href="{{url('/product-category/create')}}" class="btn btn-outline-success">Tambah Kategori</a>
    <p></p>
    <div class="nk-block nk-block-lg">
        <!-- <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Tambah Kategori Barang</h4>
                <div class="nk-block-des">
                    <p>You can alow display form in column as example below.</p>
                </div>
            </div>
        </div> -->
        <div class="card card-bordered">
            <div class="card-inner">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <table class="datatable-init nowrap table">
                    <thead>
                        <tr class="text-center">
                            <th>Nama</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product_categories as $category)
                        <tr class="text-center">
                            <td>{{ $category->name }}</td>
                            <td>
                                <div class="btn-group" aria-label="Basic example">
                                    <a href="#" class="btn btn-outline-light"><em class="fas fa-pencil-alt"></em></a>
                                    <a href="#" class="btn btn-outline-light"><em class="fas fa-trash-alt"></em></a>
                                    <button type="button" class="btn btn-outline-light dropdown-toggle dropdown-toggle-split" data-toggle="dropdown">
                                        <em class="fas fa-ellipsis-h"></em><span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu">
                                        <ul class="link-list-opt no-bdr">
                                            <li><a href="#"><span>Profile Settings</span></a></li>
                                            <li><a href="#"><span>Notification</span></a></li>
                                            <li><a href="#"></em><span>Another action</span></a></li>
                                        </ul>
                                    </div>
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
        data: {
            name: '',
            loading: false,
        },
        methods: {
            submitForm: function() {
                this.sendData();
            },
            sendData: function() {
                // console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.post('/product-category', {
                        name: this.name,
                    })
                    .then(function(response) {
                        vm.loading = false;
                        Swal.fire({
                            title: 'Success',
                            text: 'Data has been saved',
                            icon: 'success',
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/product-category';
                            }
                        })
                        // console.log(response);
                    })
                    .catch(function(error) {
                        vm.loading = false;
                        console.log(error);
                        Swal.fire(
                            'Oops!',
                            'Something wrong',
                            'error'
                        )
                    });
            }
        }
    })
</script>
@endsection