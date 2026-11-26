<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $f = $request->validate([
            'type'        => ['nullable', Rule::in(['expense', 'income'])],
            'wallet_id'   => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'from'        => ['nullable', 'date'],
            'to'          => ['nullable', 'date', 'after_or_equal:from'],
            'search'      => ['nullable', 'string', 'max:100'],
            'per_page'    => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $transactions = $request->user()->transactions()
            ->with(['wallet:id,name,type', 'category:id,name,type,color,icon'])
            ->when($f['type'] ?? null,        fn ($q, $v) => $q->where('type', $v))
            ->when($f['wallet_id'] ?? null,   fn ($q, $v) => $q->where('wallet_id', $v))
            ->when($f['category_id'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
            ->when($f['from'] ?? null,        fn ($q, $v) => $q->whereDate('date', '>=', $v))
            ->when($f['to'] ?? null,          fn ($q, $v) => $q->whereDate('date', '<=', $v))
            ->when($f['search'] ?? null,      fn ($q, $v) => $q->where('description', 'like', "%{$v}%"))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($f['per_page'] ?? 20);

        return TransactionResource::collection($transactions);
    }

    public function store(TransactionRequest $request)
    {
        $transaction = $request->user()->transactions()->create($request->validated());

        return (new TransactionResource($transaction->load(['wallet', 'category'])))
            ->response()->setStatusCode(201);
    }

    public function show(Request $request, int $id)
    {
        $transaction = $request->user()->transactions()
            ->with(['wallet', 'category'])->findOrFail($id);

        return new TransactionResource($transaction);
    }

    public function update(TransactionRequest $request, int $id)
    {
        $transaction = $request->user()->transactions()->findOrFail($id);
        $transaction->update($request->validated());

        return new TransactionResource($transaction->load(['wallet', 'category']));
    }

    public function destroy(Request $request, int $id)
    {
        $request->user()->transactions()->findOrFail($id)->delete();

        return response()->noContent();
    }

    public function summary(Request $request)
    {
        $data = $request->validate(['month' => ['nullable', 'date_format:Y-m']]);

        $start = Carbon::parse(($data['month'] ?? now()->format('Y-m')) . '-01')->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $base = $request->user()->transactions()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()]);

        $totals = (clone $base)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $income  = (float) ($totals['income'] ?? 0);
        $expense = (float) ($totals['expense'] ?? 0);

        $byCategory = (clone $base)
            ->where('type', 'expense')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category:id,name,color')
            ->get()
            ->map(fn ($row) => [
                'category_id' => $row->category_id,
                'name'        => $row->category?->name ?? 'Uncategorized',
                'color'       => $row->category?->color ?? '#6b7280',
                'total'       => (float) $row->total,
            ])
            ->sortByDesc('total')
            ->values();

        return response()->json([
            'month'               => $start->format('Y-m'),
            'income'              => $income,
            'expense'             => $expense,
            'net'                 => round($income - $expense, 2),
            'expense_by_category' => $byCategory,
        ]);
    }
}