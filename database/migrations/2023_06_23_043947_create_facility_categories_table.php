<?php

use App\Models\FacilityCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facility_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        FacilityCategory::create(['name' => 'Bedroom']);
        FacilityCategory::create(['name' => 'Kitchen']);
        FacilityCategory::create(['name' => 'Bathroom']);
        FacilityCategory::create(['name' => 'Room Amenities']);
        FacilityCategory::create(['name' => 'General']);
        FacilityCategory::create(['name' => 'Media & Technology']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_categories');
    }
};
