@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="nk-block-head ">
    <div class="nk-block-head-content">
        <div class="card card-bordered">
            <div class="card-inner overflow-hidden">
                <form @submit.prevent="is_edit_accountTransaction ? editAccountTransaction(accountTransactions_edit_id, accountTransactions_edit_index):submitForm()">
                <!-- <form @submit.prevent="submitForm()"> -->
                    <div class="row">
                        <div class=" form-group col-md-4">
                            <label class="form-label" for="full-name-1">Nomor</label>
                            <div class="form-control-wrap">
                                <input type="text" v-model="number" class="form-control" placeholder="Nomor" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label" for="full-name-1">Akun Masuk</label>
                            <div class="form-control-wrap">
                                <select v-model="account_in" class="form-control" id="account_in">
                                    <option v-for="account in accounts" :value="account.id">@{{account.name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class=" form-group col-md-4">
                            <label class="form-label" for="full-name-1">Akun Keluar</label>
                            <div class="form-control-wrap">
                                <select v-model="account_out" class="form-control" id="account_out">
                                    <option v-for="account in accounts" :value="account.id">@{{account.name}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="form-label" for="full-name-1">Tanggal Transaksi</label>
                            <div class="form-control-wrap">
                                <input type="date" v-model="date" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label" for="full-name-1">Nominal</label>
                            <div class="form-control-wrap">
                                <input type="text" v-model="amount" class="form-control text-right" class="form-control"   placeholder="0.00">
                            </div>
                        </div>
                        <div class=" form-group col-md-4">
                            <label class="form-label" for="full-name-1">Catatan</label>
                            <div class="form-control-wrap">
                                <input type="text" v-model="note" class="form-control" placeholder="Catatan">
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group col-md-6">
                        <label class="form-label" for="full-name-1">Jenis Pembayaran</label>
                        <div class="form-control-wrap">
                            <select v-model="type" class="form-control">
                                <option value="Cash">Cash</option>
                                <option value="Hutang">Hutang</option>
                                <option value="None">None</option>
                            </select>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="form-group col-md-12">
                            <div class="text-right">
                                <button v-if="is_edit_accountTransaction == true" v-on:click="onCloseEdit" type="button" class="btn btn-primary">
                                    &times;
                                </button>
                                <button class="btn btn-primary" type="submit" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span>@{{is_edit_accountTransaction ? "Edit" : "Simpan" }}</span>
                                </button>
                            </div>
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
                <table class="table table-striped" id="inOutTransactions">
                    <thead>
                        <tr class="text-center">
                            <th>Nomor</th>
                            <th>Tanggal Transaksi</th>
                            <th>Akun In</th>
                            <th>Akun Out</th>
                            <th>Nominal</th>
                            <th>Catatan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(accountTransaction, index) in accountTransactions" class="text-center">
                            <td>@{{accountTransaction[0]['number']}}</td>
                            <td>@{{accountTransaction[0]['date']}}</td>
                            <td>
                                @{{accountTransaction[0]['account']!=null?
                                    (
                                        accountTransaction[0]['account']['type']=="out"
                                        ?accountTransaction[0]['account']['name']
                                        :accountTransaction[1]['account']['name']
                                    )
                                :""
                                }}
                            </td>
                            <td>
                                 @{{accountTransaction[1]['account']!=null?
                                    (
                                        accountTransaction[1]['account']['type']=="in"
                                        ?accountTransaction[1]['account']['name']
                                        :accountTransaction[0]['account']['name']
                                    )
                                :""
                                    }}
                            </td>
                            <td>@{{accountTransaction[0]['amount']}}</td>
                            <td>@{{accountTransaction[0]['note']}}</td>
                            <td>
                                <div class="btn-group" aria-label="Basic example">
                                    <a href="#" @click.prevent="onEditAccountTransaction(index)" class="btn btn-outline-light"><em class="fas fa-pencil-alt"></em></a>
                                    <a href="#" @click.prevent="deleteAccountTransaction(accountTransaction[0].id,accountTransaction[1].id)" class="btn btn-outline-light"><em class="fas fa-trash-alt"></em></a>
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
        $(function() {
          
            $("#account_in").select2({})
            $("#account_out").select2({})
        })
    </script>
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
            number: '{{$number}}',
            account_in: '',
            account_out: '',
            date: '',
            amount: '',
            note: '',
            accountTransactions_edit_id: null,
            accountTransactions_edit_index: null,
            is_edit_accountTransaction: false,
            accounts: JSON.parse('{!! $account !!}'),
            accountTransactions: JSON.parse('{!! $accountTransaction !!}'),
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
                console.log(this.account_in);
            },
            sendData: function() {
                // console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.post('/account-transaction', {
                        number: this.number,
                        account_in: this.account_in,
                        account_out: this.account_out,
                        date: this.date,
                        amount: this.amount,
                        note: this.note,
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
                                window.location.href = '/account-transaction';
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
            editAccountTransaction: function(id, index) {
                    let vm = this;
                    console.log('w',id)
                    //vm.loading = true;
                    console.log(vm.accountTransactions[index][0].type);
                    
                    axios.patch('/account-transaction/' + id, {
                            number: this.number,
                            account_in: this.account_in,
                            account_out: this.account_out,
                            date: this.date,
                            amount: this.amount,
                            note: this.note,
                            out_id:
                            vm.accountTransactions[index][0].type=="out"
                            ?vm.accountTransactions[index][0].id
                            :vm.accountTransactions[index][1].id,
                            in_id:
                            vm.accountTransactions[index][1].type=="in"
                            ?vm.accountTransactions[index][1].id
                            :vm.accountTransactions[index][0].id
                           
                        })
                        .then(function(response) {
                            vm.loading = false;
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
            onEditAccountTransaction: function(index) {
                const accountTransaction = this.accountTransactions[index];
                
                
                this.number = accountTransaction[0].number;
                this.account_out = 
                    accountTransaction[0].type=="out"
                    ? accountTransaction[0].account_id
                    :accountTransaction[1].account_id;

                this.account_in = 
                    accountTransaction[1].type=="in"
                    ? accountTransaction[1].account_id
                    :accountTransaction[0].account_id;

                this.date = accountTransaction[0].date;
                this.amount = accountTransaction[0].amount;
                this.note = accountTransaction[0].note;
                this.accountTransactions_edit_id = accountTransaction.id;
                this.accountTransactions_edit_index = index;
                this.is_edit_accountTransaction = true;
                //console.log(account);
                setAccount(accountTransaction[1].type=="in"
                    ? accountTransaction[1].account_id
                    :accountTransaction[0].account_id,this.account_out)
            },
            onCloseEdit: function() {
                this.is_edit_accountTransaction = false;
                this.number = "{{$number}}";
                this.account_in = "";
                this.account_out = "";
                this.date = "";
                this.amount = "";
                this.note = "";
            },
            deleteAccountTransaction: function(in_id,out_id) {
                // let vm = this;
                console.log(in_id,out_id)
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
                        return axios.delete(`/account-transaction/`+in_id+'/'+out_id)
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
                    // vm.accountTransactions = vm.accountTransactions.filter(accountTransactions => accountTransactions.id !== id)
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
            },
        }
    })
</script>

<script>
  NioApp.DataTable.init = function() {
            NioApp.DataTable('#inOutTransactions', {
         
        });
    
}

$('#account_in').on('change',function(){
 
    app.$data.account_in=$(this).val()

});
$('#account_out').on('change',function(){
    app.$data.account_out=$(this).val()

});

// function setAccount(account_in,account_out){
//     console.log("tes")
//     app.$data.account_out=account_out;
//     app.$data.account_in=account_in;
// }

</script>
@endsection