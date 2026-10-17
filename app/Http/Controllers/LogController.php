<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        return Log::query()
            ->when($request->since, fn ($q) => $q->where('id', '>', $request->since))
            ->orderBy('id')
            ->limit(100)
            ->get();
    }

    public function ultimo()
    {
        return Log::latest('id')->firstOrFail();
    }
}