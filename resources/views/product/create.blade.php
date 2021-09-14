@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub"><a class="back-to" href="/product"><em class="icon ni ni-arrow-left"></em><span>Master Data Produk</span></a></div>
            <h2 class="nk-block-title fw-normal">Tambah Data Produk</h2>
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
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Kode</label>
                                    <div class="form-control-wrap">
                                        <input type="text" :value="number" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            <p></p>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Nama</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="name" class="form-control" placeholder="Masukan nama produk">
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
                                            <option v-for="subcategory in subCategoryOptions" :value="subcategory.id">@{{subcategory.name}}</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#subcategoryModal">
                                                <em class="fas fa-plus"></em>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Berat (gr)</label>
                                    <div class="form-control-wrap">
                                        <input type="text" @keypress="isNumber($event)" v-model="weight" class="form-control" placeholder="Gram (gr)">
                                    </div>
                                </div>
                            </div>
                            <p></p>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Stok Pusat</label>
                                    <div class="form-control-wrap">
                                        <input type="text" @keypress="isNumber($event)" v-model="central_stock" class="form-control" placeholder="Stok Pusat">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Stok Retail</label>
                                    <div class="form-control-wrap">
                                        <input type="text" @keypress="isNumber($event)" v-model="retail_stock" class="form-control" placeholder="Stok Retail">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Stok Studio</label>
                                    <div class="form-control-wrap">
                                        <input type="text" @keypress="isNumber($event)" v-model="studio_stock" class="form-control" placeholder="Stok Studio">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Bad Stok</label>
                                    <div class="form-control-wrap">
                                        <input type="text" @keypress="isNumber($event)" v-model="bad_stock" class="form-control" placeholder="Bad Stok">
                                    </div>
                                </div>
                                <div class="form-group col-md-8">
                                    <label class="form-label" for="full-name-1">Harga Beli</label>
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="purchase_price" v-cleave="cleaveCurrency" class="form-control text-right" placeholder="Harga Beli">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Harga Jual Agen</label>
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="agent_price" v-cleave="cleaveCurrency" class="form-control text-right" class="form-control" placeholder="Harga Jual Agen">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Harga Jual WS</label>
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="ws_price" v-cleave="cleaveCurrency" class="form-control text-right" class="form-control" placeholder="Harga Jual WS">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="full-name-1">Harga Jual Retail</label>
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="retail_price" v-cleave="cleaveCurrency" class="form-control text-right" class="form-control" placeholder="Harga Jual Retail">
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
    </div>
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Tambah Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="is_edit_category ? editProductCategory(category_edit_id,category_edit_index) : addProductCategory()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="" class="form-label col-md-8">Nama</label>
                            <div class="form-control-wrap col-md-8">
                                <input type="text" class="form-control" v-model="category.name" placeholder="Nama Kategori">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label col-md-8">Kode</label>
                            <div class="form-control-wrap col-md-4">
                                <input type="text" class="form-control" v-model="category.code" placeholder="Kode Kategori">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button v-if="is_edit_category == true" v-on:click="onCloseEdit" type="button" class="btn btn-primary">
                            &times
                        </button>
                        <button class="btn btn-primary" type="submit" :disabled="category.loading">
                            <span v-if="category.loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>@{{is_edit_category ? "Edit" : "Simpan" }}</span>
                        </button>
                    </div>
                </form>
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Manage Kategori</h5>
                </div>
                <div class="modal-body">
                    <table class="datatable-init table table-striped">
                        <thead>
                            <tr class="text-center">
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(category, index) in product_categories" :value="category.id" class="text-center">
                                <td>@{{category.code}}</td>
                                <td>@{{category.name}}</td>
                                <td>
                                    <div class="btn-group" aria-label="Basic example">
                                        <a href="#" @click.prevent="onEditCategory(index)" class="btn btn-outline-light"><em class="fas fa-pencil-alt"></em></a>
                                        <a href="#" @click.prevent="deleteRow(category.id)" class="btn btn-outline-light"><em class="fas fa-trash-alt"></em></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="subcategoryModal" tabindex="-1" aria-labelledby="subcategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subcategoryModalLabel">Tambah Subkategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="is_edit_subcategory ? editProductSubcategory(subcategory_edit_id,subcategory_edit_index) : addProductSubcategory()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="" class="form-label col-md-8">Pilih Kategori</label>
                            <div class="form-control-wrap col-md-8">
                                <select class="form-control" v-model="subcategory.product_category_id">
                                    <option v-for="category in product_categories" :value="category.id">@{{category.name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label col-md-8">Nama</label>
                            <div class="form-control-wrap col-md-8">
                                <input type="text" class="form-control" v-model="subcategory.name" placeholder="Nama Subkategori">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label col-md-8">Kode</label>
                            <div class="form-control-wrap col-md-4">
                                <input type="text" class="form-control" v-model="subcategory.code" placeholder="Kode Subkategori">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button v-if="is_edit_subcategory == true" v-on:click="onCloseEditSub" type="button" class="btn btn-primary">
                            &times
                        </button>
                        <button class="btn btn-primary" type="submit" :disabled="subcategory.loading">
                            <span v-if="subcategory.loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>@{{is_edit_subcategory ? "Edit" : "Simpan" }}</span>
                        </button>
                    </div>
                </form>
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Manage Subkategori</h5>
                </div>
                <div class="modal-body">
                    <table class="datatable-init table table-striped">
                        <thead>
                            <tr class="text-center">
                                <th>Kategori</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(subcategory, index) in product_subcategories" :value="subcategory.id" class="text-center">
                                <td>@{{subcategory.product_category==null ? "" : subcategory.product_category.name}}</td>
                                <td>@{{subcategory.code}}</td>
                                <td>@{{subcategory.name}}</td>
                                <td>
                                    <div class="btn-group" aria-label="Basic example">
                                        <a href="#" @click.prevent="onEditSubcategory(index)" class="btn btn-outline-light"><em class="fas fa-pencil-alt"></em></a>
                                        <a href="#" @click.prevent="deleteRowSub(subcategory.id)" class="btn btn-outline-light"><em class="fas fa-trash-alt"></em></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
@endsection
@section('pagescript')
<script>
    Vue.directive('cleave', {
        inserted: (el, binding) => {
            el.cleave = new Cleave(el, binding.value || {})
        },
        update: (el) => {
            const event = new Event('input', {
                bubbles: true
            });
            setTimeout(function() {
                el.value = el.cleave.properties.result
                el.dispatchEvent(event)
            }, 100);
        }
    });
    let app = new Vue({
        el: '#app',
        data: {
            name: '',
            code: '{{$code}}',
            product_category_id: '',
            product_subcategory_id: '',
            weight: '',
            central_stock: '',
            retail_stock: '',
            studio_stock: '',
            bad_stock: '',
            purchase_price: '',
            agent_price: '',
            ws_price: '',
            retail_price: '',
            status: '1',
            is_changeable: '1',
            category: {
                name: '',
                code: '',
                loading: false,
            },
            category_edit_id: null,
            subcategory_edit_id: null,
            category_edit_index: null,
            subcategory_edit_index: null,
            is_edit_category: false,
            is_edit_subcategory: false,
            subcategory: {
                product_category_id: '',
                name: '',
                code: '',
                loading: false,
            },
            loading: false,
            product_categories: JSON.parse('{!! $product_categories !!}'),
            product_subcategories: JSON.parse('{!! $product_subcategories !!}'),
            prefix: '',
            infix: '',
            cleaveCurrency: {
                delimiter: '.',
                numeralDecimalMark: ',',
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            },
        },
        methods: {
            submitForm: function() {
                this.sendData();
            },
            sendData: function() {
                // console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.post('/product', {
                        name: this.name,
                        code: this.number,
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
                        subcategory: this.subcategory.product_category_id,
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
            editProductCategory: function(id, index) {
                // console.log('submitted');
                let vm = this;
                vm.category.loading = true;
                axios.patch('/product-category/' + id, {
                        name: this.category.name,
                        code: this.category.code,
                    })
                    .then(function(response) {
                        vm.category.loading = false;
                        console.log(response);
                        const {
                            data
                        } = response.data
                        vm.product_categories[index].name = data.name;
                        vm.product_categories[index].code = data.code;
                        vm.onChangeCategory();
                        // vm.product_categories.push(response.data.data);
                        // vm.product_category_id = response.data.data.id
                        // vm.onChangeCategory();
                        // $('#categoryModal').modal('hide')
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
            editProductSubcategory: function(id, index) {
                let vm = this;
                vm.subcategory.loading = true;
                axios.patch('/product-subcategory/' + id, {
                        subcategory: this.subcategory.product_category_id,
                        name: this.subcategory.name,
                        code: this.subcategory.code,
                    })
                    .then(function(response) {
                        vm.subcategory.loading = false;
                        console.log(response);
                        const {
                            data
                        } = response.data
                        vm.product_subcategories[index].product_category_id = data.product_category_id;
                        vm.product_subcategories[index].name = data.name;
                        vm.product_subcategories[index].code = data.code;
                        vm.onChangeSubcategory();
                        // vm.product_categories.push(response.data.data);
                        // vm.product_category_id = response.data.data.id
                        // vm.onChangeCategory();
                        // $('#categoryModal').modal('hide')
                    })
                    .catch(function(error) {
                        vm.subcategory.loading = false;
                        console.log(error);
                        Swal.fire(
                            'Oops!',
                            'Something wrong',
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
            },
            deleteRow: function(id) {
                let vm = this;
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
                        return axios.delete('/product-category/' + id)
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
                    vm.product_categories = vm.product_categories.filter(category => category.id !== id)
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Data has been deleted',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // window.location.reload();
                                // invoicesTable.ajax.reload();
                            }
                        })
                    }
                });
            },
            deleteRowSub: function(id) {
                let vm = this;
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
                        return axios.delete('/product-subcategory/' + id)
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
                    vm.product_subcategories = vm.product_subcategories.filter(subcategory => subcategory.id !== id)
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Data has been deleted',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // window.location.reload();
                                // invoicesTable.ajax.reload();
                            }
                        })
                    }
                });
            },
            onEditCategory: function(index) {
                const category = this.product_categories[index];
                this.category.name = category.name;
                this.category.code = category.code;
                this.category_edit_id = category.id;
                this.category_edit_index = index;
                this.is_edit_category = true;
            },
            onEditSubcategory: function(index) {
                const subcategory = this.product_subcategories[index];
                this.subcategory.product_category_id = subcategory.product_category_id;
                this.subcategory.name = subcategory.name;
                this.subcategory.code = subcategory.code;
                this.subcategory_edit_id = subcategory.id;
                this.subcategory_edit_index = index;
                this.is_edit_subcategory = true;
            },
            onCloseEdit: function() {
                this.is_edit_category = false;
                this.category.name = "";
                this.category.code = "";
            },
            onCloseEditSub: function() {
                this.is_edit_subcategory = false;
                this.subcategory.product_category_id = "";
                this.subcategory.name = "";
                this.subcategory.code = "";
            },
        },
        computed: {
            number: function() {
                return this.prefix + this.infix + this.code
            },
            subCategoryOptions: function() {
                if (!this.product_category_id) {
                    return [];
                }
                return this.product_subcategories.filter(subcategory => subcategory.product_category_id == this.product_category_id);
            },
        }
    })
</script>
@endsection