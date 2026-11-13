<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Http\Controllers;

use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\SmartyParcelErrorException;
use kirillbdev\WCUkrShipping\Services\SmartyParcel\AssistantService;
use kirillbdev\WCUSCore\Http\Contracts\ResponseInterface;
use kirillbdev\WCUSCore\Http\Controller;
use kirillbdev\WCUSCore\Http\Request;

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * The host side of the help assistant widget: the key stays here, the browser gets only tokens.
 */
class AssistantController extends Controller
{
    private AssistantService $assistantService;

    public function __construct(AssistantService $assistantService)
    {
        $this->assistantService = $assistantService;
    }

    public function connect(Request $request): ResponseInterface
    {
        try {
            // Only the version of the text the plugin shows is accepted, not one sent by the browser
            $this->assistantService->connect(WCUS_SMARTY_PARCEL_CONSENT_VERSION);

            return $this->jsonResponse([
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function token(Request $request): ResponseInterface
    {
        try {
            return $this->jsonResponse([
                'success' => true,
                'data' => $this->assistantService->getToken(),
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    private function errorResponse(\Throwable $e): ResponseInterface
    {
        return $this->jsonResponse([
            'success' => false,
            'error' => [
                'source' => $e instanceof SmartyParcelErrorException ? 'smarty_parcel' : 'internal',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);
    }
}
