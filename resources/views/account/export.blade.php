<table>
    <thead>
        <tr>
            <td colspan="6">
                <h2><strong>Akun {{$name }} {{{$number}}}</strong></h2>
            </td>

        </tr>

        <tr>
            <td colspan="6"></td>
        </tr>
        
        <tr>
            <th><b>Tanggal</b></th>
            <th><b>Deskripsi</b></th>
            <th><b>Catatan</b></th>
            <th><b>In</b></th>
            <th><b>Out</b></th>
            <th><b>Saldo</b></th>
            
        </tr>


    </thead>
    <tbody>
    
        @php $balance=0; @endphp
        @foreach($transactions as $transaction)
        @php $transaction->type=="in"?$balance+=$transaction->amount:$balance-=$transaction->amount   @endphp
        <tr>
            <td data-format="#,##0_-" style="width:10%">{{$transaction->date}}</td>
            <td style="width:25%" >{{ $transaction->description }}</td>
            <td style="width:25%">{{ $transaction->note}}</td>
            <td  style="width:15%">
                {{
                    $transaction->type=="in"
                    ?$transaction->amount
                    :""
                }}
            </td>
            <td>
            {{
                    $transaction->type=="out"
                    ?$transaction->amount
                    :""
            }}
        </td>
            <td style="width:15%">{{$balance}}</td>
        </tr>
        @endforeach
        
        
    </tbody>
</table>