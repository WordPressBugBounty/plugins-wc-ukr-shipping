<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Contracts\Shipping;

use kirillbdev\WCUkrShipping\Dto\Shipping\PUDO;
use kirillbdev\WCUkrShipping\Dto\Shipping\SearchPUDOByGeoRequestDTO;

/**
 * Implemented only by providers able to search pickup points by coordinates.
 * Carrier directories stored locally have no coordinates, so they stay out of it.
 */
interface GeoPUDOProviderInterface
{
    /**
     * @param SearchPUDOByGeoRequestDTO $request
     * @return PUDO[]
     */
    public function searchPUDOByGeo(SearchPUDOByGeoRequestDTO $request): array;
}
