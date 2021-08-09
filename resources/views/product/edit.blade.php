@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub"><a class="back-to" href="/product"><em class="icon ni ni-arrow-left"></em><span>Master Data Produk</span></a></div>
            <h2 class="nk-block-title fw-normal">Edit Data Produk</h2>
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
        <div class="card card-bordered">
            <div class="card-inner">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <form @submit.prevent="submitForm">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Nama</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="name" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kode</label>
                                <div class="form-control-wrap">
                                    <input type="text" :value="number" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="">Kategori</label>
                                <div class="form-control-wrap">
                                    <div class="input-group mb-3">
                                        <select v-on:change="onChangeCategory" class="form-control" v-model="product_category_id">
                                            <option v-for="category in product_categories" :value="category.id">@{{category.name}}</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryModal">
                                                <em class="fas fa-plus"></em>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="">Subkategori</label>
                                <div class="form-control-wrap">
                                    <div class="input-group mb-3">
                                        <select v-on:change="onChangeSubcategory" class="form-control" v-model="product_subcategory_id">
                                            <option v-for="subcategory in product_subcategories" :value="subcategory.id">@{{subcategory.name}}</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#subcategoryModal">
                                                <em class="fas fa-plus"></em>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Berat</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="weight" class="form-control" placeholder="Gram (gr)">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Stok Pusat</label>
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="central_stock" class="form-control" placeholder="Hanya angka">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Stok Retail</label>
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="retail_stock" class="form-control" placeholder="Hanya angka">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Stok Studio</label>
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="studio_stock" class="form-control" placeholder="Hanya angka">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Bad Stok</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="bad_stock" class="form-control" placeholder="Hanya angka">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Harga Beli</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="purchase_price" class="form-control" placeholder="Hanya Angka">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Harga Jual Agen</label>
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="agent_price" class="form-control" placeholder="Hanya Angka">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Harga Jual WS</label>
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="ws_price" class="form-control" placeholder="Hanya Angka">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Harga Jual Retail</label>
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="retail_price" class="form-control" placeholder="Hanya Angka">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Status</label>
                                <div class="form-control-wrap">
                                    <select v-model="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Izinkan Perubahan Harga</label>
                                <div class="form-control-wrap">
                                    <select v-model="is_changeable" class="form-control">
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="email-address-1">Email address</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="email-address-1">
                                </div>
                            </div>
                        </div> -->
                        <div class="col-12">
                            <div class="form-group text-right">
                                <button class="btn btn-primary" type="submit" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span>Simpan</span>
                                </button>
                                <!-- <button type="submit" class="btn btn-lg btn-primary">Simpan</button> -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
            <form @submit.prevent="addProductCategory">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="categoryModalLabel">Tambah Kategori</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="" class="form-label">Nama</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" v-model="category.name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label">Kode</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" v-model="category.code">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" :disabled="category.loading">
                                <span v-if="category.loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span>Simpan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal fade" id="subcategoryModal" tabindex="-1" aria-labelledby="subcategoryModalLabel" aria-hidden="true">
            <form @submit.prevent="addProductSubcategory">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="subcategoryModalLabel">Tambah Subkategori</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="" class="form-label">Nama</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" v-model="subcategory.name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label">Kode</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" v-model="subcategory.code">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" :disabled="subcategory.loading">
                                <span v-if="subcategory.loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span>Simpan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div><!-- .nk-block -->
</div>
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        data: {
            name: '{{ $product->name }}',
            code: '{{ $product->code }}',
            product_category_id: '{{ $product->product_category_id }}',
            product_subcategory_id: '{{ $product->product_subcategory_id }}',
            weight: '{{ $product->weight }}',
            central_stock: '{{ $product->central_stock }}',
            retail_stock: '{{ $product->retail_stock }}',
            studio_stock: '{{ $product->studio_stock }}',
            bad_stock: '{{ $product->bad_stock }}',
            purchase_price: '{{ $product->purchase_price }}',
            agent_price: '{{ $product->agent_price }}',
            ws_price: '{{ $product->ws_price }}',
            retail_price: '{{ $product->retail_price }}',
            status: '{{ $product->status }}',
            is_changeable: '{{ $product->is_changeable }}',
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
                axios.patch('/product/{{$product->id}}', {
                        name: this.name,
                        code: this.code,
                        product_category_id: this.product_category_id,
                        product_subcategory_id: this.product_subcategory_id,
                        weight: this.weight,
                        central_stock: this.central_stock,
                        retail_stock: this.retail_stock,
                        studio_stock: this.studio_stock,
                        bad_stock: this.bad_stock,
                        purchase_price: this.purchase_price,
                        agent_price: this.agent_price,
                        ws_price: this.ws_price,
                        retail_price: this.retail_price,
                        status: this.status,
                        is_changeable: this.is_changeable,
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
                                window.location.href = '/product';
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
            },
            addProductCategory: function() {
                // console.log('submitted');
                let vm = this;
                vm.category.loading = true;
                axios.post('/product-category', {
                        name: this.category.name,
                        code: this.category.code,
                    })
                    .then(function(response) {
                        vm.category.loading = false;
                        console.log(response);
                        vm.product_categories.push(response.data.data);
                        vm.product_category_id = response.data.data.id
                        vm.onChangeCategory();
                        $('#categoryModal').modal('hide')
                    })
                    .catch(function(error) {
                        vm.category.loading = false;
                        console.log(error);
                        Swal.fire(
                            'Oops!',
                            'Something wrong',
                            'error'
                        )
                    });
            },
            addProductSubcategory: function() {
                let vm = this;
                vm.subcategory.loading = true;
                axios.post('/product-subcategory', {
                        name: this.subcategory.name,
                        code: this.subcategory.code,
                    })
                    .then(function(response) {
                        vm.subcategory.loading = false;
                        console.log(response);
                        vm.product_subcategories.push(response.data.data);
                        vm.product_subcategory_id = response.data.data.id
                        vm.onChangeSubcategory();
                        $('#subcategoryModal').modal('hide')
                    })
                    .catch(function(error) {
                        vm.subcategory.loading = false;
                        console.log(error);
                        Swal.fire(
                            'Oops!',
                            'Something Wrong',
                            'error'
                        )
                    });
            },
            onChangeCategory: function() {
                const category = this.product_categories.filter(cat => cat.id == this.product_category_id)[0];
                if (category == null || typeof category == "undefined") {
                    this.prefix = '';
                } else {
                    this.prefix = category.code + '-';
                }
            },
            onChangeSubcategory: function() {
                const subcategory = this.product_subcategories.filter(cat => cat.id == this.product_subcategory_id)[0];
                if (subcategory == null || typeof subcategory == "undefined") {
                    this.infix = '';
                } else {
                    this.infix = subcategory.code + '-';
                }
            }
        },
        computed: {
            number: function() {
                return this.prefix + this.infix + this.code
            }
        }
    })
</script>
@endsection