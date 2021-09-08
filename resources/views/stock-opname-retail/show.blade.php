@extends('layouts.app')

@section('title', 'Vapehitz')
@section('pagestyle')

@endsection
@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub">
                <a class="back-to" href="/retail-stock-opname"><em class="icon ni ni-arrow-left"></em>
                    <span>Data Stok Opname Retail</span>
                </a>
            </div>
            <h3 class="nk-block-title fw-normal">Detail Stok Opname Retail</h3>
        </div>
    </div>
    <div class="card card-bordered">
        <div class="card-inner-group">
            <div class="card-inner card-inner-md">
                <div class="card-title-group">
                    <div class="card-title">
                        <h6 class="title">Informasi Stok Opname</h6>
                    </div>
                </div>
            </div>
            <div class="card-inner">
                <div class="row">
                    <div class="col-sm">
                        <div class=" form-group col-md-12">
                            <label class="form-label" for="full-name-1">Nomor Stok Opname</label>
                            <div class="form-control-wrap">
                                {{$stockOpnameRetail->code}}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class=" form-group col-md-12">
                            <label class="form-label" for="full-name-1">Tanggal Stok Opname</label>
                            <div class="form-control-wrap">
                                {{$stockOpnameRetail->date}}
                            </div>
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="form-group col-md-6">
                    <label class="form-label" for="full-name-1">Keterangan</label>
                    <div class="form-control-wrap">
                        {{$stockOpnameRetail->note}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<p></p>
<div class="card card-bordered h-100">
    <div class="card-inner-group">
        <div class="card-inner card-inner-md">
            <div class="card-title-group">
                <div class="card-title">
                    <h6 class="title">Daftar Produk</h6>
                </div>
                <div class="card-tools mr-n1">

                </div>
            </div>
        </div>
        <div class="card-inner">
            <div v-if="selectedProducts.length === 0" class="text-center text-soft">
                <p class="mt-3">Belum ada barang yang dipilih</p>
            </div>
            <div v-else class="card">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="text-center">
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Stok Retail</th>
                                        <th>Real Stock</th>
                                        <th>Selisih</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(product, index) in selectedProducts" :key="index" class="text-center">
                                        <td>@{{product.code}}</td>
                                        <td>@{{product.name}}</td>
                                        <td>@{{product.retail_stock}}</td>
                                        <td>
                                            @{{product.good_stock}}
                                        </td>
                                        <td>@{{totalDifference(product)}}</td>
                                        <td>@{{product.description}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
        data: {
            code: '{{$stockOpnameRetail->code}}',
            date: '{{$stockOpnameRetail->date}}',
            note: '{{$stockOpnameRetail->note}}',
            selectedProducts: JSON.parse('{!! $stockOpnameRetail->products !!}'),
            check: [],
        },
        methods: {
            onSelectedProduct: function() {
                const selectedProductIds = this.selectedProducts.map(product => product.id);
                const productsInCheck = this.check.filter(product => selectedProductIds.indexOf(product.id) < 0);
                this.check.filter(product => selectedProductIds.indexOf(product.id) > -1)
                    .map(product => product.id)
                    .forEach(productId => {
                        const index = selectedProductIds.findIndex((id) => id == productId);
                        this.selectedProducts[index].quantity += 1;
                    })
                // console.log(arr);

                this.selectedProducts = this.selectedProducts.concat(productsInCheck);
                this.check = [];
            },
            removeSelectedProduct: function(index) {
                this.selectedProducts.splice(index, 1);
            },
            removeAllSelectedProducts: function() {
                this.selectedProducts = [];
            },
            removeFromCheck: function(index) {
                this.check.splice(index, 1);
            },
            totalDifference: function(product) {
                return Number(product.retail_stock) - Number(product.good_stock);
            },
            submitForm: function() {
                this.sendData();
            },
            sendData: function() {
                // console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.patch('/retail-stock-opname/{{$stockOpnameRetail->id}}', {
                        code: vm.code,
                        date: vm.date,
                        note: vm.note,
                        selected_products: vm.selectedProducts,
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
                                window.location.href = '/retail-stock-opname';
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
        },
    })
</script>
<script>
    $(function() {
        const productTable = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            "autoWidth": false,
            ajax: {
                url: '/datatables/stock-opname-retail/products',
                type: 'GET',
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
                    data: 'action',
                    name: 'action'
                },
            ]
        });

        $('#products-table tbody').on('click', '.btn-choose', function() {
            const rowData = productTable.row($(this).parents('tr')).data();
            const data = {
                ...rowData
            };
            const check = app.$data.check;
            const productIds = check.map(product => product.id);

            if (productIds.indexOf(data.id) < 0) {
                data['good_stock'] = 0;
                check.push(data);
            }
        });
    });
</script>
@endsection