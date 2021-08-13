@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-lg">
        <div class="nk-block-head-content">
            <div class="card card-bordered">
                <div class="card-inner overflow-hidden">
                    <form @submit.prevent="is_edit_account ? editAccount(accounts_edit_id, accounts_edit_index):submitForm()">
                        <div class=" form-group col-md-6">
                            <label class="form-label" for="full-name-1">Nomor Kartu</label>
                            <div class="form-control-wrap">
                                <input type="number" v-model="number" class="form-control" placeholder="Nomor Kartu">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="full-name-1">Nama</label>
                            <div class="form-control-wrap">
                                <input type="text" v-model="name" class="form-control" placeholder="Nama">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="full-name-1">Saldo Awal</label>
                            <div class="form-control-wrap">
                                <input type="text" v-model="init_balance" class="form-control text-right" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="full-name-1">Jenis Pembayaran</label>
                            <div class="form-control-wrap">
                                <select v-model="type" class="form-control">
                                    <option value="cash">Cash</option>
                                    <option value="hutang">Hutang</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="text-right">
                                <button v-if="is_edit_account == true" v-on:click="onCloseEdit" type="button" class="btn btn-primary">
                                    &times;
                                </button>
                                <button class="btn btn-primary" type="submit" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span>@{{is_edit_account ? "Edit" : "Simpan" }}</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
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
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <div class="table-responsive">
                    <table class="datatable-init table table-striped" id="table-account">
                        <thead>
                            <tr class="text-center">
                                <th>Nomor Kartu</th>
                                <th>Nama</th>
                                <th>Saldo</th>
                                <th>Jenis Transaksi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(account, index) in accounts" class="text-center">
                                <td>@{{account.number}}</td>
                                <td>@{{account.name}}</td>
                                <td>@{{account.init_balance}}</td>
                                <td>@{{account.type}}</td>
                                <td>
                                    <div class="btn-group" aria-label="Basic example">
                                        <a href="#" @click.prevent="onEditAccount(index)" class="btn btn-outline-light"><em class="fas fa-pencil-alt"></em></a>
                                        <a href="#" @click.prevent="deleteAccount(account.id)" class="btn btn-outline-light"><em class="fas fa-trash-alt"></em></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- .nk-block -->
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
            number: '',
            type: '',
            init_balance: '',
            accounts_edit_id: null,
            accounts_edit_index: null,
            is_edit_account: false,
            accounts: JSON.parse('{!! $accounts !!}'),
            loading: false,
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
                axios.post('/account', {
                        name: this.name,
                        number: this.number,
                        type: this.type,
                        init_balance: this.init_balance,
                    })
                    .then(function(response) {
                        vm.loading = false;
                        console.log(response);
                        vm.accounts.push(response.data.data);
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
            editAccount: function(id, index) {
                let vm = this;
                vm.loading = true;
                axios.patch('/account/' + id, {
                        number: this.number,
                        name: this.name,
                        init_balance: this.init_balance,
                        type: this.type,
                    })
                    .then(function(response) {
                        vm.loading = false;
                        console.log(response);
                        const {
                            data
                        } = response.data
                        vm.accounts[index].number = data.number;
                        vm.accounts[index].name = data.name;
                        vm.accounts[index].init_balance = data.init_balance;
                        vm.accounts[index].type = data.type;
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
            onEditAccount: function(index) {
                const account = this.accounts[index];
                this.number = account.number;
                this.name = account.name;
                this.init_balance = account.init_balance;
                this.type = account.type;
                this.accounts_edit_id = account.id;
                this.accounts_edit_index = index;
                this.is_edit_account = true;
                console.log(account);
            },
            onCloseEdit: function() {
                this.is_edit_account = false;
                this.number = "";
                this.name = "";
                this.init_balance = "";
                this.type = "";
            },
            deleteAccount: function(id) {
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
                        return axios.delete('/account/' + id)
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
                    vm.accounts = vm.accounts.filter(accounts => accounts.id !== id)
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
                })
            },
        }
    })
</script>
@endsection