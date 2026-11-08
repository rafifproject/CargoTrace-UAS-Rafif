<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GlobalIntelligenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicIntelligenceApiController extends Controller
{
    public function __construct(
        protected GlobalIntelligenceService $intelligenceService
    ) {}

    /**
     * GET /api/countries
     * Country Macroeconomic Profile from World Bank & REST Countries
     */
    public function countries(Request $request): JsonResponse
    {
        $code = strtoupper($request->input('code', 'ID'));
        $data = $this->intelligenceService->getCountryEconomy($code);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * GET /api/risk
     * Live Supply Chain Risk Index Calculation (Weighted Model)
     */
    public function risk(): JsonResponse
    {
        $risk = $this->intelligenceService->calculateSupplyChainRisk();

        return response()->json([
            'success' => true,
            'status' => 'success',
            'data' => $risk,
        ]);
    }

    /**
     * GET /api/ports
     * World Port Index Global Dataset
     */
    public function ports(Request $request): JsonResponse
    {
        $ports = $this->intelligenceService->getGlobalPorts();

        if ($search = $request->input('search')) {
            $ports = array_values(array_filter($ports, function ($p) use ($search) {
                return str_contains(strtolower($p['name']), strtolower($search))
                    || str_contains(strtolower($p['country']), strtolower($search))
                    || str_contains(strtolower($p['city']), strtolower($search))
                    || str_contains(strtolower($p['code']), strtolower($search));
            }));
        }

        return response()->json([
            'success' => true,
            'status' => 'success',
            'total' => count($ports),
            'data' => $ports,
        ]);
    }

    /**
     * GET /api/news
     * Logistics News with Lexicon Sentiment Analysis
     */
    public function news(Request $request): JsonResponse
    {
        $category = $request->input('category', 'all');
        $news = $this->intelligenceService->getLogisticsNews($category);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'category' => $category,
            'total' => count($news),
            'data' => $news,
        ]);
    }

    /**
     * GET /api/currency
     * Live Exchange Rates from ExchangeRate-API
     */
    public function currency(Request $request): JsonResponse
    {
        $base = strtoupper($request->input('base', 'USD'));
        $currencies = $this->intelligenceService->getCurrencies($base);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'data' => $currencies,
        ]);
    }
}
