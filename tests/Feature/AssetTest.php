<?php

namespace Tests\Feature;

use Tests\TestCase;

class AssetTest extends TestCase
{
    public function test_views_load_assets_locally_not_from_cdn()
    {
        // [view, butuh Alpine]
        $views = [
            ['layouts/admin.blade.php', true],
            ['layouts/peserta.blade.php', true],
            ['signIn.blade.php', false],
            ['results/transcript.blade.php', false],
        ];

        foreach ($views as [$view, $needsAlpine]) {
            $content = file_get_contents(resource_path("views/{$view}"));

            $this->assertStringNotContainsString('cdn.', $content, "{$view} masih memuat aset dari CDN");
            $this->assertStringContainsString('css/app.css', $content, "{$view} tidak memuat stylesheet lokal");

            if ($needsAlpine) {
                $this->assertStringContainsString('vendor/alpine.min.js', $content, "{$view} tidak memuat Alpine lokal");
            }
        }
    }

    public function test_local_assets_exist()
    {
        $this->assertFileExists(public_path('css/app.css'));
        $this->assertFileExists(public_path('vendor/alpine.min.js'));
    }

    public function test_compiled_css_contains_design_tokens_and_utilities()
    {
        $css = file_get_contents(public_path('css/app.css'));

        $this->assertStringContainsString('--color-canvas', $css);
        $this->assertStringContainsString('--color-ink', $css);
        $this->assertStringContainsString('--color-line', $css);
        $this->assertStringContainsString('--color-muted', $css);
        // Utility yang dipakai layout (sidebar sticky, dialog, dll.)
        $this->assertMatchesRegularExpression('/\.sticky\b/', $css);
        $this->assertMatchesRegularExpression('/\.hidden\b/', $css);
    }
}
