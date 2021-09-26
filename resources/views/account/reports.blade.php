<style type="text/css">
    p.ratakanan {
      text-align: right;
      color: red;
      size: 40px;
      text-align-last: right;
    }


    #table-account {
      font-family: Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }

    p {
      font-size: 10px;
    }

    h8 {
      font-size: 14;
    }



    
    #table-account td,
    #table-account th {
      border: 1px solid #808080;
      padding: 8px;
    }

    

    table tr th {
      font-size: 10px
    }

    span {
      font-size: 13px;
    }
    #table-account th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #2980b9;

      color: black;
    }

    #table-account thead {
      background-color: #808080;


    }

    #table-account thead th {
      color: white;

    }

    table tr td {
      font-size: 13px;
    }

    table tr th {
      font-size: 13px;
    }
  </style>
<div class="body" style="width: 100%;" >
    <div class="header" style=" width:100%;">
        <div class="header-left" style="float:left;width:100%"  >
            <!-- <table style="width:20%;" style="font-family: Arial, Helvetica, sans-serif" >
                    <thead>
                        <tr >
                            <th align="left">Nomor Akun </th>
                            <td align="left">:</td>
                            <th align="left">{{$account->number}}</th>
                        <tr>
                    </thead>
                    <tbodt>
                    
                    <tr style="text-align:left;" >
                        <th align="left">Nama Akun</th>
                        <td align="left">:</td>
                        <th align="left">{{$account->name}}</th>
                    </tr>
                    </tbody>
            </table> -->

            <span style="font-family: Arial, Helvetica, sans-serif;font-size: 18px"><b>Akun {{$account->name}} ({{$account->number}})</b></span>
         
        </div>   
        

        <div class="header-right" style="float:right">
       

        </div>  
    </div>
    
    <div class="content" style="width: 100%; float:left;margin-top:15px;" >
    <div class="table-balance"style="width: 100%; float:left" >
       <table border="1" align="right" style="border-collapse: collapse;padding-top: 5px;padding-bottom: 10px;padding-left: 10px;width:50%;overflow: visible|hidden|wrap" id="table-account">
                    <thead>
                        <tr >
                            <th align="right" style="width: 30%;">Total In</th>
                            <th align="right" style="width: 30%;">Total Out</th>
                            <th align="right" style="width: 30%;">Balance</th>
                        <tr>
                    </thead>
                    <tbody>
                        <tr style="text-align:left;" >
                        <td align="right">{{number_format($inTotal)}}</td>
                        <td align="right">{{number_format($outTotal)}}</td>
                        <td align="right">{{number_format($inTotal-$outTotal)}}</td>
                    </tr>
                   
                    </tbody>
        </table>

        <br>
        <div class="col-13 table-responsive justify-content">
        <table border="1" align="left" style="width: 100%;" align="right" style="border-collapse: collapse;overflow: visible|hidden|wrap" id="table-account">
                    <thead border="1">
                        <tr >
                            <th align="left" style="width:10%">Tanggal</th>
                            <th align="left" style="width: 20%;">Deskripsi</th>
                            <th align="left" style="width: 18%;" >Catatan</th>
                            <th align="right" style="width:13%">In</th>
                            <th align="right" style="width: 13%;">Out</th>
                            <th align="right" style="width: 13%;" >Balance</th>
                        <tr>
                    </thead>
                    <tbody border="0">
                    @php $balance = 0; @endphp
                    @foreach($transactions as $transaction)
                    @php $transaction->account_type=="in"?$balance+=$transaction->amount:$balance-=$transaction->amount   @endphp
                    <tr>
                        <td  valign="top">{{ date_format(date_create($transaction->date), "d/m/Y") }}</td>
                        <td valign="top">{{ $transaction->description}}</td>
                        <td valign="top">{{ $transaction->note}}</td>
                        <td align="right" valign="top">{{ $transaction->account_type=="in"?$transaction->amount:"" }}</td>
                        <td align="right" valign="top">{{ $transaction->account_type=="out"?$transaction->amount:"" }}</td>
                        <td align="right" valign="top">{{ $balance}}</td>
                    </tr>
                 
                
                   </tbody>
                    
                 
                   
                    </tbody>
        </table>
        </div>

    </div>
  
</div>
