@extends('layouts.app')

@section('title', 'Vapehitz')
<style>
    .dataTables_filter {
        text-align: right;
        width: 90%;
    }

    table tr th {
        font-size: 15px;
        color: black;
    }

    table tr td {
        font-size: 13px;
        color: black;
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
<div class="components-preview ">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <div class="card card-bordered">
                <div class="card-inner overflow-hidden">
                    <form @submit.prevent="is_edit_account ? editAccount(accounts_edit_id, accounts_edit_index):submitForm()">
                        <div class=" form-group col-md-6">
                            <label class="form-label" for="full-name-1">Nomor Akun</label>
                            <div class="form-control-wrap">
                                <input require type="number" v-model="number"  class="form-control number" placeholder="Nomor Akun">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="full-name-1">Nama</label>
                            <div class="form-control-wrap">
                                <input type="text" v-model="name" class="form-control name" placeholder="Nama">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="full-name-1">Tanggal Saldo Awal</label>
                            <div class="form-control-wrap">
                                <input type="date" v-model="date" class="form-control" class="form-control date">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="full-name-1">Saldo Awal</label>
                            <div class="form-control-wrap">
                                <input type="text" v-model="init_balance" class="form-control text-right init_balance" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="full-name-1">Jenis Pembayaran</label>
                            <div class="form-control-wrap">
                                <select v-model="type" class="form-control">
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
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
                <table style="width: 100%;" class="table table-striped" id="accounts">
                        <thead>
                            <tr class="text-left">
                                <th>Nomor Kartu</th>
                                <th>Nama</th>
                                <th>Saldo</th>
                                <th>Tanggal Saldo Awal</th>
                                <th>Jenis Transaksi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                     
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
            date: '',
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
                        date: this.date,
                    })
                    .then(function(response) {
                        vm.loading = false;
                         console.log(response);
                        vm.accounts.push(response.data.data);
                       
                        Swal.fire({
                                title: 'Success',
                                text: 'Data has been saved',
                                icon: 'success',
                                allowOutsideClick: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            })
                        

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
                        date: this.date,
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
                        vm.accounts[index].date = data.date;
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
                console.log("tes");
                const account = this.accounts[index];
                this.number = account.number;
                this.name = account.name;
                this.init_balance = account.init_balance;
                this.date = account.date;
                this.type = account.type;
                this.accounts_edit_id = account.id;
                this.accounts_edit_index = index;
                this.is_edit_account = true;
                console.log(account);
            },
            onCloseEdit: function() {
                this.is_edit_account = false;
                this.number = "";
                this.date = "";
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
                                 invoicesTable.ajax.reload();
                            }
                        })
                    }
                })
            },
            showAccount:function(id){
                window.location.href = '/account/show/'+id;


            },
            currencyFormat: function(number) {
                return Intl.NumberFormat('de-DE').format(number);
            },
            clearCurrencyFormat: function(number) {
                return number.replaceAll('.', number);
            }
        }
    })
</script>


<script>
    var accounts = $(function() {
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#accounts', {
                processing: true,
                serverSide: true,
                autoWidth: false,
            // dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
            ajax: {
                url: '/datatables/accounts',
                type: 'GET',
                // length: 2,
            },
            columns: [
                {
                    data: 'number',
                    name: 'number'
                },
                {
                    data: 'name',
                    name: 'name'
                },
               
                {
                    data: 'balance',
                    name: 'balance'
                },
                {
                    data: 'date',
                    name: 'date'
                },
               
             
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'action',
                    name: 'action'
                },

            ]
        });
        $.fn.DataTable.ext.pager.numbers_length = 5;

        }
        NioApp.DataTable.init();
        $('#accounts').on('click', 'tr .btn-delete', function(e) {
            e.preventDefault();
            // alert('click');
            const id = $(this).attr('data-id');
            console.log(id)
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
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Data has been deleted',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();

                        }
                    })
                }
            })
        })
        $('#accounts').on('click', 'tr .btn-edit', function(e) {
            e.preventDefault();
            // alert('click');
            const id = $(this).attr('data-id');
            console.log(id);



          
        
        })
    });
    
</script>
@endsection