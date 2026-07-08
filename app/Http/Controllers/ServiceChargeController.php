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
        $categories = \App\Models\Building::CATEGORIES;

        // Per-category totals (only active charges)
        $totalsByCategory = [];
        foreach ($categories as $key => $label) {
            $totalsByCategory[$key] = $charges->where('building_category', $key)->where('is_active', true)->sum('amount');
        }

        return view('admin.service-charges.index', compact('charges', 'categories', 'totalsByCategory'));
    }

    /**
     * Store a new service charge.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:150'],
            'building_category' => ['required', 'in:tin_shed,below_or_equal_4_floor,above_4_floor,shop'],
            'amount'            => ['required', 'integer', 'min:0'],
            'charge_type'       => ['required', 'in:per_family,per_floor,fixed'],
            'description'       => ['nullable', 'string', 'max:500'],
            'is_active'         => ['nullable', 'boolean'],
            'sort_order'        => ['nullable', 'integer', 'min:0'],
        ]);

        ServiceCharge::create([
            'name'              => $validated['name'],
            'building_category' => $validated['building_category'],
            'amount'            => $validated['amount'],
            'charge_type'       => $validated['charge_type'],
            'description'       => $validated['description'] ?? null,
            'is_active'         => $request->boolean('is_active', true),
            'sort_order'        => $validated['sort_order'] ?? 0,
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
            'name'              => ['required', 'string', 'max:150'],
            'building_category' => ['required', 'in:tin_shed,below_or_equal_4_floor,above_4_floor,shop'],
            'amount'            => ['required', 'integer', 'min:0'],
            'charge_type'       => ['required', 'in:per_family,per_floor,fixed'],
            'description'       => ['nullable', 'string', 'max:500'],
            'is_active'         => ['nullable', 'boolean'],
            'sort_order'        => ['nullable', 'integer', 'min:0'],
        ]);

        $serviceCharge->update([
            'name'              => $validated['name'],
            'building_category' => $validated['building_category'],
            'amount'            => $validated['amount'],
            'charge_type'       => $validated['charge_type'],
            'description'       => $validated['description'] ?? null,
            'is_active'         => $request->boolean('is_active', false),
            'sort_order'        => $validated['sort_order'] ?? 0,
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
