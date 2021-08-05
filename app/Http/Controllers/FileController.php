<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('file');

        $name = $file->getClientOriginalName();

        Storage::put($name, File::get($file));

        $extension = $file->getClientOriginalExtension();

        if ($extension == 'docx' || $extension == 'doc' || $extension == 'pptx' || $extension == 'ppt' || $extension == 'xlsx' || $extension == 'xlsm') {
            $scriptsExec = [
                'doc' =>  base_path('scripts').'\\word.py -pf "'.Storage::path($name).'"',
                'docx' => base_path('scripts').'\\word.py -pf "'.Storage::path($name).'"',
                'xlsx' => base_path('scripts').'\\excel.py -pf "'.Storage::path($name).'"',
                'xlsm' => base_path('scripts').'\\excel.py -pf "'.Storage::path($name).'"',
                'ppt' =>  base_path('scripts').'\\power_point.py -pf "'.Storage::path($name).'"',
                'pptx' => base_path('scripts').'\\power_point.py -pf "'.Storage::path($name).'"',
            ];
            
            system("cmd /c ".$scriptsExec[$extension]);
        }
        
        Storage::delete($name);

        $name = explode('.', $name)[0];
        
        return response()->json([
            'name' => $name . '.pdf',
        ], Response::HTTP_CREATED);
    }

    /**
     * Download the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download ($name)
    {
        $url = Storage::path($name);
        
        if (Storage::exists($name)) {
            return response()->download($url);
        }

        abort(404);
    }
}
