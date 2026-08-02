<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Family reduction applications — a building owner requests that their
 * monthly billing family count be reduced because some families have left.
 *
 * Flow:
 *   1. Member sees their building's flats + meters (read-only) in "আমার বাড়ি"
 *   2. Member identifies vacant flats (meters with no recent recharge)
 *   3. Member submits an application: "I have X families now (was Y), please reduce billing"
 *   4. Admin reviews in "অ্যাপ্লিকেশন" menu — checks meter on BPDB (external link)
 *   5. Admin approves → building.billing_family_count = requested count
 *   6. Billing stays at the approved count until admin changes it manually
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_reduction_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('current_family_count');      // what admin currently expects
            $table->unsignedInteger('requested_family_count');    // what member says is actual now
            $table->json('vacant_flat_ids')->nullable();          // flats the member says are vacant
            $table->text('reason')->nullable();                   // member's explanation

            $table->string('status')->default('pending');         // pending / approved / rejected
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
            $table->index(['building_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_reduction_applications');
    }
};
