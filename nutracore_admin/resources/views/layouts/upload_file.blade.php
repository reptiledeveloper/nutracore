@php
    $search = $_GET['search']??'';
    $category_id = $_GET['category_id']??'';
    $folder_name = $_GET['folder_name']??'';
    $current_url = url()->current();
    $categories = \App\Helpers\CustomHelper::getCategories();
@endphp

<style>
    .image-preview {
        padding: 5px;
        width: 100px;
        height: 100px;
        object-fit: fill;
        margin: 5px;
        border: 1px solid;
    }
</style>
<form action="" method="post" enctype="multipart/form-data" >
    <div class="row mb-3 mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5>Upload Files</h5>
                    <div class="row">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Select Folder</label>
                                <select class="form-control" name="folder_name">
                                    <option value="" selected>Select Folder</option>
                                    @foreach($folders as $folder)
                                        <option
                                            value="{{$folder??''}}" {{$folder == $folder_name?"selected":""}}>{{basename($folder)??''}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Select File</label>
                                <input type="file" value="" accept="image/*" class="form-control" id="fileInput" name="files[]"
                                       multiple>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <ul id="fileList"></ul>
                        </div>


                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">Upload</button>
                            <a href="{{$current_url}}" class="btn btn-danger">Reset</a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</form>

<script>
    document.getElementById('fileInput').addEventListener('change', function () {
        const fileList = document.getElementById('fileList');
        fileList.innerHTML = ''; // Clear previous file list

        const files = this.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            // Create a new FileReader object
            const reader = new FileReader();

            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result; // Set image source
                img.alt = file.name;
                img.classList.add('image-preview');
                fileList.appendChild(img);
            };

            reader.readAsDataURL(file); // Read the file as a data URL
        }
    });

</script>
