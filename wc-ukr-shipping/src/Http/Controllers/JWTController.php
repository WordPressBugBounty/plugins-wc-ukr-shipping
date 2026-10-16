<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Http\Controllers;

use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\AccessKeyRequiredException;
use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\SmartyParcelErrorException;
use kirillbdev\WCUkrShipping\Services\SmartyParcel\AccessService;
use kirillbdev\WCUSCore\Http\Contracts\ResponseInterface;
use kirillbdev\WCUSCore\Http\Controller;
use kirillbdev\WCUSCore\Http\Request;

class JWTController extends Controller
{
    private AccessService $accessService;

    public function __construct(AccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    public function issueToken(Request $request): ResponseInterface
    {
        try {
            return $this->jsonResponse([
                'success' => true,
                'data' => $this->accessService->issueToken(get_current_user_id()),
            ]);
        } catch (AccessKeyRequiredException $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'code' => 'access_key_required',
                    'message' => $e->getMessage(),
                    'source' => 'internal',
                    'details' => [
                        'docs_url' => WCUS_DOCS_ACCESS_KEYS_URL,
                        'docs_label' => __('Learn more', 'wc-ukr-shipping'),
                    ],
                ]
            ]);
        } catch (SmartyParcelErrorException $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'source' => 'smarty_parcel',
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'details' => $e->getDetails(),
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'source' => 'internal'
                ]
            ]);
        }
    }
}
