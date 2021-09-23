@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title page-title">Edit Group</h4>
            </div>
            <div class="nk-block-head-content">
                <a href="{{url('/group')}}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
            </div>
        </div>
    </div>
    <div class="card card-bordered">
        <div class="card-inner">
            <form @submit.prevent="submitForm">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label" for="full-name-1">Nama Group</label>
                            <div class="form-control-wrap">
                                <input type="text" v-model="name" class="form-control" placeholder="Masukan nama group">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-label" for="full-name-1">Permission</label>
                            <div class="table table-responsive">
                                <div class="form-control-wrap">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>All</th>
                                                <th>View</th>
                                                <th>Tambah</th>
                                                <th>Edit</th>
                                                <th>Hapus</th>
                                                <th>Laporan</th>
                                                <th>Approval</th>
                                            </tr>
                                        </thead>
                                        <tbody v-for="(parent, i) in permissions">
                                            <tr>
                                                <th class="">@{{parent.title}}</th>
                                                <td colspan="7"></td>
                                            </tr>
                                            <tr v-for="(permission, j) in parent.attributes">
                                                <td class="">@{{permission.subtitle}}</td>
                                                <td>
                                                    <div class="custom-control custom-control-sm custom-checkbox">
                                                        <input type="checkbox" @change="toggleCheckAllSection($event, permission)" class="custom-control-input" :id="'checkAll'+i+j" />
                                                        <label :for="'checkAll'+i+j" class="custom-control-label"></label>
                                                    </div>
                                                </td>
                                                <td v-for="(attribute, k) in permission.attributes" class="text-center">
                                                    <div v-if="attribute !== null" class="custom-control custom-control-sm custom-checkbox">
                                                        <input type="checkbox" v-model="checkedPermissions" :value="attribute" class="custom-control-input" :id="'permission'+i+j+k">
                                                        <label class="custom-control-label" :for="'permission'+i+j+k"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-inner col-lg-12">
                        <div class="col-lg-12 text-right">
                            <button type="submit" class="btn btn-primary" :class="loading && 'spinner spinner-white spinner-right'" :disabled="loading">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        data: {
            name: '{{$group->name}}',
            permissions: [{
                    title: 'MASTER DATA',
                    attributes: [{
                            subtitle: 'Master Data Supplier',
                            attributes: ['view_supplier', 'add_supplier', 'edit_supplier', 'delete_supplier', 'print_supplier', null, null],
                        },
                        {
                            subtitle: 'Master Data Customer',
                            attributes: ['view_customer', 'add_customer', 'edit_customer', 'delete_customer', 'print_customer', null],
                        },
                        {
                            subtitle: 'Master Data Produk',
                            attributes: ['view_product', 'add_product', 'edit_product', 'delete_product', 'print_product', null],
                        },
                    ]
                },
                {
                    title: 'TRANSAKSI PUSAT',
                    attributes: [{
                            subtitle: 'Pembelian Barang',
                            attributes: ['view_purchase_product', 'add_purchase_product', 'edit_purchase_product', 'delete_purchase_product', 'print_purchase_product', null],
                        },
                        {
                            subtitle: 'Pembayaran Supplier',
                            attributes: ['view_payment_supplier', 'add_payment_supplier', null, null, 'print_payment_supplier', null],
                        },
                        {
                            subtitle: 'Retur Barang Pembelian',
                            attributes: ['view_return_product_purchase', 'add_return_product_purchase', null, null, 'print_return_product_purchase', null],
                        },
                        {
                            subtitle: 'Penyelesaian Retur Pembelian',
                            attributes: ['view_product_payment', 'add_product_payment', null, null, 'print_product_payment', null],
                        },
                        {
                            subtitle: 'Penjualan Barang',
                            attributes: ['view_product_sell', 'add_product_sell', 'edit_product_sell', 'delete_product_sell', 'print_product_sell', 'approval_product_sell'],
                        },
                        {
                            subtitle: 'Pembayaran Pelanggan',
                            attributes: ['view_customer_payment', 'add_customer_payment', null, null, 'print_customer_payment', null],
                        },
                        {
                            subtitle: 'Retur Barang Penjualan',
                            attributes: ['view_return_product_sell', 'add_return_product_sell', null, null, 'print_return_product_sell', null],
                        },
                        {
                            subtitle: 'Penyelesaian Retur Penjualan',
                            attributes: ['view_sell_return_settlement', 'add_sell_return_settlement', 'edit_sell_return_settlement', null, 'print_sell_return_settlement', null],
                        },
                        {
                            subtitle: 'Stock Opname',
                            attributes: ['view_stock_opname', 'add_stock_opname', 'edit_stock_opname', null, 'print_stock_opname', null],
                        },
                        {
                            subtitle: 'Pengeluaran Bad Stock',
                            attributes: ['view_badstock_release', 'add_badstock_release', 'edit_badstock_release', 'delete_badstock_release', 'print_badstock_release', 'approval_badstock_release'],
                        },
                        {
                            subtitle: 'Permintaan ke G.Retail',
                            attributes: ['view_request_to_retail', 'add_request_to_retail', 'edit_request_to_retail', 'delete_request_to_retail', 'print_request_to_retail', null],
                        },
                        {
                            subtitle: 'Permintaan ke G.Studio',
                            attributes: ['view_request_to_studio', 'add_request_to_studio', 'edit_request_to_studio', 'delete_request_to_studio', 'print_request_to_studio', null],
                        },
                        {
                            subtitle: 'Approval Permintaan',
                            attributes: ['view_confirm_request', null, null, null, 'print_confirm_request', 'approval_confirm_request'],
                        },
                    ]
                },
                {
                    title: 'TRANSAKSI RETAIL',
                    attributes: [{
                            subtitle: 'Permintaan G.Pusat',
                            attributes: ['view_request_to_central_retail', 'add_request_to_central_retail', 'edit_request_to_central_retail', 'delete_request_to_central_retail', 'print_request_to_central_retail', null]
                        },
                        {
                            subtitle: 'Penjualan Barang',
                            attributes: ['view_retail_sell', 'add_retail_sell', null, null, 'print_retail_sell', 'approval_retail_sell']
                        },
                        {
                            subtitle: 'Retur Barang Penjualan',
                            attributes: ['view_return_retail_sell', 'add_return_retail_sell', null, null, 'print_return_retail_sell', null]
                        },
                        {
                            subtitle: 'Stock Opname',
                            attributes: ['view_sop_retail', 'add_sop_retail', 'edit_sop_retail', null, 'print_sop_retail', 'approval_sop_retail']
                        },
                        {
                            subtitle: 'Approval Permintaan',
                            attributes: ['view_confirm_request_retail', null, null, null, 'print_confirm_request_retail', 'approval_confirm_request_retail'],
                        },
                    ],
                },
                {
                    title: 'TRANSAKSI STUDIO',
                    attributes: [{
                            subtitle: 'Permintaan G.Pusat',
                            attributes: ['view_request_to_central_studio', 'add_request_to_central_studio', 'edit_request_to_central_studio', 'delete_request_to_central_studio', 'print_request_to_central_studio', null]
                        },
                        {
                            subtitle: 'Penjualan Barang',
                            attributes: ['view_studio_sell', 'add_studio_sell', null, null, 'print_studio_sell', 'approval_studio_sell']
                        },
                        {
                            subtitle: 'Retur Barang Penjualan',
                            attributes: ['view_return_studio_sell', 'add_return_studio_sell', null, null, 'print_return_studio_sell', null]
                        },
                        {
                            subtitle: 'Stock Opname',
                            attributes: ['view_sop_studio', 'add_sop_studio', 'edit_sop_studio', null, 'print_sop_studio', 'approval_sop_studio']
                        },
                        {
                            subtitle: 'Approval Permintaan',
                            attributes: ['view_studio_confirm_request', null, null, null, 'print_studio_confirm_request', 'approval_studio_confirm_request'],
                        },
                    ],
                },
                {
                    title: 'ADMINISTRATOR',
                    attributes: [{
                            subtitle: 'Data Group',
                            attributes: ['view_data_group', 'add_data_group', 'edit_data_group', 'delete_data_group', null, null]
                        },
                        {
                            subtitle: 'Data Users',
                            attributes: ['view_data_user', 'add_data_user', 'edit_data_user', 'delete_data_user', null, null]
                        },
                        {
                            subtitle: 'Ganti Password',
                            attributes: ['view_password_change', null, 'edit_password_change', null, null, null]
                        },
                    ],
                },
                {
                    title: 'FINANCE',
                    attributes: [{
                            subtitle: 'Akun',
                            attributes: ['view_account_finance', 'add_account_finance', 'edit_account_finance', 'delete_account_finance', null, null]
                        },
                        {
                            subtitle: 'Cash In Cash Out',
                            attributes: ['view_cash_in_out_finance', 'add_cash_in_out_finance', 'edit_cash_in_out_finance', 'delete_cash_in_out_finance', null, null]
                        },
                    ],
                },
            ],
            checkedPermissions: JSON.parse('{!! $group->permission !!}'),
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
                axios.patch('/group/{{$group->id}}', {
                        name: this.name,
                        permission: this.checkedPermissions,
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
                                window.location.href = '/group';
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
            select: function() {
                this.selectAll = !this.selectAll;
                this.checkedPermissions = [];
                if (this.selectAll) {
                    for (var i in this.permissions.attributes.attributes) {
                        this.checkedPermissions.push(this.permission.attributes.attributes[i]);
                    }
                }
            },
            updateCheckall: function() {
                if (this.checkedPermissions.length == this.permission.length) {
                    this.selectAll = true;
                } else {
                    this.selectAll = false;
                }
            },
            toggleCheckAllSection: function(e, subAttribute) {
                // console.log(e);
                const isChecked = e.target.checked;
                // If Unchecked
                if (!isChecked) {
                    let unchecked = this.checkedPermissions.filter(checked => {
                        const included = subAttribute.attributes.includes(checked);
                        return !included;
                    });
                    // console.log(unchecked);
                    this.checkedPermissions = unchecked;
                } else { // If Checked
                    // let unchecked = this.checkedPermissions;
                    this.checkedPermissions = this.checkedPermissions.filter(checked => {
                        const included = subAttribute.attributes.includes(checked);
                        return !included;
                    }).concat(subAttribute.attributes.filter(attribute => attribute !== null));
                }
            },
        }
    })
</script>
@endsection