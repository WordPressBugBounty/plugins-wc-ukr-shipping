<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Http\Controllers;

use kirillbdev\WCUSCore\Http\Controller;
use kirillbdev\WCUSCore\Http\Request;

class FeedbackController extends Controller
{
    private const FEEDBACK_API_URL = 'https://kirillbdev.pro/api/v1/feedbacks';

    public function store(Request $request)
    {
        wp_remote_post(self::FEEDBACK_API_URL, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 5,
            'body' => json_encode([
                'product' => 'wc-ukr-shipping',
                'reason' => $request->get('reason'),
                'message' => $request->get('message'),
            ]),
        ]);

        return $this->jsonResponse([
            'success' => true
        ]);
    }
}
