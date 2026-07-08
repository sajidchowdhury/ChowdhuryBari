<?php

namespace App\Http\Controllers;

use App\Models\ServiceCharge;
use Illuminate\Http\Request;

class ServiceChargeController extends Controller
{
    /**
     * List all service charges (admin).
     */
    public function index()
    {
        $charges = ServiceCharge::orderBy('sort_order')->orderBy('name')->get();
        $total = $charges->where('is_active', true)->sum('amount');

        return view('admin.service-charges.index', compact('charges', 'total'));
    }

    /**
     * Store a new service charge.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'amount'      => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        ServiceCharge::create([
            'name'        => $validated['name'],
            'amount'      => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active', true),
            'sort_order'  => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.service-charges.index')
            ->with('status', "সেবা '{$validated['name']}' যোগ করা হয়েছে।");
    }

    /**
     * Update an existing service charge.
     */
    public function update(Request $request, ServiceCharge $serviceCharge)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'amount'      => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $serviceCharge->update([
            'name'        => $validated['name'],
            'amount'      => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active', false),
            'sort_order'  => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.service-charges.index')
            ->with('status', "সেবা '{$serviceCharge->name}' আপডেট হয়েছে।");
    }

    /**
     * Delete a service charge.
     */
    public function destroy(ServiceCharge $serviceCharge)
    {
        $name = $serviceCharge->name;
        $serviceCharge->delete();

        return redirect()->route('admin.service-charges.index')
            ->with('status', "সেবা '{$name}' মুছে ফেলা হয়েছে।");
    }
}
