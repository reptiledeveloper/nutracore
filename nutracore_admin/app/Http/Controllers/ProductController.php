<?php

namespace App\Http\Controllers;

use App\Exports\SampleExport;
use App\Helpers\CustomHelper;
use App\Models\Blocks;
use App\Models\Company;
use App\Models\Products;
use App\Models\ProductVarient;
use App\Models\VendorProductPrice;
use App\Models\VariantImage;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use ProductImport;
use Storage;
use Validator;
use Yajra\DataTables\DataTables;


class ProductController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $category_id = $request->category_id ?? '';
        $subcategory_id = $request->subcategory_id ?? '';
        $vendor_id = $request->vendor_id ?? '';
        $products = Products::where('is_delete', 0)->latest();
        if (!empty($category_id)) {
            $products->where('category_id', $category_id);
        }
        if (!empty($subcategory_id)) {
            $products->where('subcategory_id', $subcategory_id);
        }
        if (!empty($vendor_id)) {
            $product_ids = CustomHelper::getVendorProductIds($vendor_id);
            $products->whereIn('id', $product_ids);
        }
        if (!empty($search)) {
            $products->where('name', 'like', '%' . $search . '%');
        }

        $products = $products->paginate(50);
        $data['products'] = $products;
        return view('products.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;

        $product = '';
        if (is_numeric($id) && $id > 0) {
            $product = Products::find($id);
            if (empty($product)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/products');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/products';
            }
            $rules = [];
            if (is_numeric($id) && $id > 0) {

            } else {
                //$rules['image'] = 'mimes:jpg,jpeg,png|max:2048|dimensions:min_width=300,min_height=300,max_width=2000,max_height=2000';
                // $rules['product_images'] = 'mimes:jpg,jpeg,png|max:2048|dimensions:min_width=300,min_height=300,max_width=2000,max_height=2000';
            }

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Product has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Product has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Product';

        if (!empty($product)) {
            $page_heading = 'Update Product';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['product'] = $product;

        return view('products.form', $data);

    }


    public function save(Request $request, $id = 0)
    {


        $data = $request->except(['_token', 'back_url', 'varient_weight', 'variant_name', 'image', 'varient_sku', 'unit', 'unit_value', 'mrp', 'selling_price', 'subscription_price', 'varient_id', 'product_images', 'variant_images']);
        $oldImg = '';
        $data['is_approve'] = 1;

        if (!empty($request->option_name)) {
            $data['option_name'] = implode(',', $request->option_name);
        }
        if (!empty($request->attribute_values)) {
            $data['attribute_values'] = implode(',', $request->attribute_values);
        }
        if (!empty($request->tags)) {
            $data['tags'] = implode(',', $request->tags);
        }
        $product = new Products();
        if (is_numeric($id) && $id > 0) {
            $exist = Products::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $product = $exist;
                $oldImg = $exist->image;
            }
        }
        foreach ($data as $key => $val) {
            $product->$key = $val;
        }
        $isSaved = $product->save();
        if ($isSaved) {
            $this->saveImage($request, $product, $oldImg);
            $this->saveVarients($request, $product);
            $this->saveAttributesProducts($request, $product);
        }
        return $isSaved;
    }

    public function saveVarients($request, $product)
    {
        $variant_name = $request->variant_name ?? [];
        $mrp = $request->mrp ?? [];
        $selling_price = $request->selling_price ?? [];
        $subscription_price = $request->subscription_price ?? [];
        $variant_ids = $request->varient_id ?? [];
        $varient_sku = $request->varient_sku ?? [];
        $varient_weight = $request->varient_weight ?? [];

        foreach ($variant_name as $key => $name) {
            if (!empty($name) && !empty($selling_price[$key])) {
                $dbArray = [];
                $dbArray['product_id'] = $product->id;
                $dbArray['unit'] = $name;
                $dbArray['mrp'] = $mrp[$key] ?? null;
                $dbArray['selling_price'] = $selling_price[$key] ?? null;
                $dbArray['subscription_price'] = $subscription_price[$key] ?? null;
                $dbArray['varient_sku'] = $varient_sku[$key] ?? null;
                $dbArray['varient_weight'] = $varient_weight[$key] ?? null;

                // Check if updating or inserting
                if (!empty($variant_ids[$key])) {
                    $variant = ProductVarient::find($variant_ids[$key]);
                    if ($variant) {
                        $variant->update($dbArray);
                    }
                } else {
                    $variant = ProductVarient::create($dbArray);
                }

                // Handle variant images (multiple)
                if ($request->hasFile("variant_images.$key") && isset($variant)) {
                    foreach ($request->file("variant_images.$key") as $file) {
                        $path = 'products';
                        $uploaded_data = CustomHelper::UploadImage($file, $path);
                        // Save to related table
                        VariantImage::create([
                            'varient_id' => $variant->id,
                            'product_id' => $product->id,
                            'image' => $uploaded_data,
                        ]);
                    }
                }
            }
        }

        return true;
    }

    public function saveAttributesProducts($request, $product)
    {
        // Clear existing attribute values for this product (if editing)
        DB::table('attributes_products')->where('products_id', $product->id)->delete();

        $attributeIDs = $request->option_name ?? [];
        $attributeValues = $request->attribute_values ?? [];

        foreach ($attributeIDs as $index => $attributeID) {
            if (!empty($attributeID) && !empty($attributeValues[$index])) {
                // Split values like "L,XL" or "Red,White"
                $values = explode(',', $attributeValues[$index]);

                foreach ($values as $val) {
                    DB::table('attributes_products')->insert([
                        'products_id' => $product->id,
                        'attributes_id' => $attributeID,
                        'values' => trim($val),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }


    private function saveImage($request, $product, $oldImg = '')
    {

        $file = $request->file('image');
        if ($file) {
            $path = 'products';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            if ($uploaded_data) {
                $product->image = $uploaded_data;
                $product->save();
            }
        }

        $files = $request->file('product_images');
        if (!empty($files)) {
            foreach ($files as $file) {
                $path = 'products';
                $uploaded_data = CustomHelper::UploadImage($file, $path);
                $dbArray = [];
                $dbArray['product_id'] = $product->id;
                $dbArray['image'] = $uploaded_data;
                DB::table('product_images')->insert($dbArray);
            }
        }


    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Products::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Products has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }

    public function view(Request $request)
    {
        $method = $request->method();
        $id = $request->id ?? '';
        if ($method == 'post' || $method == 'POST') {
            $vendor_id = $request->vendor_id ?? '';
            $product_id = $request->product_id ?? '';
            $unit = $request->unit ?? '';
            $unit_value = $request->unit_value ?? '';
            $mrp = $request->mrp ?? '';
            $selling_price = $request->selling_price ?? '';
            $subscription_price = $request->subscription_price ?? '';
            $varient_id = $request->varient_id ?? '';
            $status = $request->status ?? '';
            $stock_avail = $request->stock_avail ?? '';
            $is_stock = $request->is_stock ?? '';

            $is_subscribed_product = $request->is_subscribed_product ?? 0;
            if (!empty($vendor_id)) {
                foreach ($vendor_id as $key => $value) {
                    $exist = CustomHelper::checkVendorPrice($value, $product_id[$key] ?? '', $varient_id[$key] ?? '');
                    $dbArray = [];
                    $dbArray['vendor_id'] = $value;
                    $dbArray['product_id'] = $product_id[$key] ?? '';
                    $dbArray['varient_id'] = $varient_id[$key] ?? '';
                    $dbArray['unit'] = $unit[$key] ?? '';
                    $dbArray['unit_value'] = $unit_value[$key] ?? '';
                    $dbArray['mrp'] = $mrp[$key] ?? '';
                    $dbArray['selling_price'] = $selling_price[$key] ?? '';
                    $dbArray['subscription_price'] = $subscription_price[$key] ?? '';
                    $dbArray['status'] = $status[$key] ?? 0;
                    $dbArray['is_subscribed_product'] = $is_subscribed_product[$key] ?? 0;
                    $dbArray['is_stock'] = $is_stock[$key] ?? 0;
                    $dbArray['stock_avail'] = $stock_avail[$key] ?? 0;
                    if (empty($exist)) {
                        VendorProductPrice::insert($dbArray);
                    } else {
                        VendorProductPrice::where('id', $exist->id)->update($dbArray);
                    }
                }
            }
            return back()->with('alert-success', 'Prices Successfully Updated...');

        }

        $products = Products::where('is_delete', 0)->where('id', $id)->first();
        $data['products'] = $products;
        return view('products.view', $data);
    }

    public function search(Request $request)
    {
        $search = $request->q ?? '';
        $itemArr = [];
        $pagination = false;
        if (!empty($search)) {
            $products = Products::where('status', 1)->where('is_delete', 0);
            $products->where('name', 'like', '%' . $search . '%');
            $products = $products->paginate(10);
            if (!empty($products)) {
                foreach ($products as $product) {
                    $dbArray = [];
                    $dbArray['id'] = $product->id ?? '';
                    $dbArray['text'] = $product->name ?? '';
                    $itemArr[] = $dbArray;
                }
            }
            if ($products->lastPage() > 1) {
                $pagination = true;
            }
        }
        $paginationArr['more'] = $pagination;
        echo json_encode(['items' => $itemArr, 'pagination' => $paginationArr]);
    }

    public function sample(Request $request)
    {


        $exportArr = [];
        $products = Products::where('is_delete', 0)->get();
        if (!empty($products)) {
            foreach ($products as $product) {
                $attributesArr = [];
                $attributes_products = DB::table('attributes_products')->where('products_id', $product->id)->groupBy('attributes_id')->get();
                if (!empty($attributes_products)) {
                    foreach ($attributes_products as $attributes_product) {
                        $dbArray = [];
                        $dbArray['attributes_id'] = $attributes_product->attributes_id ?? '';
                        $dbArray['attributes_name'] = CustomHelper::getAttributeName($attributes_product->attributes_id ?? '') ?? '';
                        $attributes_product_data = DB::table('attributes_products')->where('products_id', $product->id)->where('attributes_id', $attributes_product->attributes_id)->get();
                        if (!empty($attributes_product_data)) {
                            $dbArray1 = [];
                            foreach ($attributes_product_data as $attributes_product_dat) {
                                $dbArray1[] = $attributes_product_dat->values ?? '';
                            }
                            $dbArray['attributes_values'] = $dbArray1;
                        }
                        $attributesArr[] = $dbArray;
                    }
                }
                $excelArr = [];
                $excelArr['ID'] = $product->id ?? '';
                $excelArr['ProductName'] = $product->name ?? '';
                $excelArr['CategoryID'] = $product->category_id ?? '';
                $excelArr['CategoryName'] = CustomHelper::getCategoryName($product->category_id ?? '') ?? '';
                $excelArr['SubCategoryID'] = $product->subcategory_id ?? '';
                $excelArr['SubCategoryName'] = CustomHelper::getCategoryName($product->subcategory_id ?? '') ?? '';
                $excelArr['BrandID'] = $product->brand_id ?? '';
                $excelArr['BrandName'] = CustomHelper::getBrandName($product->brand_id ?? '') ?? '';
                $excelArr['Tags'] = $product->tags ?? '';
                $excelArr['Tax'] = $product->tax ?? '';
                $excelArr['ShortDescription'] = $product->short_description ?? '';
                $excelArr['LongDescription'] = $product->long_description ?? '';
                $excelArr['Image'] = $product->image ?? '';
                $excelArr['Type'] = $product->type ?? '';
                $excelArr['sku'] = $product->sku ?? '';
                $excelArr['HSN'] = $product->hsn ?? '';
               // $excelArr['AttributeValues'] = $product->attribute_values ?? '';


//
//                for ($j = 1; $j <= 4; $j++) {
//                    $index1 = $j - 1;
//                    $excelArr['AttributeID' . $j] = $attributesArr[$index1]['attributes_id'] ?? '';
//                    $excelArr['AttributeName' . $j] = $attributesArr[$index1]['attributes_name'] ?? '';
//                    $array = json_decode($attributesArr[$index1]['attributes_values'] ?? '', true);
//                    $string = '"' . implode('","', $array) . '"';
//                    $excelArr['Values' . $j] = $string ?? '';
//                }
//
//
////                $excelArr['attribute_data'] = $attributesArr;
                $varients = CustomHelper::getAdminProductVarients($product->id);
                for ($i = 1; $i <= 15; $i++) {
                    $index = $i - 1;
                    $excelArr['VarientId' . $i] = $varients[$index]->id ?? '';
                    $excelArr['Unit' . $i] = $varients[$index]->unit ?? '';
                    $excelArr['SKU' . $i] = $varients[$index]->varient_sku ?? '';
                    $excelArr['Weight' . $i] = $varients[$index]->varient_weight ?? '';
                    $excelArr['MRP' . $i] = $varients[$index]->mrp ?? '';
                    $excelArr['SellingPrice' . $i] = $varients[$index]->selling_price ?? '';
                    $excelArr['SubscriptionPrice' . $i] = $varients[$index]->subscription_price ?? '';
                }
                $exportArr[] = $excelArr;
            }
        }

        if (!empty($exportArr)) {
            $headings = array_keys($exportArr[0]);
            $fileName = 'Product Sample-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $headings), $fileName);
        } else {
            return back();
        }
    }


    public function import(Request $request)
    {
        $data = [];
        $method = $request->method();
        if($method == 'POST'){
            $request->validate([
                'file' => 'required',
            ]);

            Excel::import(new ProductImport, $request->file('file'));
            return back()->with('success', 'Products imported successfully!');
        }

        return view('products.import', $data);

    }

    public function importold(Request $request)
    {
        $data = [];
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $rules = [];
            $rules['file'] = 'required';
            $request->validate($rules);
            $dataArray = Excel::toCollection(new class implements ToCollection, WithHeadings {
                public function collection(Collection $rows)
                {
                    return $rows;
                }

                public function headings(): array
                {
                    return ['Name', 'Price', 'Quantity']; // Adjust as needed
                }
            }, $request->file('file'));
            $flattenedData = $dataArray[0]; // First sheet
            $headings = $dataArray[0]->first()->values()->toArray();// First sheet

            $headingsArr = [];
            for ($i = 0; $i <= 11; $i++) {
                $headingsArr[] = $headings[$i] ?? '';
            }
            $headingsArr[] = 'Varients';
            $data['products'] = $flattenedData;
            $data['headings'] = $headingsArr;
            $productArr = [];
            if (!empty($flattenedData)) {
                foreach ($flattenedData as $key => $product) {
                    if ($key > 0) {
                        $varientArr = [];
                        $productData = [];
                        $productData['id'] = $product[0] ?? '';
                        $sellerName = \App\Helpers\CustomHelper::getVendorName($product[1] ?? '');
                        $categoryName = \App\Helpers\CustomHelper::getCategoryName($product[3] ?? '');
                        $subCategoryName = \App\Helpers\CustomHelper::getCategoryName($product[4] ?? '');
                        $brandName = \App\Helpers\CustomHelper::getBrandName($product[5] ?? '');
                        $manufactureName = \App\Helpers\CustomHelper::getManufactureName($product[6] ?? '');
                        $image = \App\Helpers\CustomHelper::getImageUrl('products', $product[10] ?? '');

                        $productData['seller_id'] = $product[1] ?? '';
                        $productData['seller_name'] = $sellerName ?? '';
                        $productData['product_name'] = $product[2] ?? '';
                        $productData['category_id'] = $product[3] ?? '';
                        $productData['category_name'] = $categoryName ?? '';
                        $productData['subcategory_id'] = $product[4] ?? '';
                        $productData['subcategory_name'] = $subCategoryName ?? '';
                        $productData['brand_id'] = $product[5] ?? '';
                        $productData['brand_name'] = $brandName ?? '';
                        $productData['manufacture_id'] = $product[6] ?? '';
                        $productData['manufacture_name'] = $manufactureName ?? '';
                        $productData['tags'] = $product[7] ?? '';
                        $productData['short_description'] = $product[8] ?? '';
                        $productData['long_description'] = $product[9] ?? '';
                        $productData['image'] = $image ?? '';
                        $productData['image_path'] = $product[10] ?? '';
                        $productData['type'] = $product[11] ?? '';
                        $productData['sku'] = $product[12] ?? '';
                        $productData['made_in'] = $product[13] ?? '';
                        $productData['is_subscribed_product'] = $product[14] ?? 0;

                        for ($i = 15; $i <= 31; $i++) {
                            if (!empty($product[$i])) {
                                $dbArray = [];
                                $dbArray['varient_id'] = $product[$i - 1] ?? '';
                                $i++;
                                $dbArray['unit'] = $product[$i - 1] ?? '';
                                $i++;
                                $dbArray['unit_value'] = $product[$i - 1] ?? '';
                                $i++;
                                $dbArray['mrp'] = $product[$i - 1] ?? '';
                                $i++;
                                $dbArray['selling_price'] = $product[$i - 1] ?? '';
                                $i++;
                                $dbArray['subscription_price'] = $product[$i - 1] ?? '';
                                $varientArr[] = $dbArray;


                            }
                        }
                        $productData['varients'] = $varientArr;

                        $productArr[] = $productData;
                    }
                }
            }

            $data['products_data'] = $productArr;


            return view('products.import', $data);
        }


        return view('products.import', $data);

    }


    public function import_product(Request $request)
    {
        $productArr = $request->productArr ?? '';
        $productArr = json_decode($productArr);
        if (!empty($productArr)) {
            foreach ($productArr as $product) {
                $dbArray = [];
                $id = $product->id ?? '';
                $seller_id = $product->seller_id ?? '';
                if (!empty($product->seller_id)) {
                    $dbArray['vendor_id'] = $product->seller_id;
                }
                if (!empty($product->product_name)) {
                    $dbArray['name'] = $product->product_name;
                }
                if (!empty($product->category_id)) {
                    $dbArray['category_id'] = $product->category_id;
                }
                if (!empty($product->subcategory_id)) {
                    $dbArray['subcategory_id'] = $product->subcategory_id;
                }
                if (!empty($product->brand_id)) {
                    $dbArray['brand_id'] = $product->brand_id;
                }
                if (!empty($product->manufacture_id)) {
                    $dbArray['manufacter_id'] = $product->manufacture_id;
                }
                if (!empty($product->tags)) {
                    $dbArray['tags'] = $product->tags;
                }
                if (!empty($product->short_description)) {
                    $dbArray['short_description'] = $product->short_description;
                }
                if (!empty($product->long_description)) {
                    $dbArray['long_description'] = $product->long_description;
                }
                if (!empty($product->type)) {
                    $dbArray['type'] = $product->type;
                }
                if (!empty($product->sku)) {
                    $dbArray['sku'] = $product->sku;
                }
                if (!empty($product->made_in)) {
                    $dbArray['made_in'] = $product->made_in;
                }
                if (isset($product->is_subscribed_product)) {
                    $dbArray['is_subscribed_product'] = $product->is_subscribed_product;
                }
                if (!empty($product->image_path)) {
                    $dbArray['image'] = $product->image_path;
                }

                if (!empty($id)) {
                    Products::where('id', $id)->update($dbArray);
                } else {
                    $id = Products::insertGetId($dbArray);
                }
                $varients = $product->varients ?? '';
                if (!empty($varients)) {
                    foreach ($varients as $varient) {
                        $dbArray1 = [];
                        $varient_id = $varient->varient_id ?? '';
                        $dbArray1['product_id'] = $id;
                        if (!empty($varient->unit)) {
                            $dbArray1['unit'] = $varient->unit;
                        }
                        if (!empty($varient->unit_value)) {
                            $dbArray1['unit_value'] = $varient->unit_value;
                        }
                        if (!empty($varient->mrp)) {
                            $dbArray1['mrp'] = $varient->mrp;
                        }
                        if (!empty($varient->selling_price)) {
                            $dbArray1['selling_price'] = $varient->selling_price;
                        }
                        if (!empty($varient->subscription_price)) {
                            $dbArray1['subscription_price'] = $varient->subscription_price;
                        }

                        if (!empty($varient_id)) {
                            ProductVarient::where('id', $varient_id)->update($dbArray1);
                        } else {
                            $varient_id = ProductVarient::insertGetId($dbArray1);
                        }

                        if (!empty($seller_id)) {
                            $exist = CustomHelper::checkVendorPrice($seller_id, $id, $varient_id);
                            $dbArray2 = [];
                            if (!empty($varient->unit)) {
                                $dbArray2['unit'] = $varient->unit;
                            }
                            if (!empty($varient->unit_value)) {
                                $dbArray2['unit_value'] = $varient->unit_value;
                            }
                            if (!empty($varient->mrp)) {
                                $dbArray2['mrp'] = $varient->mrp;
                            }
                            if (!empty($varient->selling_price)) {
                                $dbArray2['selling_price'] = $varient->selling_price;
                            }
                            if (!empty($varient->subscription_price)) {
                                $dbArray2['subscription_price'] = $varient->subscription_price;
                            }

                            if (!empty($exist)) {
                                VendorProductPrice::where('id', $exist->id)->update($dbArray2);
                            } else {
                                $dbArray2['vendor_id'] = $seller_id;
                                $dbArray2['product_id'] = $id;
                                $dbArray2['varient_id'] = $varient_id;
                                VendorProductPrice::insert($dbArray2);
                            }
                        }
                    }
                }
            }
        }
        return back()->with('alert-success', 'Product Imported Successfully');
    }


    public function approve_product(Request $request)
    {
        $data = [];
        $products = Products::where('vendor_id', '!=', null)->where('is_approve', 0)->latest();

        $products = $products->paginate(10);
        $data['products'] = $products;

        return view('products.approve_product', $data);
    }

    public function assign_product(Request $request)
    {

        $data = [];
        $vendor_id = $request->vendor_id ?? '';
        $category_id = $request->category_id ?? '';
        $subcategory_id = $request->subcategory_id ?? '';
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $vendor_id = $request->vendor_id ?? '';
            $product_ids = $request->product_ids ?? '';
            if (!empty($product_ids)) {
                foreach ($product_ids as $key => $product_id) {
                    $varients = CustomHelper::getProductVarients($product_id);
                    if (!empty($varients)) {
                        foreach ($varients as $varient) {
                            $exist = CustomHelper::checkVendorPrice($vendor_id, $product_id ?? '', $varient->id ?? '');
                            $dbArray = [];
                            $dbArray['vendor_id'] = $vendor_id;
                            $dbArray['product_id'] = $product_id ?? '';
                            $dbArray['varient_id'] = $varient->id ?? '';
                            $dbArray['unit'] = $varient->unit ?? '';
                            $dbArray['unit_value'] = $varient->unit_value ?? '';
                            $dbArray['mrp'] = $varient->mrp ?? '';
                            $dbArray['selling_price'] = $varient->selling_price ?? '';
                            $dbArray['subscription_price'] = $varient->subscription_price ?? '';
                            $dbArray['status'] = 1;
                            if (empty($exist)) {
                                VendorProductPrice::insert($dbArray);
                            } else {
                                VendorProductPrice::where('id', $exist->id)->update($dbArray);
                            }
                        }
                    }
                }
            }
            return back();
        }

        $products = [];
        if (!empty($vendor_id) && !empty($category_id) && !empty($subcategory_id)) {
            $products = Products::select('id', 'name', 'image', 'vendor_id', 'status')->where('category_id', $category_id)->where('subcategory_id', $subcategory_id)->where('status', 1)->get();
        }
        $data['vendor_id'] = $vendor_id;
        $data['products'] = $products;

        return view('products.assign_product', $data);
    }

}
