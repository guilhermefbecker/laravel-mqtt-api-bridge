<?php

namespace App\Http\Controllers;

use App\Http\Resources\LogResource;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Log::query()
            ->when($request->filled('dispositivo'), fn ($q) => $q->where('dispositivo', 'like', '%' . $request->dispositivo . '%'))
            ->when($request->filled('cor'), fn ($q) => $q->where('cor', $request->cor))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->boolean('estado')))
            ->when($request->filled('ciclo'), fn ($q) => $q->where('ciclo', $request->boolean('ciclo')))
            ->when($request->filled('de'), fn ($q) => $q->whereDate('created_at', '>=', $request->de))
            ->when($request->filled('ate'), fn ($q) => $q->whereDate('created_at', '<=', $request->ate))
            ->orderByDesc('id')
            ->paginate(15);

        return LogResource::collection($logs);
    }

    public function ultimo()
    {
        return Log::latest('id')->firstOrFail();
    }
}