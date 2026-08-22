<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'video_url')) {
                $table->string('video_url', 500)->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'video_type')) {
                $table->string('video_type', 50)->nullable()->after('video_url');
            }
            if (!Schema::hasColumn('products', 'is_promoted')) {
                $table->boolean('is_promoted')->default(false)->after('featured');
            }
        });

        Schema::table('businesses', function (Blueprint $table) {
            if (!Schema::hasColumn('businesses', 'promo_video_url')) {
                $table->string('promo_video_url', 500)->nullable()->after('about');
            }
        });

        // Set sample video reels on existing demo products
        $sampleVideos = [
            'https://youtube.com/shorts/dQw4w9WgXcQ',
            'https://www.youtube.com/watch?v=ScMzIvxBSi4',
        ];

        $firstProducts = DB::table('products')->limit(4)->get();
        foreach ($firstProducts as $idx => $p) {
            DB::table('products')->where('id', $p->id)->update([
                'video_url' => $sampleVideos[$idx % count($sampleVideos)],
                'is_promoted' => true,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'video_type', 'is_promoted']);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['promo_video_url']);
        });
    }
};
