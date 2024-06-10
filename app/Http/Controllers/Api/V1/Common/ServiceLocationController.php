<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Models\Admin\ServiceLocation;
use App\Http\Controllers\ApiController;
use App\Transformers\ServiceLocationTransformer;
use Illuminate\Http\Request;

/**
 * @group ServiceLocations
 *
 * Get ServiceLocatons
 */
class ServiceLocationController extends ApiController
{
    /**
     * Get all the ServiceLocatons.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $servicelocationsQuery  = ServiceLocation::active()->companyKey();

      $serviceLocations = $servicelocationsQuery->with('zones')->get();       

        return $this->respondOk($serviceLocations);
    }




public function seatBySeatCity()
    {
        $servicelocationsQuery = ServiceLocation::active()->companyKey();
    
        // Filter service locations for Islamabad and Lahore
        $serviceLocations = $servicelocationsQuery
            ->whereIn('seat_status', ['active'])
            ->get();
    
        return $this->respondOk($serviceLocations);
    }
}
