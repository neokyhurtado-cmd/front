<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SiteSetting;

return new class extends Migration
{
    public function up(): void
    {
        SiteSetting::query()->firstOrCreate(['id' => 1], [
            'hero_title' => 'Panorama Ingeniería IA',
            'hero_subtitle' => 'Movilidad, señalización y transporte — curado con IA.',
            'notification_text' => 'Sistema en pruebas: algunas publicaciones pueden reprogramarse automáticamente.',
            'sidebar_topics' => ['movilidad','señalización','tránsito','TransMilenio','seguridad vial','Bogotá'],
            'corporate_url' => 'https://www.panoramaingenieria.com',
            'dark_default' => false,
        ]);
    }

    public function down(): void
    {
        SiteSetting::query()->where('id', 1)->delete();
    }
};
