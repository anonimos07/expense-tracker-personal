<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    private function owned(Request $request, int $id): Wallet
    {
        return $request->user()->wallets()->withBalance()->findOrFail($id);
    }

    public function index(Request $request)
    {
        $wallets = $request->user()->wallets()->withBalance()->orderBy('name')->get();

        return WalletResource::collection($wallets)->additional([
            'total_balance' => round($wallets->sum('balance'), 2),
        ]);
    }

    public function store(WalletRequest $request)
    {
        $wallet = $request->user()->wallets()->create($request->validated());

        return (new WalletResource($this->owned($request, $wallet->id)))
            ->response()->setStatusCode(201);
    }

    public function show(Request $request, int $id)
    {
        return new WalletResource($this->owned($request, $id));
    }

    public function update(WalletRequest $request, int $id)
    {
        $wallet = $request->user()->wallets()->findOrFail($id);
        $wallet->update($request->validated());

        return new WalletResource($this->owned($request, $id));
    }

    public function destroy(Request $request, int $id)
    {
        $wallet = $request->user()->wallets()->findOrFail($id);

        if ($wallet->transactions()->exists()) {
            return response()->json([
                'message' => 'This wallet has transactions. Delete or move them first.',
            ], 409);
        }

        if ($request->user()->wallets()->count() <= 1) {
            return response()->json([
                'message' => 'You must keep at least one wallet.',
            ], 409);
        }

        $wallet->delete();

        return response()->noContent();
    }
}