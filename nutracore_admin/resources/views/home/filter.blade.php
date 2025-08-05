@php
    $search = $_GET['search']??'';
@endphp

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <form action="" method="">
                <div class="card-body">
                    <div class="row">
                        @if(!empty($search_view))
                            <div class="col-md-4">
                                <label>Search</label>
                                <input type="text" class="form-control" value="{{$search??''}}" name="search">
                            </div>
                        @endif

                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <button class="btn btn-primary" type="submit">Search</button>
                            <a class="btn btn-danger"  href="">Reset</a>
                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
