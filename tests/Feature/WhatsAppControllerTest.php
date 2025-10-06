<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WhatsAppControllerTest extends TestCase
{
    #[Test]
    public function it_can_send_text_message_via_api()
    {
        // Fake response API PushWa
        Http::fake([
            'dash.pushwa.com/api/kirimPesan' => Http::response([
                'status' => true,
                'message' => 'Pesan berhasil dikirim',
                'idMsg' => '12345',
            ], 200),
        ]);

        // Kirim POST request ke route controller kamu
        $response = $this->postJson('/api/wa/send-text', [
            'target' => '6285714241420',
            'message' => 'Halo Laravel!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Pesan berhasil dikirim',
                'idMsg' => '12345',
            ]);
    }

    #[Test]
    public function it_returns_error_when_validation_fails()
    {
        // Tanpa field 'target' dan 'message'
        $response = $this->postJson('/api/wa/send-text', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['target', 'message']);
    }

    /** @test */
    public function it_can_send_image_message_via_api()
    {
        Http::fake([
            'dash.pushwa.com/api/kirimPesan' => Http::response([
                'status' => true,
                'message' => 'Gambar terkirim',
                'idMsg' => '99999',
            ], 200),
        ]);

        $response = $this->postJson('/api/wa/send-image', [
            'target' => '6285714241420',
            'url' => asset('images/Samarent.png'),
            'message' => 'Cek gambar ini',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Gambar terkirim',
            ]);
    }

    #[Test]
    public function it_returns_error_if_pushwa_fails()
    {
        Http::fake([
            'dash.pushwa.com/api/kirimPesan' => Http::response([
                'status' => false,
                'message' => 'Invalid token',
            ], 401),
        ]);

        $response = $this->postJson('/api/wa/send-text', [
            'target' => '6285714241420',
            'message' => 'Test Error',
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'status' => false,
            ]);
    }
}
