<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlanFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(config('diviky.subscriptions.tables.plan_features'), function (Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->integer('plan_id')->unsigned();
            $table->string('slug');
            $table->json('name');
            $table->json('description')->nullable();
            $table->string('value');
            $table->smallInteger('resettable_period')->unsigned()->default(0);
            $table->string('resettable_interval')->default('month');
            $table->mediumInteger('sort_order')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['plan_id', 'slug']);
            $table->foreign('plan_id')->references('id')->on(config('diviky.subscriptions.tables.plans'))
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('diviky.subscriptions.tables.plan_features'));
    }
}
