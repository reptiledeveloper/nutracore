@php
    $products = [];
//    $product_id = $type_id??'';

    $product_ids = $selected_data->product_ids??'';
    $product_ids = explode(",",$product_ids);
    if(!empty($product_ids)){
        $products = \App\Models\Products::whereIn('id',$product_ids)->get();
    }
    if(!empty($product_id)){
        $products = \App\Models\Products::where('id',$product_id)->get();
    }

@endphp
<div class="form-group col-md-6 mt-3" id="product_show">
    <label for="inputEmail4" class="form-label">Choose Product</label>
    @if(!empty($multiple))
        <select class="form-control select2product" name="product_ids[]" multiple>
            @foreach($products as $product)
                <option
                        value="{{$product->id ??''}}" {{in_array($product->id,$product_ids)?"selected":""}}>{{$product->name??''}}</option>
            @endforeach
        </select>
    @else
        <select class="form-control select2product" name="product_id">
            <option value="" selected>Select Product</option>
            @foreach($products as $product)
                <option
                        value="{{$product->id ??''}}" {{$product->id == $product_id?"selected":""}}>{{$product->name??''}}</option>
            @endforeach
        </select>
    @endif


    @include('snippets.errors_first', ['param' => 'banner_name'])
</div>
