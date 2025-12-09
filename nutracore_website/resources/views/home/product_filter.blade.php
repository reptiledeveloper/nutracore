<?php

use App\Helpers\CustomHelper;

$sort_by = $_GET['sort_by'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$brand_id = $_GET['brand_id'] ?? '';

$categories = \App\Models\Category::where('status',1)->where('is_delete',0)->where('parent_id',0)->get();
$brands = \App\Models\Brand::where('status',1)->where('is_delete',0)->get();

?>

<style>
    .custom-col {
        flex: 0 0 20%;
        max-width: 20%;
    }

    @media (max-width: 768px) {
        .custom-col {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }


</style>
<style>
    .sort-by-product-area {
        position: relative;
        display: inline-block;
    }

    .sort-by-product-wrap {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .sort-by-dropdown-wrap span {
        font-weight: 600;
    }

    .sort-by-dropdown {
        position: absolute;
        top: 45px;
        right: 0;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        width: 180px;
        display: none;
        z-index: 999999;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .sort-by-dropdown.active {
        display: block;
    }

    .sort-by-dropdown ul {
        list-style: none;
        margin: 0;
        padding: 10px 12px;
    }

    .sort-by-dropdown ul li {
        padding: 6px 0;
    }

    .sort-by-dropdown ul li a {
        color: #333;
        text-decoration: none;
        font-size: 14px;
        display: block;
    }

    .sort-by-dropdown ul li a:hover {
        color: #007bff;
    }
</style>


<style>
    /* Desktop filter visible */
    .filter-desktop { display: block; margin-bottom: 15px; }
    .mobile-filter-btn { display: none; margin-bottom: 15px; }

    @media(max-width: 768px) {
        .filter-desktop { display: none; }
        .mobile-filter-btn { display: inline-block; }
    }

    /* Bottom Sheet */
    .bottom-sheet {
        position: fixed;
        left: 0;
        right: 0;
        bottom: -100%;
        background: #fff;
        border-radius: 16px 16px 0 0;
        box-shadow: 0px -4px 20px rgba(0,0,0,0.2);
        padding: 20px;
        transition: bottom 0.4s ease;
        z-index: 999999;
    }
    .bottom-sheet.open {
        bottom: 0;
    }
    .sheet-header {
        width: 60px;
        height: 5px;
        background: #ccc;
        border-radius: 10px;
        margin: 0 auto 15px auto;
    }
</style>
<button class="btn btn-dark mobile-filter-btn" id="openFilterSheet">
    <i class="fa fa-filter"></i> Filter
</button>



<!-- ============================= -->
<!-- ✅ DESKTOP FILTER (VISIBLE ON DESKTOP) -->
<!-- ============================= -->
<div class="filter-desktop">
    <form action="" method="GET" id="filterFormDesktop">
        <div class="d-flex gap-3">

            <select id="sortSelectDesktop" class="form-select" name="sort_by">
                <option value="" selected>Sort by</option>
                <option value="low_to_high" {{ $sort_by=='low_to_high'?'selected':'' }}>Price: Low → High</option>
                <option value="high_to_low" {{ $sort_by=='high_to_low'?'selected':'' }}>Price: High → Low</option>
            </select>

            <select id="categoryChangeDesktop" class="form-select" name="category_id">
                <option value="" selected>Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $category_id==$cat->id?'selected':'' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <select id="brandChangeDesktop" class="form-select" name="brand_id">
                <option value="" selected>Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ $brand_id==$brand->id?'selected':'' }}>
                        {{ $brand->brand_name }}
                    </option>
                @endforeach
            </select>

        </div>
    </form>
</div>




<!-- ============================= -->
<!-- ✅ MOBILE FILTER (ACCORDION) -->
<!-- ============================= -->
<div id="filterSheet" class="bottom-sheet">

    <div class="sheet-header"></div>
    <h5 class="text-center mb-3">Filters</h5>

    <form action="" method="GET" id="filterFormMobile">

        <div class="mb-3">
            <label>Sort By</label>
            <select id="sortSelectMobile" class="form-select" name="sort_by">
                <option value="" selected>Sort by</option>
                <option value="low_to_high" {{ $sort_by=='low_to_high'?'selected':'' }}>Price: Low → High</option>
                <option value="high_to_low" {{ $sort_by=='high_to_low'?'selected':'' }}>Price: High → Low</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Category</label>
            <select id="categoryChangeMobile" class="form-select" name="category_id">
                <option value="" selected>Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $category_id==$cat->id?'selected':'' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Brand</label>
            <select id="brandChangeMobile" class="form-select" name="brand_id">
                <option value="" selected>Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ $brand_id==$brand->id?'selected':'' }}>
                        {{ $brand->brand_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="button" class="btn btn-primary w-100" id="applyFilterBtn">
            Apply Filters
        </button>

    </form>
</div>
<script>
    // Open bottom sheet
    document.getElementById("openFilterSheet").addEventListener("click", function () {
        document.getElementById("filterSheet").classList.add("open");
    });

    // Close on outside click
    window.addEventListener("click", function(e){
        let sheet = document.getElementById("filterSheet");
        if(sheet.classList.contains("open") && !sheet.contains(e.target) && e.target.id !== "openFilterSheet"){
            sheet.classList.remove("open");
        }
    });

    // Desktop auto-submit
    function autoSubmitDesktop() {
        document.getElementById("filterFormDesktop").submit();
    }

    document.getElementById("sortSelectDesktop").addEventListener("change", autoSubmitDesktop);
    document.getElementById("categoryChangeDesktop").addEventListener("change", autoSubmitDesktop);
    document.getElementById("brandChangeDesktop").addEventListener("change", autoSubmitDesktop);

    // Mobile "Apply Filters"
    document.getElementById("applyFilterBtn").addEventListener("click", function () {
        document.getElementById("filterFormMobile").submit();
    });
</script>
