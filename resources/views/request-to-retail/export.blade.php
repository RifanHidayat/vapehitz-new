<table>
    <thead>
       
        
        <tr>
            <th><b>No. Permintan</b></th>
            <th><b>Tanggal</b></th>
            <th><b>Nama Produk</b></th>
            <th><b>QTY</b></th>
            
            
        </tr>


    </thead>
    <tbody>
    
 
        @foreach($req as $r)
       
        <tr>
            <td  style="width: 20px;">{{$r->code}}</td>
            <td  style="width: 20px;">{{$r->date}}</td>
            <td></td>
            <td></td>
</tr>
            
        

         @foreach($r->products as $product)
        <tr>
            <td></td>
            <td></td>
            <td style="width: 50px;">{{$product->name}}</td>
            <td  style="width: 10px;">{{$product->pivot->quantity}}</td>
        
</tr>
            
        
        @endforeach
    
        
        @endforeach
        
        
    </tbody>
</table>