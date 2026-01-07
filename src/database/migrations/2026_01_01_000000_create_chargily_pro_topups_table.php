<?php

use Chargily\ChargilyProLaravel\Enums\ChargilyProTopupStatusEnum;
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
        Schema::create('chargily_pro_topups', function (Blueprint $table) {
            $table->id();
            $table->string("operator");
            $table->string("mode_name");
            $table->string("value");
            $table->string("country_code");
            $table->string("phone_number");
            $table->integer("status")->default(ChargilyProTopupStatusEnum::CREATED);
            $table->text("message")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chargily_pro_topups');
    }
};
