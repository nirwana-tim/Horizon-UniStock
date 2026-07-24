<?php

namespace App\Http\Controllers;

use App\Exports\Templates\DpLunasTemplateExport;
use App\Exports\Templates\HakBarangTemplateExport;
use App\Exports\Templates\HargaTemplateExport;
use App\Exports\Templates\KatalogTemplateExport;
use App\Exports\Templates\MahasiswaTemplateExport;
use Illuminate\Http\Request;
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
            'penerimaan' => \App\Exports\Templates\StockReceiveTemplateExport::class,
            'stock_opname' => \App\Exports\Templates\StockOpnameTemplateExport::class,
        ];

        if (!isset($validTypes[$type])) {
            abort(404, 'Template tidak ditemukan.');
        }

        $exportClass = new $validTypes[$type]();

        return Excel::download($exportClass, "Template_Import_{$type}.xlsx");
    }
}
