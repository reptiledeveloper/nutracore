<?php

namespace App\Imports;

use App\Models\Products;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // --- 1. Update or Insert Product ---
            $product = Products::updateOrCreate(
                ['id' => $row['id'] ?? 0],
                [
                    'name' => $row['productname'] ?? '',
                    'category_id' => $row['categoryid'] ?? '',
                    'subcategory_id' => $row['subcategoryid'] ?? '',
                    'brand_id' => $row['brandid'] ?? '',
                    'tags' => $row['tags'] ?? '',
                    'tax' => $row['tax'] ?? '',
                    // 'short_description' => $row['shortdescription'] ?? '',
                    // 'long_description' => $row['longdescription'] ?? '',
                    'image' => $row['image'] ?? '',
                    'type' => $row['type'] ?? '',
                    'sku' => $row['sku'] ?? '',
                    'hsn' => $row['hsn'] ?? '',
                    'product_weight' => $row['weight'] ?? '',
                    'product_mrp' => $row['mrp'] ?? '',
                    'product_selling_price' => $row['sellingprice'] ?? '',
                    'product_subscription_price' => $row['subscriptionprice'] ?? '',
                ]
            );

            // --- 2. Update or Insert Variant ---
            if (!empty($row['variant_id']) || !empty($row['varientname']) || !empty($row['sku'])) {
                DB::table('product_varients')->updateOrInsert(
                    [
                        'id' => $row['variant_id'] ?? null,
                        'product_id' => $product->id
                    ],
                    [
                        'unit' => $row['unit'] ?? '',
                        'varient_sku' => $row['sku'] ?? '',
                        'varient_weight' => $row['weight'] ?? '',
                        'mrp' => $row['mrp'] ?? '',
                        'selling_price' => $row['sellingprice'] ?? '',
                        'subscription_price' => $row['subscriptionprice'] ?? '',
                    ]
                );
            }
        }
    }
}
