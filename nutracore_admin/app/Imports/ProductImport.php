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
                    'short_description' => $row['shortdescription'] ?? '',
                    'long_description' => $row['longdescription'] ?? '',
                    'image' => $row['image'] ?? '',
                    'type' => $row['type'] ?? '',
                    'sku' => $row['sku'] ?? '',
                    'hsn' => $row['hsn'] ?? '',
                    'attribute_values' => $row['attributevalues'] ?? '',
                    'product_mrp' => $row['product_mrp'] ?? '',
                    'product_selling_price' => $row['product_selling_price'] ?? '',
                    'product_subscription_price' => $row['product_subscription_price'] ?? '',
                ]
            );

            // --- 2. Update or Insert Variants ---
            for ($i = 1; $i <= 15; $i++) {
                $variantId = $row['varientid'.$i] ?? null;
                $unit = $row['unit'.$i] ?? null;
                $sku = $row['sku'.$i] ?? null;
                $weight = $row['weight'.$i] ?? null;
                $mrp = $row['mrp'.$i] ?? null;
                $selling = $row['sellingprice'.$i] ?? null;
                $subscription = $row['subscriptionprice'.$i] ?? null;

                // Skip empty rows
                if (!$unit && !$sku && !$mrp) continue;

                DB::table('product_varients')->updateOrInsert(
                    ['id' => $variantId, 'product_id' => $product->id],
                    [
                        'unit' => $unit,
                        'varient_sku' => $sku,
                        'varient_weight' => $weight,
                        'mrp' => $mrp,
                        'selling_price' => $selling,
                        'subscription_price' => $subscription,
                    ]
                );
            }
        }
    }
}
