<?php

use Chargily\ChargilyProLaravel\Enums\ChargilyProVoucherStatusEnum;
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
        Schema::create('chargily_pro_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("value");
            $table->integer("quantity")->default(1);
            $table->integer("status")->default(ChargilyProVoucherStatusEnum::CREATED);
            $table->text("serial")->nullable();
            $table->text("key")->nullable();
            $table->text("message")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chargily_pro_vouchers');
    }
};
