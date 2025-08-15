@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $product_id = $product->id ?? '';
    $name = $product->name ?? '';
    $vendor_id = $product->vendor_id ?? '';
    $short_description = $product->short_description ?? '';
    $tags = $product->tags ?? '';
    $type = $product->type ?? '';
    $status = $product->status ?? 1;
    $category_id = $product->category_id ?? '';
    $subcategory_id = $product->subcategory_id ?? '';
    $long_description = $product->long_description ?? '';
    $manufacter_id = $product->manufacter_id ?? '';
    $brand_id = $product->brand_id ?? '';
    $meta_title = $product->meta_title ?? '';
    $meta_description = $product->meta_description ?? '';
    $product_mrp = $product->product_mrp ?? '';
    $product_selling_price = $product->product_selling_price ?? '';
    $product_subscription_price = $product->product_subscription_price ?? '';


    $tax = $product->tax ?? '';
    $max_qty = $product->max_qty ?? '';
    $min_qty = $product->min_qty ?? '';
    $is_tax_included = $product->is_tax_included ?? 0;
    $is_returnable = $product->is_returnable ?? '';
    $type = $product->type ?? '';
    $video_type = $product->video_type ?? '';
    $video = $product->video ?? '';
    $sku = $product->sku ?? '';
    $hsn = $product->hsn ?? '';





    $brands = \App\Helpers\CustomHelper::getBrands();
    $manufacturer = \App\Helpers\CustomHelper::getManufacturer();
    $categories = \App\Helpers\CustomHelper::getCategories();
    $image = \App\Helpers\CustomHelper::getImageUrl('products', $product->image ?? '');
    $vendors = \App\Helpers\CustomHelper::getVendors();
    $attributes = \App\Helpers\CustomHelper::getAttributes();
    $varients = \App\Helpers\CustomHelper::getProductVarients($product_id);
    $tags = explode(",",$tags);
    $multiple_images = \App\Helpers\CustomHelper::getProductImages($product_id);
    $alltags = \App\Models\Tags::where('is_delete',0)->get();
    ?>

    <div class="content ">

        <div class="mb-4">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <i class="bi bi-globe2 small me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page_heading}}</li>
                </ol>
            </nav>
        </div>
        @include('snippets.errors')
        @include('snippets.flash')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">{{$page_heading}}</div>
                            <?php if (request()->has('back_url')){
                                $back_url = request('back_url'); ?>
                            <div class="dropdown ms-auto">
                                <a href="{{ url($back_url) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>


                <div class="card mt-3">
                    <div class="card-body pt-0">
                        <form class="card-body" action="" method="post" accept-chartset="UTF-8"
                              enctype="multipart/form-data" role="form">
                            {{ csrf_field() }}
                            <input type="hidden" id="id" value="{{ $product_id }}">

                            <div class="row">
                                <h3>Product Details</h3>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" name="name"
                                           value="{{ old('name', $name) }}">
                                    @include('snippets.errors_first', ['param' => 'name'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">SKU</label>
                                    <input type="text" class="form-control" name="sku"
                                           value="{{ old('sku', $sku) }}">
                                    @include('snippets.errors_first', ['param' => 'sku'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">HSN</label>
                                    <input type="text" class="form-control" name="hsn"
                                           value="{{ old('hsn', $hsn) }}">
                                    @include('snippets.errors_first', ['param' => 'hsn'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Is Tax Included</label>
                                    <select class="form-control" name="is_tax_included">
                                        <option value="" selected>Select</option>
                                        <option value="1" {{$is_tax_included == 1?"selected":""}}>Yes</option>
                                        <option value="0" {{$is_tax_included == 0?"selected":""}}>No</option>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'sku'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Tax</label>
                                    <input type="text" class="form-control" name="tax"
                                           value="{{ old('tax', $tax) }}">
                                    @include('snippets.errors_first', ['param' => 'tax'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Min Qty</label>
                                    <input type="text" class="form-control" name="min_qty"
                                           value="{{ old('min_qty', $min_qty) }}">
                                    @include('snippets.errors_first', ['param' => 'min_qty'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Max Qty</label>
                                    <input type="text" class="form-control" name="max_qty"
                                           value="{{ old('max_qty', $max_qty) }}">
                                    @include('snippets.errors_first', ['param' => 'max_qty'])
                                </div>


                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Choose Vendor</label>
                                    <select class="form-control select2" name="vendor_id">
                                        <option value="" selected disabled>Select Vendor</option>
                                        <?php if (!empty($vendors)){
                                        foreach ($vendors as $vendor){
                                            ?>
                                        <option
                                            value="{{$vendor->id}}" <?php if ($vendor->id == $vendor_id) echo "selected" ?>>{{$vendor->name??''}}</option>
                                        <?php }

                                        } ?>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'vendor_id'])
                                </div>


                                <div class="form-group col-md-12 mt-3">
                                    <label for="inputEmail4" class="form-label">How To Use</label>
                                    <textarea class="form-control editor"
                                              name="short_description" >{{ old('short_description', $short_description) }}</textarea>
                                    @include('snippets.errors_first', ['param' => 'short_description'])
                                </div>
                                <div class="form-group col-md-12 mt-3">
                                    <label for="inputEmail4" class="form-label">Long Description</label>
                                    <textarea class="form-control editor"
                                              name="long_description">{{ old('long_description', $long_description) }}</textarea>
                                    @include('snippets.errors_first', ['param' => 'long_description'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Choose Category</label>
                                    <select class="form-control" name="category_id" id="category_id">
                                        <option value="" selected disabled>Select Category</option>
                                        <?php if (!empty($categories)){
                                        foreach ($categories as $category){
                                            ?>
                                        <option
                                            value="{{$category->id}}" <?php if ($category->id == $category_id) echo "selected" ?>>{{$category->name??''}}</option>
                                        <?php }

                                        } ?>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'vendor_id'])
                                </div>
                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Choose SubCategory</label>
                                    <select class="form-control select2" name="subcategory_id" id="subcategory_id">
                                        <option value="" selected disabled>Select SubCategory
                                        </option>
                                        <?php if (!empty($subcategories)){
                                        foreach ($subcategories as $cat){
                                            ?>
                                        <option
                                            value="{{ $cat->id }}" <?php if ($cat->id == $subcategory_id) {
                                            echo 'selected';
                                        } ?>>
                                            {{ $cat->name ?? '' }}</option>
                                        <?php }

                                        } ?>
                                    </select>
                                    @include('snippets.errors_first', [
                                        'param' => 'sub_category_id',
                                    ])
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Choose Brand</label>
                                    <select class="form-control select2" name="brand_id">
                                        <option value="" selected disabled>Select Brand</option>
                                        <?php if (!empty($brands)){
                                        foreach ($brands as $cat){
                                            ?>
                                        <option
                                            value="{{ $cat->id }}" <?php if ($cat->id == $brand_id) {
                                            echo 'selected';
                                        } ?>>
                                            {{ $cat->brand_name ?? '' }}</option>
                                        <?php }

                                        } ?>
                                    </select>
                                    @include('snippets.errors_first', [
                                        'param' => 'brand_id',
                                    ])
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Choose
                                        Manufacturer</label>
                                    <select class="form-control select2" name="manufacter_id">
                                        <option value="" selected disabled>Select Manufacturer
                                        </option>
                                        <?php if (!empty($manufacturer)){
                                        foreach ($manufacturer as $cat){
                                            ?>
                                        <option
                                            value="{{ $cat->id }}" <?php if ($cat->id == $manufacter_id) {
                                            echo 'selected';
                                        } ?>>
                                            {{ $cat->name ?? '' }}</option>
                                        <?php }

                                        } ?>
                                    </select>
                                    @include('snippets.errors_first', [
                                        'param' => 'manufacter_id',
                                    ])
                                </div>


                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Tags ( These tags help you in search
                                        result )</label>
{{--                                    <input type="text" class="form-control" name="tags"--}}
{{--                                           value="{{ old('tags', $tags) }}">--}}
                                    <select class="from-control" name="tags[]" id="tags">
                                        @foreach($alltags as $tag)
                                            <option value="{{$tag->name??''}}" {{in_array($tag->name,$tags) ? "selected":""}}>{{$tag->name??''}}</option>
                                        @endforeach
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'tags'])
                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Main Image (max 2MB, MinSize : 300 to Max Size: 2000)</label>
                                    <input type="file" class="form-control" placeholder="Name" name="image"
                                           value="" accept="image/*">
                                    @include('layouts.show_image',['type'=>'single','images'=>$image,'folder'=>''])

                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="validationCustom01" class="form-label">Other Images (Multiple) : (max 2MB, MinSize : 300 to Max Size: 2000)</label>
                                    <input type="file" class="form-control" placeholder="Name" name="product_images[]"
                                           multiple accept="image/*">

                                    @include('layouts.show_image',['type'=>'multiple','images'=>$multiple_images,'folder'=>'product_images'])


                                </div>

                                <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Type</label>
                                    <select class="form-control" name="type">
                                        <option value="" selected>Select Type</option>
                                        <option value="veg" {{$type == 'veg' ? "selected":""}}>Veg</option>
                                        <option value="nonveg" {{$type == 'nonveg' ? "selected":""}}>NonVeg</option>
                                        <option value="none" {{$type == 'none' ? "selected":""}}> None</option>
                                    </select>
                                    @include('snippets.errors_first', ['param' => 'tags'])
                                </div>



                                 <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" name="meta_title"
                                           value="{{ old('meta_title', $meta_title) }}">
                                    @include('snippets.errors_first', ['param' => 'meta_title'])
                                </div>

                                 <div class="form-group col-md-6 mt-3">
                                    <label for="inputEmail4" class="form-label">Meta Description</label>
                                    <input type="text" class="form-control" name="meta_description"
                                           value="{{ old('meta_description', $meta_description) }}">
                                    @include('snippets.errors_first', ['param' => 'meta_description'])
                                </div>

                                <div class="form-group col-md-4 mt-3">
                                    <label for="inputEmail4" class="form-label">MRP</label>
                                    <input type="text" class="form-control" name="product_mrp" id="product_mrp"
                                           value="{{ old('product_mrp', $product_mrp) }}">
                                    @include('snippets.errors_first', ['param' => 'product_mrp'])
                                </div>

                                 <div class="form-group col-md-4 mt-3">
                                    <label for="inputEmail4" class="form-label">Selling Price</label>
                                    <input type="text" class="form-control" name="product_selling_price" id="product_selling_price"
                                           value="{{ old('product_selling_price', $product_selling_price) }}">
                                    @include('snippets.errors_first', ['param' => 'product_selling_price'])
                                </div>
                                 <div class="form-group col-md-4 mt-3">
                                    <label for="inputEmail4" class="form-label">Subscription Price</label>
                                    <input type="text" class="form-control" name="product_subscription_price" id="product_subscription_price"
                                           value="{{ old('product_subscription_price', $product_subscription_price) }}">
                                    @include('snippets.errors_first', ['param' => 'product_subscription_price'])
                                </div>


                                <div class="form-group col-md-6 mt-3">
                                    <label for="userName" class="form-label">Status<span
                                            class="text-danger">*</span></label>

                                    <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="1"
                                               <?php echo $status == '1' ? 'checked' : ''; ?> checked>
                                        <label class="form-check-label"
                                               for="customRadioBox1">Active</label>
                                    </div>

                                    <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="0" <?php echo strlen($status) > 0 && $status == '0' ? 'checked' : ''; ?>>
                                        <label class="form-check-label"
                                               for="customRadioBox1">InActive</label>
                                    </div>
                                </div>
                            </div>
                            @include('layouts.varients',['varients'=>$varients])


                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection
