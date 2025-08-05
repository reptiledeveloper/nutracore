<?php
$feature_id = $id??'';
?>

<div class="row">
    @if($type == 'single')
        <div class="col-md-2 mt-2" >
            <div class="position-relative d-inline-block border rounded p-2">
                <!-- Delete Button -->
                <a href="{{$image??''}}" target="_blank"> <img
                                                                      src="{{$image??''}}" id="imageBox"
                                                                      class=" object-fit-cover rounded"
                                                                      alt="" height="50px"
                                                                      width="50px"></a>
            </div>
        </div>
    @else
        @if(!empty($images))
            @foreach($images as $image)



                <div class="col-md-2 mt-2" id="delete_{{$image->id??''}}">
                    <div class="position-relative d-inline-block border rounded p-2">
                        <!-- Delete Button -->
                        <button class="btn-close position-absolute top-0 end-0 m-1" type="button"
                                onclick="delete_image('{{$folder??''}}','{{$image->id??''}}','{{$image->image??''}}')"></button>
                        <!-- Image -->
                        <a href="{{$image->image??''}}" target="_blank"> <img style="margin-top: 20px;"
                                                                              src="{{$image->image??''}}" id="imageBox"
                                                                              class=" object-fit-cover rounded"
                                                                              alt="" height="50px"
                                                                              width="50px"></a>
                    </div>
                </div>
            @endforeach
        @endif

    @endif

</div>


<script>
    function delete_image(folder, id,image_name) {
        if(confirm('Are You Sure Want To Confirm?')){
            var _token = '{{ csrf_token() }}';
            var feature_id = '{{$feature_id??''}}';
            $.ajax({
                url: "{{ route('admin.delete_image') }}",
                type: "POST",
                data: {folder: folder, id: id,feature_id:feature_id,image_name:image_name},
                dataType: "HTML",
                headers: {'X-CSRF-TOKEN': _token},
                cache: false,
                success: function (resp) {
                    $('#delete_' + id).remove();
                }
            });
        }
    }
</script>
