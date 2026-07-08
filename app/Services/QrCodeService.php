<?php

namespace App\Services;

use App\Models\Student;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;

class QrCodeService
{
    public function getQrPngDataUrl(Student $student, int $size = 300): string
    {
        $renderer = new GDLibRenderer($size, 4, 'png');
        $writer = new Writer($renderer);
        $png = $writer->writeString($student->nim, Encoder::DEFAULT_BYTE_MODE_ENCODING, ErrorCorrectionLevel::H());

        return 'data:image/png;base64,' . base64_encode($png);
    }
}
