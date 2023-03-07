<?php

use App\Enum\UserType;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->json('oauth_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('clicks')->nullable();
            $table->string('cost_micros')->nullable();
            $table->string('impressions')->nullable();
            $table->string('conversions')->nullable();
            $table->string('all_conversions')->nullable();
            $table->enum("user_type" , [
                UserType::ADMIN->value,
                UserType::CUSTOMER->value,
            ])->default(UserType::CUSTOMER->value);
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
