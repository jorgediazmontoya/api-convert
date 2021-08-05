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

        $extension = $file->getClientOriginalExtension();
        $randomName = uniqid().".{$extension}";

        Storage::put($randomName, File::get($file));

        if ($extension == 'docx' || $extension == 'doc' || $extension == 'pptx' || $extension == 'ppt' || $extension == 'xlsx' || $extension == 'xlsm') {
            $scriptsExec = [
                'doc' =>  base_path('scripts').'\\word.py -pf "'.Storage::path($randomName).'"',
                'docx' => base_path('scripts').'\\word.py -pf "'.Storage::path($randomName).'"',
                'xlsx' => base_path('scripts').'\\excel.py -pf "'.Storage::path($randomName).'"',
                'xlsm' => base_path('scripts').'\\excel.py -pf "'.Storage::path($randomName).'"',
                'ppt' =>  base_path('scripts').'\\power_point.py -pf "'.Storage::path($randomName).'"',
                'pptx' => base_path('scripts').'\\power_point.py -pf "'.Storage::path($randomName).'"',
            ];

            system("cmd /c ".$scriptsExec[$extension]);
        }

        Storage::delete($randomName);

        $randomName = explode('.', $randomName)[0];

        return response()->json([
            'name' => $randomName . '.pdf',
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
