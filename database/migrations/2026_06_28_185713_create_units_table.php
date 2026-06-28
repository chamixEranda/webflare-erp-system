<?php

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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->nullable();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->integer('uom_type')->comment('1 = Weight, 2 = Length, 3 = Volume, 4 = Area, 5 = Time, 6 = Temperature, 7 = Speed, 8 = Pressure, 9 = Energy, 10 = Power, 11 = Frequency, 12 = Data Storage, 13 = Currency');
            $table->bigInteger('base_unit_id')->nullable();
            $table->decimal('conversion_factor', 18, 8)->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
