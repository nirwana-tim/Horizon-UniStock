<?php

namespace App\Http\Controllers;

use App\Exports\Templates\DpLunasTemplateExport;
use App\Exports\Templates\HakBarangTemplateExport;
use App\Exports\Templates\HargaTemplateExport;
use App\Exports\Templates\KatalogTemplateExport;
use App\Exports\Templates\MahasiswaTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class TemplateController extends Controller
{
    public function download(Request $request, string $type)
    {
        $validTypes = [
            'mahasiswa' => MahasiswaTemplateExport::class,
            'dp_lunas' => DpLunasTemplateExport::class,
            'katalog' => KatalogTemplateExport::class,
            'harga' => HargaTemplateExport::class,
            'hak_barang' => HakBarangTemplateExport::class,
        ];

        if (!isset($validTypes[$type])) {
            abort(404, 'Template tidak ditemukan.');
        }

        $staticPath = "templates/import_{$type}.xlsx";

        if (Storage::disk('local')->exists($staticPath)) {
            return Storage::disk('local')->download($staticPath, "Template_Import_{$type}.xlsx");
        }

        $exportClass = new $validTypes[$type]();

        return Excel::download($exportClass, "Template_Import_{$type}.xlsx");
    }
}
