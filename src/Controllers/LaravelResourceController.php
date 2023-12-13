<?php

namespace Da\QrCode\Controllers;

use Da\QrCode\Factory\LaravelQrCodeFactory;
use Illuminate\Http\Request;
use Exception;

final class LaravelResourceController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws Exception
     * @throws \Da\QrCode\Exception\ValidationException
     */
    public function __invoke(Request $request)
    {
        if (ini_get('output_buffering')) {
            ob_clean();
        }

        $data = $request->only([
            'content',
            'label',
            'margin',
            'size',
        ]);

        if (! isset($data['content'])) {
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
            $data['size'] ?? null,
            null,
            null,
            null,
            null,
            $data['label'] ?? null
        );

        return response($qrCode->writeString())
            ->withHeaders([
                'Content-Type' => $qrCode->getContentType(),
            ]);
    }
}
