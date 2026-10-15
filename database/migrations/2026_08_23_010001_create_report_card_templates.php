<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_card_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->index();
            $table->text('front_layout_settings');
            $table->longText('front_canvas_layout')->nullable();
            $table->boolean('enable_back_page')->default(0);
            $table->text('back_page_content')->nullable();
            $table->longText('back_canvas_layout')->nullable();
            // GrapeJS storage (html + css + json)
            $table->longText('grapejs_html')->nullable();
            $table->longText('grapejs_css')->nullable();
            $table->longText('grapejs_json')->nullable();
            $table->string('orientation', 20)->default('portrait');
            $table->timestamps();
            $table->unique('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_card_templates');
    }
};
