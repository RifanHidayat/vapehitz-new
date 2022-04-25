<!-- <table>
    <thead>
      
        
        <tr>
            <th><b>Kode Product Category</b></th>
            <th><b>Nama Product Category</b></th>
            <th><b>Kode Product Subcategory</b></th>
            <th><b>Nama Proudct Subcategory</b></th>
            <th><b>Kode product</b></th>
            <th><b>Nama product</b></th>
            <th><b>berat</b></th>
            <th><b>Stok Pusat</b></th>
            <th><b>Stok Retail</b></th>
            <th><b>Stok Studio</b></th>
            <th><b>Bad Stock/b></th>
            <th><b>Booked</b></th>
            <th><b>Purchase Price</b></th>
            <th><b>Agent Price</b></th>
            <th><b>WS Price</b></th>
            <th><b>Retail Price</b></th>


            
        </tr>


    </thead>
    <tbody>
    
   
        @foreach($products as $product)
     
        <tr>
        <td  >{{$product->code}}</td>
            <td  >{{$product->name}}</td>
            <td  >{{$product->weight}}</td>
            <td  >{{$product->central_stock}}</td>
            <td  >{{$product->code}}</td>
            <td  >{{$product->name}}</td>
            <td  >{{$product->weight}}</td>
            <td  >{{$product->central_stock}}</td>
            <td  >{{$product->retail_stock}}</td>
            <td  >{{$product->studio_stock}}</td>
            <td  >{{$product->bad_stock}}</td>
            <td  >{{$product->booked}}</td>
            <td  >{{$product->puerhcase_price}}</td>
            <td  >{{$product->agent_price}}</td>
            <td  >{{$product->ws_price}}</td>
            <td  >{{$product->retail_price}}</td>

           
          
        </tr>
        @endforeach
        
        
    </tbody>
</table> -->

<table>
    <thead>
        
        
        <tr>
        <!-- <th><b>Kode Product Category</b></th>
            <th><b>Nama Product Category</b></th>
            <th><b>Kode Product Subcategory</b></th>
            <th><b>Nama Proudct Subcategory</b></th> -->
            <th><b>Id</b></th>
            <th><b>Kode product</b></th>
            <th><b>Nama product</b></th>
            <th><b>berat</b></th>
            <th><b>Stok Pusat</b></th>
            <th><b>Stok Retail</b></th>
            <th><b>Stok Studio</b></th>
            <th><b>Bad Stock</b></th>
            <th><b>Booked</b></th>
            <th><b>Purchase Price</b></th>
            <th><b>Agent price</b></th>
            <th><b>Ws price</b></th>
            <th><b>Retail price</b></th>
          
           
        
            
        </tr>


    </thead>
    <tbody>
    
   
        @foreach($products as $product)
    
        <tr>
        <!-- <td style="width:25%" >{{ $product->productCategory->code }}</td>
            <td style="width:25%" >{{ $product->productCategory->name }}</td>
            <td style="width:25%" >{{ $product->productSubCategory->code }}</td>
            <td style="width:25%" >{{ $product->productSubCategory->name }}</td> -->
             <td style="width:25%" >{{ $product->id }}</td>
            <td style="width:25%" >{{ $product->code }}</td>
            <td style="width:25%" >{{ $product->name }}</td>
        
            <td style="width:25%" >{{ $product->weight }}</td>
            <td style="width:25%" >{{ $product->central_stock }}</td>
            <td style="width:25%" >{{ $product->retail_stock }}</td>
            <td style="width:25%" >{{ $product->studio_stock }}</td>
            <td style="width:25%" >{{ $product->bad_stock }}</td>
  
            <td style="width:25%" >{{ $product->booked }}</td>
            <td style="width:25%" >{{ $product->purchase_price }}</td>
            <td style="width:25%" >{{ $product->agent_price }}</td>
            <td style="width:25%" >{{ $product->ws_price }}</td>
            <td style="width:25%" >{{ $product->retail_price }}</td>
            
           
         
        </tr>
        @endforeach
        
        
    </tbody>
</table>