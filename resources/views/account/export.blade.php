<table>
    <thead>
        <tr>
            <td colspan="6">
                <strong>Akun {{$name }} {{{$number}}}</strong>
            </td>

        </tr>
        <tr>
            <th>Tanggal</th>
            <th>Deskripsi</th>
            <th>Catatan</th>
            <th>In</th>
            <th>Out</th>
            <th>Saldo</th>
            
        </tr>
    </thead>
    <tbody>
    
        @php $balance=0; @endphp
        @foreach($transactions as $transaction)
        @php $transaction->account_type=="in"?$balance+=$transaction->amount:$balance-=$transaction->amount   @endphp
        <tr>
            <td data-format="#,##0_-" style="width:10%">{{$transaction->date}}</td>
            <td style="width:25%" >{{ $transaction->description }}</td>
            <td style="width:25%">{{ $transaction->note}}</td>
            <td  style="width:15%">
                {{
                    $transaction->account_type=="in"
                    ?$transaction->amount
                    :""
                }}
            </td>
            <td>
            {{
                    $transaction->account_type=="out"
                    ?$transaction->amount
                    :""
            }}
        </td>
            <td style="width:15%">{{$balance}}</td>
        </tr>
        @endforeach
        
        
    </tbody>
</table>