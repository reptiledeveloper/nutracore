@php
    $search = $_GET['search'] ?? '';
    $category_id = $_GET['category_id'] ?? '';
    $subcategory_id = $_GET['subcategory_id'] ?? '';
    $folder_name = $_GET['folder_name'] ?? '';
    $vendor_id = $_GET['vendor_id'] ?? '';
    $date = $_GET['date'] ?? '';
    $agent_id = $_GET['agent_id'] ?? '';
    $brand_id = $_GET['brand_id'] ?? '';
    $tag = $_GET['tag'] ?? '';
    $type = $_GET['type'] ?? '';
    $product_id = $_GET['product_id'] ?? '';
    $payment_method = $_GET['payment_method'] ?? '';
    $pos_cancel_type = $_GET['pos_cancel_type'] ?? '';
    $order_from = $_GET['order_from'] ?? '';
    $status = $_GET['status'] ?? '';

    $current_url = url()->current();

    $categories = \App\Helpers\CustomHelper::getCategories();
    $vendors = \App\Helpers\CustomHelper::getVendors();
    $brands = \App\Helpers\CustomHelper::getBrands();
    $tags = \App\Helpers\CustomHelper::getTags();

    $subcategories = [];
    if (!empty($category_id)) {
        $subcategories = \App\Helpers\CustomHelper::getSubCategory($category_id);
    }
$products = \App\Helpers\CustomHelper::getProducts();
    $agents = \App\Helpers\CustomHelper::getAgents();

    $is_export = $is_export ?? '';
    $is_import = $is_import ?? '';
     $order_status_arr = config('custom.order_status_arr');
