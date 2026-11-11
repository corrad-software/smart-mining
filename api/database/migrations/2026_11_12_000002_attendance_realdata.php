<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // enrich employees with the real staff fields
        Schema::table('employees', function (Blueprint $t) {
            $t->string('employee_id')->nullable()->index();   // KS0002 …
            $t->integer('workdays')->nullable();
            $t->string('shift')->nullable();
            $t->string('hq_region')->nullable();              // hq | northern | southern
        });

        // the daily geo/clock attendance collection (separate from F001 report_attendances)
        Schema::create('attendances', function (Blueprint $t) {
            $t->id();
            $t->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $t->date('date')->nullable()->index();
            $t->string('status')->nullable();                 // present|late|absent|on_leave
            $t->time('timeclock_in')->nullable();
            $t->time('timeclock_out')->nullable();
            $t->decimal('lat', 12, 6)->nullable();
            $t->string('legacy_id')->nullable()->index();
            $t->timestamps();
            $t->index(['date', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::table('employees', function (Blueprint $t) {
            $t->dropColumn(['employee_id', 'workdays', 'shift', 'hq_region']);
        });
    }
};
