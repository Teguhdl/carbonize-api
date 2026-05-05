<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\BaseController;

class TransportModeController extends BaseController
{
    /**
     * GET /transport/modes
     *
     * Mengembalikan daftar mode transportasi yang tersedia.
     * FE menggunakan ini untuk menampilkan pilihan pertama kepada user.
     */
    public function index()
    {
        $modes = [
            [
                'mode'        => 'private',
                'label'       => 'Kendaraan Pribadi',
                'description' => 'Gunakan kendaraan pribadi seperti motor atau mobil',
                'icon'        => 'car',
            ],
            [
                'mode'        => 'public',
                'label'       => 'Transportasi Umum',
                'description' => 'Gunakan bus, MRT, angkot, atau ojek online',
                'icon'        => 'bus',
            ],
        ];

        return $this->success($modes, 'Daftar mode transportasi berhasil diambil');
    }
}
