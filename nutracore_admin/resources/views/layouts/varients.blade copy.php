<div class="row">
    <div class="d-md-flex gap-4 align-items-center">
        <h3 class="d-none d-md-flex">Varients</h3>
        <div class="dropdown ms-auto">
            <button onclick="add_more()" class="btn btn-primary add_button"
                    type="button"><i class="fa fa-plus" aria-hidden="true"></i>
            </button>
        </div>
    </div>





    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="products">
            <thead>
            <tr>
                <th>Size</th>
                <th>Flavour</th>
                <th>Images</th>
                <th>MRP</th>
                <th>Selling Price</th>
                <th>Subscription Price</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody class="field_wrapper">
            @if(!empty($varients) && count($varients) >0)
                @foreach($varients as $key=> $varient)
                    <input type="hidden" value="{{$varient->id}}" name="varient_id[]">
                    <tr>
                        <td><input type="text" class="form-control" placeholder="Size" value="{{$varient->unit??''}}"
                                   name="unit[]"></td>
                        <td><input type="text" class="form-control" placeholder="Flavour" value="{{$varient->unit_value??''}}"
                                   name="unit_value[]"></td>
                                  
                        <td>
                            <input type="file" placeholder="MRP" class="form-control" value=""
                                   name="varient_images[]">
                        </td>
                        <td><input type="text" placeholder="MRP" class="form-control" value="{{$varient->mrp??''}}"
                                   name="mrp[]"></td>
                        <td><input type="text" placeholder="Selling Price" class="form-control"
                                   value="{{$varient->selling_price??''}}"
                                   name="selling_price[]"></td>
                        <td><input type="text" placeholder="Subscription Price" class="form-control"
                                   value="{{$varient->subscription_price??''}}"
                                   name="subscription_price[]"></td>
                        <td>

                        </td>
                    </tr>
                @endforeach

            @else

                <tr>
                    <input type="hidden" value="0" name="varient_id[]">
                    <td><input type="text" class="form-control" placeholder="Size" value="" name="unit[]"></td>
                     <td><input type="text" class="form-control" placeholder="Flavour" value=""
                                   name="unit_value[]"></td>
                    <td>
                        <input type="file" placeholder="MRP" class="form-control" value=""
                               name="varient_images[]">
                    </td>
                    <td><input type="text" class="form-control" placeholder="MRP" value="" name="mrp[]"></td>
                    <td><input type="text" class="form-control" value=""
                               name="selling_price[]" placeholder="Selling Price"></td>
                    <td><input type="text" class="form-control" value=""
                               name="subscription_price[]" placeholder="Subscription Price"></td>
                    <td>

                    </td>
                </tr>

            @endif

            </tbody>
        </table>
    </div>


</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
    function add_more() {
        const table = document.getElementById('products').getElementsByTagName('tbody')[0];
        const newRow = table.insertRow();
        const cell1 = newRow.insertCell(0);
        const cell2 = newRow.insertCell(1);
        const cell3 = newRow.insertCell(2);
        const cell4 = newRow.insertCell(3);
        const cell5 = newRow.insertCell(4);
        const cell6 = newRow.insertCell(5);
        const cell7 = newRow.insertCell(6);
        cell1.innerHTML = '<input type="hidden" value="0" name="varient_id[]"> <input type="text" placeholder="Size" class="form-control" value="" name="unit[]">';
        cell2.innerHTML = ' <td><input type="text" class="form-control" placeholder="Flavour" value="" name="unit_value[]"></td>';
        cell3.innerHTML = '<input type="file" placeholder="" class="form-control" value="" name="varient_images[]">';
        cell4.innerHTML = '<input type="text" placeholder="MRP" class="form-control" value="" name="mrp[]">';
        cell5.innerHTML = '<input type="text" placeholder="Selling Price" class="form-control" value="" name="selling_price[]">';
        cell6.innerHTML = '<input type="text" placeholder="Subscription Price" class="form-control" value="" name="subscription_price[]">';
        const deleteButton = document.createElement('button');
        deleteButton.innerHTML = '<i class="fa fa-minus" aria-hidden="true"></i>';
        deleteButton.className = 'btn btn-primary';
        deleteButton.onclick = function () {
            table.deleteRow(newRow.rowIndex - 1); // -1 to adjust for header row
        };
        cell7.appendChild(deleteButton);
        deleteButton.onclick = function () {
            table.deleteRow(newRow.rowIndex - 1); // -1 to adjust for header row
        };
    }
</script>
