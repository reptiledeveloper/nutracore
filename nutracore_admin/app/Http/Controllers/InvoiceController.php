<?php
namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Products;
use App\Models\Stock;
use App\Models\StockBatch;
use App\Models\Supplier;
use Attribute;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Auth;
use Validator;

use App\Models\Brand;
use App\Models\Attributes;

use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;



class InvoiceController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }



    public function index(Request $request)
    {
        $data = [];
        $search = $request->search ?? '';
        $invoices = Invoice::with('supplier')->where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
//            $invoices->where('name', 'like', '%' . $search . '%');
        }
        $invoices = $invoices->paginate(10);
        $data['invoices'] = $invoices;
        return view('invoices.index', $data);
    }


    public function add(Request $request)
    {
        $id = $request->id ?? 0;
        $invoice = $id ? Invoice::with('items')->find($id) : null;

        if ($id && !$invoice) {
            return redirect($this->ADMIN_ROUTE_NAME . '/invoices')
                ->with('alert-danger', 'Invoice not found.');
        }

        if ($request->isMethod('post')) {
            $back_url = $request->back_url ?? $this->ADMIN_ROUTE_NAME . '/invoices';

            $rules = [
                'supplier_id'        => 'required|exists:suppliers,id',
                'invoice_date'       => 'required|date',
                'product_id.*'       => 'required|exists:products,id',
                'variant_id.*'       => 'nullable|exists:product_varients,id',
                'batch.*'            => 'required|string|max:50',
                'mfg.*'              => 'required|date',
                'expiry.*'           => 'required|date|after:mfg.*',
                'qty.*'              => 'required|integer|min:1',
                'purchase_price.*'   => 'required|numeric|min:0',
            ];
            $request->validate($rules);

            $saved = $this->save($request, $id);

            if ($saved) {
                $msg = $id ? 'Invoice has been updated successfully.' : 'Invoice has been added successfully.';
                return redirect(url($back_url))->with('alert-success', $msg);
            }
            return back()->with('alert-danger', 'Something went wrong. Please try again.');
        }

        return view('invoices.form', [
            'page_heading' => $id ? 'Update Invoice' : 'Add Invoice',
            'id'           => $id,
            'invoice'      => $invoice,
            'suppliers'    => Supplier::all(),
            'products'     => Products::with('variants')->get(),
        ]);
    }


    public function save(Request $request, $id = 0)
    {
        DB::transaction(function () use ($request, $id, &$invoice) {
            // Save invoice header
            $invoice = $id ? Invoice::findOrFail($id) : new Invoice();
            $invoice->supplier_id  = $request->supplier_id;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->invoice_number = $request->invoice_number;
            $invoice->remarks      = $request->remarks ?? '';
            $invoice->save();

            // Remove old items if updating
            if ($id) {
                Stock::where('invoice_id', $invoice->id)->delete();
            }

            // Save each invoice item & batch
            foreach ($request->product_id as $index => $productId) {
                $item = new Stock();
                $item->invoice_id     = $invoice->id;
                $item->product_id     = $productId;
                $item->variant_id     = $request->variant_id[$index] ?? null;
                $item->batch_number   = $request->batch[$index];
                $item->mfg_date       = $request->mfg[$index];
                $item->expiry_date    = $request->expiry[$index];
                $item->quantity       = $request->qty[$index];
                $item->purchase_price = $request->purchase_price[$index];
                $item->save();

                // Create or update stock batch
                StockBatch::updateOrCreate(
                    [
                        'product_id'   => $productId,
                        'variant_id'   => $request->variant_id[$index] ?? null,
                        'batch_number' => $request->batch[$index],
                    ],
                    [
                        'mfg_date'     => $request->mfg[$index],
                        'expiry_date'  => $request->expiry[$index],
                        'quantity'     => DB::raw('quantity + ' . $request->qty[$index]),
                        'purchase_price' => $request->purchase_price[$index],
                    ]
                );
            }
        });

        return true;
    }




    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Invoice::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Invoice has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
