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
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_group_id')->nullable();
            $table->string('name');
            $table->decimal('rate', 10, 2)->default(0);
            $table->integer('tax_type')->comment('1 = Inclusive, 2 = Exclusive');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            //set foreign key constraint
            $table->foreign('tax_group_id')->references('id')->on('tax_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_rates', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('tax_rates');
    }
};
