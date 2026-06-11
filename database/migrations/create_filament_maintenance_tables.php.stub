<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filament_maintenance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('panel_id')->unique();
            $table->boolean('enabled')->default(false);
            $table->json('allowed_ips')->nullable();
            $table->json('allowed_roles')->nullable();
            $table->json('manager_user_ids')->nullable();
            $table->json('manager_roles')->nullable();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->string('view')->nullable();
            $table->string('enabled_by')->nullable();
            $table->timestamp('enabled_at')->nullable();
            $table->string('disabled_by')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('filament_maintenance_events', function (Blueprint $table) {
            $table->id();
            $table->string('panel_id')->index();
            $table->string('action');
            $table->string('user_id')->nullable();
            $table->string('ip')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filament_maintenance_events');
        Schema::dropIfExists('filament_maintenance_settings');
    }
};
