<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\TrelloReportController;
use App\Http\Controllers\ReportController;
class ReportDispatcherController extends Controller
{
    protected function getController()
    {
        return match (auth()->user()->service) {
            'trello' => app(TrelloReportController::class),
            'linear' => app(ReportController::class),
            default => null,
        };
    }

    public function reports(Request $request)
    {
        $controller = $this->getController();
        if (!$controller) return response()->json(['message' => 'Invalid service'], 400);
        return $controller->reports($request);
    }

    public function getReportData(Request $request)
    {
        $controller = $this->getController();
        if (!$controller) return response()->json(['message' => 'Invalid service'], 400);
        return $controller->getReportData($request);
    }

    public function getAnalysis(Request $request)
    {
        $controller = $this->getController();
        if (!$controller) return response()->json(['message' => 'Invalid service'], 400);
        return $controller->getAnalysis($request);
    }

    public function exportReport(Request $request)
    {
        $controller = $this->getController();
        if (!$controller) return response()->json(['message' => 'Invalid service'], 400);
        return $controller->exportReport($request);
    }
}