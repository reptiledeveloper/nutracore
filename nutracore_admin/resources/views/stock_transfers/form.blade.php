@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    ?>

    <div class="content ">

        <div class="mb-4">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <i class="bi bi-globe2 small me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page_heading}}</li>
                </ol>
            </nav>
        </div>
        @include('snippets.errors')
        @include('snippets.flash')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">{{$page_heading}}</div>
                            <?php if (request()->has('back_url')){
                                $back_url = request('back_url'); ?>
                            <div class="dropdown ms-auto">
                                <a href="{{ url($back_url) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>


                <div class="card mt-3">
                    <div class="card-body pt-0">
                        <form class="card-body" action="" method="post" accept-chartset="UTF-8"
                              enctype="multipart/form-data" role="form">
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-2">Items</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTransferRow()">+ Add Row</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="transferTable">
                                        <thead>
                                        <tr>
                                            <th>Product / Variant</th>
                                            <th>Batch</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Qty</th>
                                            <th>Remove</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                            </div>

                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>

    @php
        $stockMap = $stocks->map(function ($s) {
        return [
            'id'    => $s->id,
            'label' => implode(' ', array_filter([
                $s->product->name ?? '',
                $s->variant ? '- ' . $s->variant->unit : null,
                '(Batch: ' . ($s->batch_no ?? '-') .
                ', Qty: ' . ($s->quantity ?? 0) .
                ($s->expiry_date ? ', Exp: ' . $s->expiry_date : '') . ')'
            ])),
            'batch' => $s->batch_no
        ];
    })->values();
 @endphp
    <script>
        /* server-side JSON (safe) */
        const stockMap = @json($stockMap);
            console.log(stockMap);
        let tIndex = 0;

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function buildOptionsHtml() {
            let html = '';
            for (let i = 0; i < stockMap.length; i++) {
                const s = stockMap[i];
                html += '<option value="' + s.id + '">' + escapeHtml(s.label) + '</option>';
            }
            return html;
        }

        function addTransferRow(){
            const tb = document.querySelector('#transferTable tbody');
            const tr = document.createElement('tr');

            const options = buildOptionsHtml();

            // build row using string concatenation to avoid nested template issues
            tr.innerHTML =
                '<td>' +
                '<select class="form-select" name="items[' + tIndex + '][stock_id]" required onchange="syncBatch(this)">' +
                '<option value="">-- Select --</option>' + options +
                '</select>' +
                '</td>' +
                '<td><input class="form-control" name="items[' + tIndex + '][batch_display]" readonly></td>' +
                '<td><input class="form-control" name="items[' + tIndex + '][from_location]" placeholder="From location" required></td>' +
                '<td><input class="form-control" name="items[' + tIndex + '][to_location]" placeholder="To location" required></td>' +
                '<td><input type="number" min="1" class="form-control" name="items[' + tIndex + '][quantity]" required></td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest(\'tr\').remove()">X</button></td>';

            tb.appendChild(tr);
            tIndex++;
        }

        function syncBatch(sel){
            const tr = sel.closest('tr');
            const batchInput = tr.querySelector('input[name$="[batch_display]"]');
            const s = stockMap.find(x => String(x.id) === String(sel.value));
            batchInput.value = s ? (s.batch || '-') : '';
        }

        addTransferRow();
    </script>

@endsection
