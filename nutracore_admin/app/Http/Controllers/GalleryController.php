<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Models\Blocks;
use App\Models\Company;
use Auth;
use DB;
use FilesystemIterator;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Storage;
use Validator;

class GalleryController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request)
    {
        $path = dirname(__DIR__, 4) . '/images/';
        $folders = File::directories($path);
        $data['folders'] = $folders;
        $basePath = dirname(__DIR__, 4) . '/images';
        $search = $request->search ?? '';
        $filesArr = [];
        $folder_name = $request->folder_name ?? '';
        $paginatedFiles = [];
        if (!empty($folder_name)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folder_name, FilesystemIterator::SKIP_DOTS)
            );

            $files = [];
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files[] = $file;
                }
            }

            // Apply search filter if needed
            if (!empty($search)) {
                $files = array_filter($files, function ($file) use ($search) {
                    return stripos($file->getFilename(), $search) !== false;
                });
            }

            // Pagination setup
            $perPage = 50; // Adjust per-page limit as needed
            $currentPage = request()->get('page', 1);
            $totalFiles = count($files);

            // Slice only required files for current page
            $currentPageItems = array_slice($files, ($currentPage - 1) * $perPage, $perPage);

            // Create paginator instance
            $paginatedFiles = new LengthAwarePaginator(
                $currentPageItems,
                $totalFiles,
                $perPage,
                $currentPage,
                ['path' => request()->url()]
            );
        }

        $data['files'] = $paginatedFiles;
        $data['folder_name'] = $folder_name;
        return view('gallery.index', $data);
    }

    public function add(Request $request)
    {
        $data = [];
        $method = $request->method();
        if($method == 'post' || $method == 'POST'){
            $rules = [];
            $rules['folder_name'] = 'required';
            $rules['files'] = 'required';
            $request->validate($rules);
            $files = $request->file('files');
            if(!empty($files)){
                foreach ($files as $file){
                    $success = CustomHelper::uploadImage($file,basename($request->folder_name));
                }
            }
            return back()->with('alert-success', 'Files Are Uploaded Successfully');
        }

        $path = dirname(__DIR__, 4) . '/images/';
        $folders = File::directories($path);
        $data['folders'] = $folders;
        $data['page_heading'] = 'Upload Gallery';
        return view('gallery.form', $data);
    }

    public function delete(Request $request)
    {
        $file_name = $request->file_name ?? '';
        unlink($file_name);
        return back();

    }

}
