<?php

namespace Da\QrCode\Controllers;

use Da\QrCode\Factory\LaravelQrCodeFactory;
use Illuminate\Http\Request;
use Exception;

final class LaravelResourceController
{
    public function __invoke(Request $request)
    {
        $data = $request->only([
            'content',
            'margin',
            'size',
        ]);

        if (is_null($data['content'])) {
            throw new Exception('The param `content` is required');
        }

        $qrCode = LaravelQrCodeFactory::make(
            $data['content'],
            null,
            null,
            null,
            null,
            null,
            null,
            $data['margin'] ?? null,
            $data['size'] ?? null
        );

        return response($qrCode->writeString())
            ->withHeaders([
                'Content-Type' => $qrCode->getContentType(),
            ]);
    }
}