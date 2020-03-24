<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Node;
use App\Models\NodeItem;
use App\Models\Item;
use App\Models\Category;
use App\Models\Catalog;
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
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\MyReadFilter;
use ZipArchive;
use SplFileInfo;

class CatalogController extends Controller
{
    public function uploadCatalog(Request $data)
    {
        $validator = Validator::make($data->all(), [
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('home')
                ->withErrors($validator)
                ->withInput();
        }

        $path = storage_path() . '/app/temp';
        if (file_exists($path)) {
            foreach (glob($path.'/*') as $file) {
                unlink($file);
            }
        }

        $path = storage_path() . '/app/catalog_upload';
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

        $file = $data->file('file');
        $path = storage_path() . '/app/' . $file->store('temp');
        $zip = new ZipArchive;
        $res = $zip->open($path);
        if ($res === TRUE) {
            $zip->extractTo(storage_path() . '/app/catalog_upload');
            $zip->close();
            $import = self::storeCatalogFromZip();
        }
        else return 'false';
        dd($import);
    }

    public function storeCatalogFromZip()
    {
        $files = scandir(storage_path() . '/app/catalog_upload');
        foreach($files as $file)
        {
            $fileType = new SplFileInfo($file);

            $reader = new Xlsx();
            $reader->setReadDataOnly(true);
           
            if($fileType->getExtension() == "xlsx")
            {
                $url = storage_path() . '/app/catalog_upload/' . $file;
                $spreadsheet = $reader->load($url);

                $cells = $spreadsheet->getActiveSheet()->getCellCollection();
                        
                $result = [];
                $position = [];

                $row1 = 1;
                $col1 = 'A';
                $row2 = 1;
                $col2 = 'B';
                if(!Catalog::where('name', $cells->get($col1.$row1)->getValue())->exists())
                {
                    $catalog = Catalog::create([
                        'uuid' => Str::uuid(),
                        'name' => $cells->get($col1.$row1)->getValue(),
                        'photo' => '/storage/catalog/'.$cells->get($col2.$row2)->getValue()
                    ]);

                    foreach($files as $categoryImage)
                    { 
                        $imageName = new SplFileInfo($categoryImage);
                        if($imageName->getFilename() == $cells->get($col2.$row2)->getValue())
                        {
                            $imageExtension = $imageName->getExtension();
                            $urlImage = storage_path() . '/app/catalog_upload/' . $imageName;

                            if (file_exists($urlImage))
                            {
                                copy($urlImage, storage_path() . '/app/public/catalog/' . $imageName);
                            }                          
                        }
                    }
                }
                else
                {
                    $catalog = Catalog::where('name', $cells->get($col1.$row1)->getValue())->first();
                    foreach($files as $categoryImage)
                    { 
                        $imageName = new SplFileInfo($categoryImage);
                        if($imageName->getFilename() == $cells->get($col2.$row2)->getValue())
                        {
                            $imageExtension = $imageName->getExtension();
                            $urlImage = storage_path() . '/app/catalog_upload/' . $imageName;
                            return $urlImage;

                            if (file_exists($urlImage))
                            {
                                copy($urlImage, storage_path() . '/app/public/catalog/' . $imageName);
                            }                          
                        }
                    }
                }

                for ($row = 2; $row <= $cells->getHighestRow(); $row++){
                    for ($col = 'A'; $col <= 'D'; $col++) {
                        // if($cells->get($col.$row) == null) $position[$col] = null;
                        if($cells->get($col.$row) == null) break 2;
                        else $position[$col] = $cells->get($col.$row)->getValue();
                    }
                    $result[$row] = $position;
                    $position = [];
                }   
                
                foreach($result as $item)
                {
                    if(!Category::where('name', '=', $item['A'])->where('catalog_id', $catalog->id)->exists())
                    {
                        $category = Category::create([
                            'catalog_id' => $catalog->id,
                            'uuid' => (string) Str::uuid(),
                            'name' => $item['A'],
                            'photo' => '/storage/catalog/category/'.$item['B'],
                        ]);
                    }
                    else $category = Category::where('name', $item['A'])->where('catalog_id', $catalog->id)->first();

                    if(!Item::where('name', '=', $item['C'])->where('category_id', $category->id)->exists())
                    {
                        Item::create([
                            'category_id' => $category->id,
                            'uuid' => (string) Str::uuid(),
                            'name' => $item['C'],
                            'photo' => '/storage/catalog/category/item/'.$item['D'],
                        ]);
                    }

                    foreach($files as $categoryImage)
                    { 
                        $imageName = new SplFileInfo($categoryImage);
                        if($imageName->getFilename() == $item['B'])
                        {
                            $imageExtension = $imageName->getExtension();
                            $urlImage = storage_path() . '/app/catalog_upload/' . $imageName;

                            if (file_exists($urlImage))
                            {
                                copy($urlImage, storage_path() . '/app/public/catalog/category/' . $imageName);
                            }                          
                        }
                        if($imageName->getFilename() == $item['D'])
                        {
                            $imageExtension = $imageName->getExtension();
                            $urlImage = storage_path() . '/app/catalog_upload/' . $imageName;

                            if (file_exists($urlImage))
                            {
                                copy($urlImage, storage_path() . '/app/public/catalog/category/item/' . $imageName);
                            }                          
                        }
                    }
                }
            }      
        }

        $path = storage_path() . '/app/temp';
        if (file_exists($path)) {
            foreach (glob($path.'/*') as $file) {
                unlink($file);
            }
        }

        $path = storage_path() . '/app/catalog_upload';
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
        return true;
    }
}
