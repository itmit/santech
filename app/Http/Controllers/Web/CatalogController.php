<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Node;
use App\Models\NodeItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;
use SplFileInfo;

class CatalogController extends Controller
{
    public function uploadCatalog()
    {
        $path = storage_path() . '/app/temp';
        if (file_exists($path)) {
            foreach (glob($path.'/*') as $file) {
                unlink($file);
            }
        }

        $path = storage_path() . '/app/upload';
        if (file_exists($path)) {
            foreach (glob($path.'/*') as $file) {
                if(is_dir($file))
                {
                    foreach(scandir($file) as $p) if (($p!='.') && ($p!='..'))
                    unlink($file.DIRECTORY_SEPARATOR.$p);
                    // return rmdir($file);
                }
                else
                {
                    unlink($file);
                }
            }
        }

        $zip = new ZipArchive;
        $res = $zip->open($path);
        if ($res === TRUE) {
            $zip->extractTo(storage_path() . '/app/catalog_upload');
            $zip->close();
            // $import = self::storeSusliksFromZip();
        }
        else return 'false';
        return 'true';
    }
}