@endphp

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <form action="" method="get">
                <div class="card-body">
                    <h5>Filter</h5>
                    <div class="row">

                        @if(!empty($search_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" name="search" placeholder="Search..."
                                       value="{{ $search }}">
                            </div>
                        @endif

                        @if(!empty($brand_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Brand</label>
                                <select class="form-control" name="brand_id" id="brand_id">
                                    <option value="" selected>Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option
                                            value="{{ $brand->id }}" {{ $brand->id == $brand_id ? 'selected' : '' }}>
                                            {{ $brand->brand_name??'' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if(!empty($tag_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Tag</label>
                                <select class="form-control" name="tag" id="tag">
                                    <option value="" selected>Select Tag</option>
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->name ??''}}" {{ $tag->name == $tag ? "selected" : "" }}>
                                            {{ $tag->name??'' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if(!empty($order_from_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Order From</label>
                                <select class="form-control" name="order_from" id="order_from">
                                    <option value="" selected>Select Order From</option>
                                    <option value="POS" {{$order_from == "POS" ?"selected":""}}>POS</option>
                                    <option value="APP" {{$order_from == "APP" ?"selected":""}}>APP</option>
                                </select>
                            </div>
                        @endif
                        @if(!empty($categories_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Category</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    <option value="" selected>Select Category</option>
                                    @foreach($categories as $category)
                                        <option
                                            value="{{ $category->id }}" {{ $category->id == $category_id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if(!empty($register_by_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Registered By</label>
                                <select class="form-control" name="type" id="type">
                                    <option value="" selected>Select Registered By</option>
                                    <option value="app" {{$type == "app"?"selected":""}}>App</option>
                                    <option value="website" {{$type == "website"?"selected":""}}>Website</option>

                                </select>
                            </div>
                        @endif
                            @if(!empty($order_status_show))
                                <div class="col-md-4 mt-2">
                                    <label class="form-label">Order Status :</label>
                                    <select class="form-control" name="status">
                                        <option value="">Select Status</option>
                                        @foreach($order_status_arr as $stat => $val)
                                            <option
                                                value="{{ $stat }}" {{ $stat == $status ? 'selected' : '' }}>
                                                {{ $val }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        @if(!empty($payment_method_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Payment Method</label>
                                <select class="form-control" name="payment_method" id="payment_method">
                                    <option value="" selected>Select Payment Method</option>
                                    <option value="online" {{$type == "online"?"selected":""}}>Online</option>
                                    <option value="cod" {{$type == "cod"?"selected":""}}>COD</option>
                                    <option value="Multipay" {{$type == "Multipay"?"selected":""}}>Multipay</option>
                                    <option value="UPI" {{$type == "UPI"?"selected":""}}>UPI</option>
                                    <option value="Cash" {{$type == "Cash"?"selected":""}}>Cash</option>
                                    <option value="Card" {{$type == "Card"?"selected":""}}>Card</option>

                                </select>
                            </div>
                        @endif

                        @if(!empty($pos_cancel_type_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">POS Cancel Type</label>
                                <select class="form-control" name="pos_cancel_type" id="pos_cancel_type">
                                    <option value="" selected>Select</option>
                                    <option value="exchange" {{$type == "exchange"?"selected":""}}>Exchange</option>
                                    <option value="return" {{$type == "return"?"selected":""}}>Return</option>


                                </select>
                            </div>
                        @endif

                        @if(!empty($expiry_show))
                            <div class="col-md-4 mt-3">
                                <label class="form-label">Expiry Filter</label>
                                <select name="expiry_in_days" class="form-select" onchange="this.form.submit()">
                                    <option value="0" {{ $days===0 ? 'selected':'' }}>All</option>
                                    <option value="30" {{ $days===30? 'selected':'' }}>Within 30 days</option>
                                    <option value="60" {{ $days===60? 'selected':'' }}>Within 60 days</option>
                                    <option value="90" {{ $days===90? 'selected':'' }}>Within 90 days</option>
                                    <option value="180" {{ $days===180? 'selected':'' }}>Within 180 days</option>
                                </select>
                            </div>
                        @endif

                        @if(!empty($subcategory_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">SubCategory</label>
                                <select class="form-control" name="subcategory_id" id="subcategory_id">
                                    <option value="" selected>Select SubCategory</option>
                                    @foreach($subcategories as $subcategory)
                                        <option
                                            value="{{ $subcategory->id }}" {{ $subcategory->id == $subcategory_id ? 'selected' : '' }}>
                                            {{ $subcategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if(!empty($vendor_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Choose Store</label>
                                <select class="form-control" name="vendor_id">
                                    <option value="" selected>Select Store</option>
                                    @foreach($vendors as $vendor)
                                        <option
                                            value="{{ $vendor->id }}" {{ $vendor->id == $vendor_id ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if(!empty($folder_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Choose Folder</label>
                                <select class="form-control" name="folder_name">
                                    <option value="" selected>Select Folder</option>
                                    @foreach($folders as $folder)
                                        <option value="{{ $folder }}" {{ $folder == $folder_name ? 'selected' : '' }}>
                                            {{ ucfirst(basename($folder)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if(!empty($product_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Choose Product</label>
                                <select class="form-control select2" name="product_id">
                                    <option value="" selected>Select Product</option>
                                    @foreach($products as $product)
                                        <option
                                            value="{{ $product->id }}" {{ $product->id == $product_id ? 'selected' : '' }}>
                                            {{$product->sku??''}} - {{$product->name??''}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if(!empty($date_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" value="{{ $date }}">
                            </div>
                        @endif

                        @if(!empty($delivery_agents_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Choose Delivery Agent</label>
                                <select class="form-control" name="agent_id">
                                    <option value="" selected>Select Delivery Agent</option>
                                    @foreach($agents as $agent)
                                        <option
                                            value="{{ $agent->id }}" {{ $agent_id == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <button class="btn btn-primary">Search</button>
                            <a href="{{ $current_url }}" class="btn btn-danger">Reset</a>
                        </div>
                        <div class="col-md-6 text-right">
                            @if($is_export == 1)
                                <a href="{{ $export_url }}" class="btn btn-warning">Export</a>
                            @endif
                            @if($is_import == 1)
                                <a data-bs-toggle="modal" data-bs-target="#import_modal"
                                   class="btn btn-success">Import</a>
                            @endif
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

@if($is_import == 1)
    <!-- Import Modal -->
    <div class="modal fade" id="import_modal" tabindex="-1" role="dialog" aria-labelledby="importLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ $import_url }}" method="post" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importLabel">Import</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="file" name="file" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
@endif
