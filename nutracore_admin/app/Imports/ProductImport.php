<?php
namespace App\Imports;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        print_r($row);
        die;
        $productName = $row['product_name'] ?? '';
        $attributeJson = $row['attribute_values'] ?? '';

        return new Products([
            'name' => $productName,
            'attribute_values' => $attributeJson,
        ]);
    }
}
