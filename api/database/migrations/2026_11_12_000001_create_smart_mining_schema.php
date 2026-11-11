<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ---- master data ----
        Schema::create('regions', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->decimal('lat', 10, 6)->nullable();
            $t->decimal('lng', 10, 6)->nullable(); $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('contractors', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('site_types', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('legacy_id')->nullable()->index(); $t->timestamps(); });
        Schema::create('site_products', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('legacy_id')->nullable()->index(); $t->timestamps(); });
        Schema::create('equipment_item_types', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('code')->nullable(); $t->string('category')->nullable();
            $t->integer('display_order')->default(0); $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });

        // ---- users: extend Laravel users table ----
        Schema::table('users', function (Blueprint $t) {
            $t->string('username')->nullable()->index();
            $t->string('employee_id')->nullable();
            $t->string('position')->nullable();
            $t->string('company')->nullable();
            $t->boolean('blocked')->default(false);
            $t->boolean('confirmed')->default(true);
            $t->string('legacy_id')->nullable()->index();
        });

        // ---- sites ----
        Schema::create('sites', function (Blueprint $t) {
            $t->id();
            $t->string('name'); $t->string('site_code')->nullable()->index();
            $t->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('contractor_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('site_type_id')->nullable()->constrained()->nullOnDelete();
            $t->string('type')->nullable();               // e.g. Sand_Mining
            $t->string('product')->nullable();
            $t->string('supervisor')->nullable();
            $t->boolean('is_activated')->default(true);
            $t->decimal('lat', 10, 6)->nullable(); $t->decimal('lng', 10, 6)->nullable();
            $t->decimal('total_acreage', 12, 4)->nullable();
            $t->decimal('reserve_land', 12, 4)->nullable();
            $t->decimal('remaining_land', 12, 4)->nullable();
            $t->date('permit_date')->nullable(); $t->date('permit_date_end')->nullable(); $t->date('luas_permit_date')->nullable();
            $t->jsonb('geojson')->nullable(); $t->string('geojson_url')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });

        Schema::create('cameras', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('type')->default('cctv'); // cctv | drone
            $t->string('location')->nullable(); $t->decimal('lat', 10, 6)->nullable(); $t->decimal('lng', 10, 6)->nullable();
            $t->string('hls_url')->nullable(); $t->foreignId('site_id')->nullable()->constrained()->cascadeOnDelete();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });

        // ---- employees + pivot ----
        Schema::create('employees', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('type')->nullable();   // kssb | contractor
            $t->string('job_level')->nullable();
            $t->boolean('is_activated')->default(true); $t->boolean('is_deleted')->default(false);
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('employee_site', function (Blueprint $t) {
            $t->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $t->foreignId('site_id')->constrained()->cascadeOnDelete();
            $t->primary(['employee_id', 'site_id']);
        });
        // user ↔ region / site scoping
        Schema::create('region_user', function (Blueprint $t) {
            $t->foreignId('region_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->primary(['region_id', 'user_id']);
        });
        Schema::create('site_user', function (Blueprint $t) {
            $t->foreignId('site_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->primary(['site_id', 'user_id']);
        });

        // ---- incident / docket reports ----
        Schema::create('reports', function (Blueprint $t) {
            $t->id(); $t->string('name')->nullable(); $t->date('date')->nullable();
            $t->string('type')->nullable();        // docket_cancelation | docket_correction
            $t->string('severity')->nullable();    // Low | Normal | High
            $t->string('status')->default('New');
            $t->text('scope')->nullable(); $t->text('issue')->nullable(); $t->text('action')->nullable(); $t->text('remarks')->nullable();
            $t->jsonb('media')->nullable();
            $t->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });

        // ---- F001 weekly header ----
        Schema::create('weekly_reports', function (Blueprint $t) {
            $t->id(); $t->string('document_no')->nullable();
            $t->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('contractor_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('reviewed_by')->nullable();
            $t->string('status')->default('draft'); // draft|pending_review|pending_approval|submitted|returned
            $t->string('cycle_type')->nullable();   // open_circuit | closed_circuit
            $t->date('start_effective_date')->nullable(); $t->date('end_effective_date')->nullable();
            $t->integer('cctv_luar_qty')->nullable(); $t->string('cctv_luar_status')->nullable();
            $t->integer('cctv_dalam_kabin_qty')->nullable(); $t->string('cctv_dalam_kabin_status')->nullable();
            $t->jsonb('pricing')->nullable();  // clay/granite/sand/laterite/ground_load prices + permit ranges
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });

        // ---- F001 daily body ----
        Schema::create('daily_reports', function (Blueprint $t) {
            $t->id(); $t->foreignId('weekly_report_id')->constrained()->cascadeOnDelete();
            $t->date('date')->nullable(); $t->boolean('is_public_holiday')->default(false); $t->string('public_holiday_name')->nullable();
            $t->jsonb('weather_slots')->nullable(); // 48 half-hour keys
            // manpower counts
            foreach ([
                'mekanik','operator_longarm','operator_palong','pekerja_am','pemandu_excavator','pemandu_lori',
                'pemandu_motor_grader','pemandu_rb','pemandu_shovel','pemandu_tractor','pemandu_water_tanker',
            ] as $role) { $t->integer($role.'_count')->default(0); }
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('daily_system_statuses', function (Blueprint $t) {
            $t->id(); $t->foreignId('daily_report_id')->constrained()->cascadeOnDelete();
            $t->string('cctv_dalam_kabin_status')->nullable(); $t->string('cctv_luar_status')->nullable();
            $t->string('cty_gemma_status')->nullable(); $t->string('e_docket_status')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('daily_environment_statuses', function (Blueprint $t) {
            $t->id(); $t->foreignId('daily_report_id')->constrained()->cascadeOnDelete();
            $t->string('effluent_pagi')->nullable(); $t->string('effluent_tghari')->nullable(); $t->string('effluent_petang')->nullable();
            $t->string('kolam1')->nullable(); $t->string('kolam2')->nullable(); $t->string('kolam3')->nullable();
            $t->string('spillway')->nullable(); $t->string('spilltray')->nullable(); $t->jsonb('spilltray_images')->nullable();
            $t->string('papan_tanda')->nullable(); $t->jsonb('papan_tanda_images')->nullable();
            $t->string('tanda_sempadan')->nullable(); $t->jsonb('tanda_sempadan_images')->nullable();
            $t->string('laluan_keluar_masuk')->nullable(); $t->jsonb('laluan_keluar_masuk_images')->nullable();
            $t->string('maintenance')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('weekly_sales_products', function (Blueprint $t) {
            $t->id(); $t->foreignId('weekly_report_id')->constrained()->cascadeOnDelete();
            $t->string('product')->nullable(); $t->string('permit_code')->nullable();
            $t->date('permit_start_date')->nullable(); $t->date('permit_end_date')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('daily_sales_records', function (Blueprint $t) {
            $t->id(); $t->foreignId('daily_report_id')->constrained()->cascadeOnDelete();
            $t->foreignId('weekly_sales_product_id')->nullable()->constrained()->nullOnDelete();
            $t->string('doket_kssb_mula')->nullable(); $t->string('doket_kssb_akhir')->nullable(); $t->integer('doket_kssb_baki')->nullable();
            $t->string('doket_ptd_mula')->nullable(); $t->string('doket_ptd_akhir')->nullable(); $t->integer('doket_ptd_baki')->nullable();
            $t->decimal('stok_mula', 14, 2)->nullable(); $t->decimal('baki_stockpile', 14, 2)->nullable(); $t->decimal('baki_topup', 14, 2)->nullable();
            $t->decimal('jualan', 14, 2)->nullable(); $t->decimal('jualan_terkumpul', 14, 2)->nullable();
            $t->integer('lori_trip')->nullable(); $t->decimal('pengeluaran_cuci', 14, 2)->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('avr_readings', function (Blueprint $t) {
            $t->id(); $t->foreignId('daily_report_id')->constrained()->cascadeOnDelete();
            $t->string('weighbridge')->nullable(); $t->string('shift')->nullable();
            $t->decimal('input', 14, 2)->nullable(); $t->decimal('output', 14, 2)->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
            $t->index('daily_report_id');
        });
        Schema::create('weekly_assignments', function (Blueprint $t) {
            $t->id(); $t->foreignId('weekly_report_id')->constrained()->cascadeOnDelete();
            $t->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $t->string('shift')->nullable(); $t->text('note')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('report_attendances', function (Blueprint $t) {
            $t->id(); $t->foreignId('daily_report_id')->nullable()->constrained()->cascadeOnDelete();
            $t->foreignId('weekly_assignment_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $t->date('date')->nullable(); $t->string('status')->nullable(); $t->text('note')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
            $t->index(['daily_report_id', 'employee_id']);
        });
        Schema::create('weekly_notes', function (Blueprint $t) {
            $t->id(); $t->foreignId('weekly_report_id')->constrained()->cascadeOnDelete();
            $t->date('tarikh')->nullable(); $t->text('tindakan')->nullable(); $t->text('ulasan')->nullable(); $t->date('deadline')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('non_compliances', function (Blueprint $t) {
            $t->id(); $t->foreignId('weekly_report_id')->constrained()->cascadeOnDelete();
            $t->string('jabatan')->nullable(); $t->date('tarikh')->nullable(); $t->text('ulasan')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('report_activities', function (Blueprint $t) {
            $t->id(); $t->foreignId('weekly_report_id')->constrained()->cascadeOnDelete();
            $t->string('from_status')->nullable(); $t->string('to_status')->nullable();
            $t->foreignId('action_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('action_by_name')->nullable(); $t->string('action_by_role')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });

        // ---- equipment subsystem ----
        Schema::create('weekly_equipment_entries', function (Blueprint $t) {
            $t->id(); $t->foreignId('weekly_report_id')->constrained()->cascadeOnDelete();
            $t->foreignId('equipment_item_type_id')->nullable()->constrained()->nullOnDelete();
            $t->string('brand')->nullable(); $t->string('owner')->nullable(); $t->string('specification')->nullable(); $t->text('note')->nullable();
            $t->jsonb('images')->nullable(); $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('equipment_daily_counts', function (Blueprint $t) {
            $t->id(); $t->foreignId('weekly_equipment_entry_id')->constrained()->cascadeOnDelete();
            $t->foreignId('daily_report_id')->nullable()->constrained()->cascadeOnDelete();
            $t->integer('count')->default(0); $t->string('legacy_id')->nullable()->index(); $t->timestamps();
            $t->index(['daily_report_id', 'weekly_equipment_entry_id']);
        });

        // ---- site geo/media assets ----
        Schema::create('site_polygons', function (Blueprint $t) {
            $t->id(); $t->foreignId('site_id')->constrained()->cascadeOnDelete();
            $t->string('name')->nullable(); $t->date('date')->nullable(); $t->jsonb('features')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('site_standoffs', function (Blueprint $t) {
            $t->id(); $t->foreignId('site_id')->constrained()->cascadeOnDelete();
            $t->string('name')->nullable(); $t->decimal('lat', 10, 6)->nullable(); $t->decimal('lng', 10, 6)->nullable();
            $t->decimal('bearing', 8, 2)->nullable(); $t->jsonb('media')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('site_galleries', function (Blueprint $t) {
            $t->id(); $t->foreignId('site_id')->constrained()->cascadeOnDelete();
            $t->string('name')->nullable(); $t->jsonb('media')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('site_gallery_captions', function (Blueprint $t) {
            $t->id(); $t->foreignId('site_id')->nullable()->constrained()->cascadeOnDelete();
            $t->string('file')->nullable(); $t->text('text')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('point_clouds', function (Blueprint $t) {
            $t->id(); $t->foreignId('site_id')->constrained()->cascadeOnDelete(); $t->string('url')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('tiles', function (Blueprint $t) {
            $t->id(); $t->foreignId('site_id')->constrained()->cascadeOnDelete();
            $t->string('name')->nullable(); $t->date('date')->nullable(); $t->string('url')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('monitoring_reports', function (Blueprint $t) {
            $t->id(); $t->string('name')->nullable(); $t->string('type')->nullable(); $t->timestamp('published_at')->nullable();
            $t->jsonb('files')->nullable(); $t->foreignId('upload_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('landowner_monthly_reports', function (Blueprint $t) {
            $t->id(); $t->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $t->date('date')->nullable(); $t->string('status')->nullable();
            $t->string('submitted_by')->nullable(); $t->string('reviewed_by')->nullable(); $t->string('approved_by')->nullable();
            $t->string('file')->nullable(); $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('complaint_files', function (Blueprint $t) {
            $t->id(); $t->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name')->nullable(); $t->string('type')->nullable(); $t->jsonb('files')->nullable();
            $t->foreignId('upload_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });
        Schema::create('iso_files', function (Blueprint $t) {
            $t->id(); $t->string('name')->nullable(); $t->string('type')->nullable(); $t->jsonb('files')->nullable();
            $t->string('legacy_id')->nullable()->index(); $t->timestamps();
        });

        // ---- PostGIS geometry columns (SRID 4326) ----
        DB::statement('ALTER TABLE sites ADD COLUMN boundary geometry(Geometry,4326)');
        DB::statement('ALTER TABLE sites ADD COLUMN geom geometry(Point,4326)');
        DB::statement('ALTER TABLE reports ADD COLUMN polygon geometry(Geometry,4326)');
        DB::statement('ALTER TABLE site_polygons ADD COLUMN geom geometry(Geometry,4326)');
        DB::statement('ALTER TABLE site_standoffs ADD COLUMN geom geometry(Point,4326)');
        DB::statement('CREATE INDEX sites_geom_gix ON sites USING GIST (geom)');
        DB::statement('CREATE INDEX sites_boundary_gix ON sites USING GIST (boundary)');
        DB::statement('CREATE INDEX site_polygons_geom_gix ON site_polygons USING GIST (geom)');
    }

    public function down(): void
    {
        foreach ([
            'iso_files','complaint_files','landowner_monthly_reports','monitoring_reports','tiles','point_clouds',
            'site_gallery_captions','site_galleries','site_standoffs','site_polygons','equipment_daily_counts',
            'weekly_equipment_entries','report_activities','non_compliances','weekly_notes','report_attendances',
            'weekly_assignments','avr_readings','daily_sales_records','weekly_sales_products','daily_environment_statuses',
            'daily_system_statuses','daily_reports','weekly_reports','reports','site_user','region_user','employee_site',
            'employees','cameras','sites','equipment_item_types','site_products','site_types','contractors','regions',
        ] as $tbl) { Schema::dropIfExists($tbl); }
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['username','employee_id','position','company','blocked','confirmed','legacy_id']);
        });
    }
};
