<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class CertificateController extends Controller
{
    use ApiResponseTrait;
    use AuthorizesRequests;

    public function show(Certificate $certificate): JsonResponse
    {
        $this->authorize('view', $certificate);
        return $this->successResponse(
            new CertificateResource($certificate),
            'Certificate retrieved successfully.'
        );
    }
}
