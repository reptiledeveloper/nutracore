<?php

$basePath = dirname(__DIR__, 4) . '/images';
$folder_name = $basePath . '/' . $folder;
//if (!empty($folder_name)) {
//    $iterator = new RecursiveIteratorIterator(
//        new RecursiveDirectoryIterator($folder_name, FilesystemIterator::SKIP_DOTS)
//    );
//    $files = [];
//    foreach ($iterator as $file) {
//        if ($file->isFile()) {
//            $files[] = $file;
//        }
//    }
//}

?>

<style>
    .image-container {
        position: relative;
        display: inline-block;
        border: 3px solid #333;
        padding: 5px;
        border-radius: 8px;
    }
    .image-container img {
        width: 140px; /* Adjust as needed */
        height: 100px;
        display: block;
        border-radius: 5px;
    }
    .checkbox {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
</style>


<div class="form-group col-md-2 mt-3">
    <div class="form-group mb-0 mt-3 justify-content-end">
        <button type="button" class="btn btn-primary mt-2" onclick="open_exist_image('')">Choose image</button>
    </div>
</div>


<div class="modal fade" id="open_image_modal"
     tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Choose Image</h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label>Search</label>
                        <input type="text" class="form-control" name="" onkeyup="searchImage(this.value)">
                    </div>


                </div>
                <div class="row" id="search_result_image">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger"
                        data-bs-dismiss="modal">Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function open_exist_image() {


        $('#open_image_modal').modal('show');
    }
</script>
<?php if($select_type == 'single'){?>
<script>
    document.querySelectorAll(".checkbox").forEach(checkbox => {
        checkbox.addEventListener("change", function() {
            document.querySelectorAll(".checkbox").forEach(cb => {
                if (cb !== this) cb.checked = false;
            });
        });
    });
</script>
<?php }?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    // A $( document ).ready() block.
    $( document ).ready(function() {
        searchImage('');
    });
    function searchImage(search){
        var _token = '{{ csrf_token() }}';
        var folder= '{{$folder}}';
        $.ajax({
            url: "{{ route('admin.search_image') }}",
            type: "POST",
            data: {search: search,folder:folder},
            dataType: "HTML",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {
                $('#search_result_image').html(resp);
            }
        });
    }
</script>

