<?php

$attributes = DB::Table('attribute')->where('status', 1)->where('is_delete', 0)->get();
    if(!empty($product)){
        $option_name = explode(",", $product->option_name) ?? '';
$options = DB::Table('attribute')->where('status', 1)->whereIn('id', $option_name)->get();

    }else{
        $option_name = "";
        $options = [];
    }
?>

<style>
    .tag {
        display: inline-flex;
        align-items: center;
        background-color: #f1f3f5;
        color: #333;
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 20px;
        margin: 3px;
        transition: background 0.2s;
    }

    .tag:hover {
        background-color: #e2e6ea;
    }

    .tag button {
        border: none;
        background: transparent;
        margin-left: 6px;
        font-weight: bold;
        color: #dc3545;
        font-size: 1rem;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }
</style>
</head>

<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Product Options</h5>
            <button id="add-option" type="button" class="btn btn-sm btn-primary">Add another option</button>
        </div>
    </div>
</div>

<div class="row mt-3" id="options-container">
    @php $optionCount = 1; @endphp
    @if(!empty($options))
        @foreach($options as $index => $attribute)
            @php
                $attributes_products = DB::table('attributes_products')->where('products_id', $product->id)->where('attributes_id', $attribute->id)->get();
            @endphp
            <div class="col-md-12">
                <div class="option-group border rounded p-3 mb-3 bg-light">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="form-label fw-bold mb-0">Option <span class="option-index">{{ $optionCount++ }}</span>:
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-option">Remove</button>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 mb-2">
                            <select class="form-control" name="option_name[]">
                                <option value="">Select Attribute</option>
                                @foreach($attributes as $attr)
                                    <option value="{{ $attr->id }}" {{ $attr->id == $attribute->id ? 'selected' : '' }}>
                                        {{ $attr->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 mb-2">
                            <div class="tags-wrapper position-relative border rounded px-2 py-1 bg-white"
                                style="min-height: 45px;">
                                <div class="tags-container d-flex flex-wrap align-items-center gap-1">

                                    @php $values = []; @endphp
                                    @foreach($attributes_products as $val)
                                        @php
                                            $values[] = $val->values ?? '';
                                        @endphp
                                        <span class="tag">{{ $val->values ?? '' }}<button type="button"
                                                class="remove-tag">&times;</button></span>
                                    @endforeach
                                    <input type="text" class="form-control border-0 shadow-none tag-input flex-grow-1"
                                        placeholder="Type value and press Enter" style="min-width: 120px;">
                                    <input type="hidden" class="hidden-attribute-values" name="attribute_values[]"
                                        value="{{ implode(',', $values) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<template id="option-template">
    <div class="col-md-12">
        <div class="option-group border rounded p-3 mb-3 bg-light">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-label fw-bold mb-0">Option <span class="option-index"></span>:</div>
                <button type="button" class="btn btn-sm btn-danger remove-option">Remove</button>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-2">
                    <select class="form-control" name="option_name[]">
                        <option value="">Select Attribute</option>
                        @foreach($attributes as $attribute)
                            <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 mb-2">
                    <div class="tags-wrapper position-relative border rounded px-2 py-1 bg-white"
                        style="min-height: 45px;">
                        <div class="tags-container d-flex flex-wrap align-items-center gap-1">
                            <input type="text" class="form-control border-0 shadow-none tag-input flex-grow-1"
                                placeholder="Type value and press Enter" style="min-width: 120px;">
                            <input type="hidden" class="hidden-attribute-values" name="attribute_values[]">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<div class="mt-3">
    <button type="button" class="btn btn-success" id="generate-combinations">Generate Variant Combinations</button>
</div>

<div class="mt-4">
    <h5>Variant Combinations:</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle" id="variant-table">
            <thead class="table-light">
                <tr>
                    <th>Variant</th>
                    <th>Images</th>
                    <th>SKU</th>
                    <th>Weight</th>
                    <th>MRP</th>
                    <th>Selling Price</th>
                    <th>Subscription Price</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($product->variants))
                    @foreach($product->variants as $index => $variant)
                        <tr>
                            <input type="hidden" name="variant_name[]" value="{{ $variant->unit }}">
                            <input type="hidden" name="varient_id[]" value="{{ $variant->id }}">
                            <td><strong>{{ $variant->unit }}</strong></td>
                            <td>
                                <input type="file" name="variant_images[{{ $index }}][]" class="form-control form-control-sm"
                                    multiple>
                                @foreach($variant->images as $img)
                                    <div class="mt-1 d-inline-block position-relative me-2">
                                        <img src="{{ asset('storage/' . $img->image_path) }}" width="50" height="50"
                                            class="border rounded">
                                    </div>
                                @endforeach
                            </td>
                              <td><input type="text"  name="varient_sku[]" class="form-control form-control-sm"
                                    value="{{ $variant->varient_sku }}" placeholder="Enter sku"></td>
                            <td><input type="text"  name="varient_weight[]" class="form-control form-control-sm"
                                    value="{{ $variant->varient_weight }}" placeholder="Enter Weight"></td>
                            <td><input type="number" step="0.01" name="mrp[]" class="form-control form-control-sm"
                                    value="{{ $variant->mrp }}" placeholder="Enter MRP"></td>
                            <td><input type="number" step="0.01" name="selling_price[]" class="form-control form-control-sm"
                                    value="{{ $variant->selling_price }}" placeholder="Selling Price"></td>
                            <td><input type="number" step="0.01" name="subscription_price[]"
                                    class="form-control form-control-sm" value="{{ $variant->subscription_price }}"
                                    placeholder="Subscription Price"></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        let optionCount = "{{ isset($product) && isset($product->attributes) ? count($product->attributes) : 0 }}";


        function updateHiddenValues(container) {
            const tags = container.find('.tag').map(function () {
                return $(this).clone().children().remove().end().text().trim();
            }).get();
            container.find('.hidden-attribute-values').val(tags.join(','));
        }

        function bindTagInputEvents(group) {
            const input = group.find('.tag-input');
            const container = group;

            input.off('keypress').on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    const tagText = this.value.trim();
                    if (tagText !== "") {
                        const tag = $(`<span class="tag">${tagText}<button type="button" class="remove-tag">&times;</button></span>`);
                        tag.insertBefore($(this));
                        this.value = "";
                        updateHiddenValues(container);
                    }
                }
            });

            group.off('click', '.remove-tag').on('click', '.remove-tag', function () {
                $(this).closest('.tag').remove();
                updateHiddenValues(container);
            });
        }

        // Bind tags on existing (edit) form
        $('#options-container .option-group').each(function () {
            bindTagInputEvents($(this));
        });

        $('#add-option').click(function () {
            optionCount++;
            const template = $($('#option-template').html());
            template.find('.option-index').text(optionCount);
            bindTagInputEvents(template);
            $('#options-container').append(template);
        });

        $('#options-container').on('click', '.remove-option', function () {
            $(this).closest('.col-md-12').remove();
        });



        $('#generate-combinations').click(function () {
            const attributes = [];

            $('#options-container .option-group').each(function () {
                const attrName = $(this).find('select[name="option_name[]"] option:selected').text().trim();
                const values = $(this).find('.tag').map(function () {
                    return $(this).clone().children().remove().end().text().trim();
                }).get();

                if (attrName && values.length > 0) {
                    attributes.push({
                        name: attrName,
                        values: values
                    });
                }
            });

            if (attributes.length === 0) {
                alert("Please add at least one option with values.");
                return;
            }

            const valueSets = attributes.map(attr => attr.values);
            const combinations = cartesianProduct(valueSets);

            const existingVariants = {};
            $('#variant-table tbody tr').each(function () {
                const name = $(this).find('input[name="variant_name[]"]').val();
                existingVariants[name] = {
                    id: $(this).find('input[name="varient_id[]"]').val() || '',
                    mrp: $(this).find('input[name="mrp[]"]').val(),
                    varient_sku: $(this).find('input[name="varient_sku[]"]').val(),
                    varient_weight: $(this).find('input[name="varient_weight[]"]').val(),
                    selling: $(this).find('input[name="selling_price[]"]').val(),
                    subscription: $(this).find('input[name="subscription_price[]"]').val()
                };
            });

            const $tbody = $('#variant-table tbody');
            $tbody.empty();
            const defaultMrp = $('#product_mrp').val() || '';
            const defaultSelling = $('#product_selling_price').val() || '';
            const defaultSubscription = $('#product_subscription_price').val() || '';


            combinations.forEach((combo, index) => {
                const variantName = combo.join(' / ');
                const existing = existingVariants[variantName] || {};

                const row = `
    <tr>
        <input type="hidden" name="variant_name[]" value="${variantName}">
        <input type="hidden" name="varient_id[]" value="${existing.id || ''}">
        <td><strong>${variantName}</strong></td>
        <td><input type="file" name="variant_images[${index}][]" class="form-control form-control-sm" multiple></td>
            <td><input type="text"  name="varient_sku[]" class="form-control form-control-sm"
                                    value="${existing.varient_sku || ''}" placeholder="Enter sku"></td>
<td><input type="text"  name="varient_weight[]" class="form-control form-control-sm"
                                    value="${existing.varient_weight || ''}" placeholder="Enter Weight"></td>
        <td><input type="number" step="0.01" name="mrp[]" class="form-control form-control-sm" value="${existing.mrp || defaultMrp}" placeholder="Enter MRP"></td>
        <td><input type="number" step="0.01" name="selling_price[]" class="form-control form-control-sm" value="${existing.selling || defaultSelling}" placeholder="Selling Price"></td>
        <td><input type="number" step="0.01" name="subscription_price[]" class="form-control form-control-sm" value="${existing.subscription || defaultSubscription}" placeholder="Subscription Price"></td>
    </tr>
`;

                $tbody.append(row);
            });
        });
    });

    function cartesianProduct(arr) {
        if (arr.length === 0) return [];
        if (arr.length === 1) return arr[0].map(val => [val]);

        return arr.reduce((a, b) =>
            a.flatMap(d => b.map(e => [...(Array.isArray(d) ? d : [d]), e]))
        );
    }
</script>
