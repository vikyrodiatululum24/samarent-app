<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Reimbursement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicReimbursementTest extends TestCase
{
    use RefreshDatabase;

    protected $validToken = 'reimbursement2026';

    /**
     * Test: Dapat mengakses form dengan token valid
     */
    public function test_can_access_form_with_valid_token()
    {
        $response = $this->get(route('reimbursement.create', ['token' => $this->validToken]));

        $response->assertStatus(200);
        $response->assertViewIs('reimbursement.create');
        $response->assertViewHas('users');
        $response->assertViewHas('token');
    }

    /**
     * Test: Tidak dapat mengakses form dengan token invalid
     */
    public function test_cannot_access_form_with_invalid_token()
    {
        $response = $this->get(route('reimbursement.create', ['token' => 'wrong_token']));

        $response->assertStatus(403);
    }

    /**
     * Test: Tidak dapat mengakses form tanpa token
     */
    public function test_cannot_access_form_without_token()
    {
        $response = $this->get(route('reimbursement.create'));

        $response->assertStatus(403);
    }

    /**
     * Test: Dapat membuat reimbursement dengan data valid
     */
    public function test_can_create_reimbursement_with_valid_data()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $data = [
            'token' => $this->validToken,
            'user_id' => $user->id,
            'km_awal' => 10000,
            'foto_odometer_awal' => UploadedFile::fake()->image('odometer_awal.jpg'),
            'km_akhir' => 10100,
            'foto_odometer_akhir' => UploadedFile::fake()->image('odometer_akhir.jpg'),
            'tujuan_perjalanan' => 'Test perjalanan dinas',
            'keterangan' => 'Test keterangan',
            'dana_masuk' => 500000,
            'dana_keluar' => 300000,
        ];

        $response = $this->post(route('reimbursement.store'), $data);

        $response->assertRedirect(route('reimbursement.success', ['token' => $this->validToken]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reimbursements', [
            'user_id' => $user->id,
            'km_awal' => 10000,
            'km_akhir' => 10100,
            'tujuan_perjalanan' => 'Test perjalanan dinas',
        ]);

        Storage::disk('public')->assertExists(
            Reimbursement::first()->foto_odometer_awal
        );
        Storage::disk('public')->assertExists(
            Reimbursement::first()->foto_odometer_akhir
        );
    }

    /**
     * Test: Tidak dapat membuat reimbursement dengan token invalid
     */
    public function test_cannot_create_reimbursement_with_invalid_token()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $data = [
            'token' => 'wrong_token',
            'user_id' => $user->id,
            'km_awal' => 10000,
            'foto_odometer_awal' => UploadedFile::fake()->image('odometer_awal.jpg'),
            'km_akhir' => 10100,
            'foto_odometer_akhir' => UploadedFile::fake()->image('odometer_akhir.jpg'),
            'tujuan_perjalanan' => 'Test perjalanan dinas',
        ];

        $response = $this->post(route('reimbursement.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid access token');
        $this->assertDatabaseCount('reimbursements', 0);
    }

    /**
     * Test: Validasi field required
     */
    public function test_validates_required_fields()
    {
        Storage::fake('public');

        $data = [
            'token' => $this->validToken,
        ];

        $response = $this->post(route('reimbursement.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'user_id',
            'km_awal',
            'foto_odometer_awal',
            'km_akhir',
            'foto_odometer_akhir',
            'tujuan_perjalanan',
        ]);
    }

    /**
     * Test: Validasi KM harus numeric
     */
    public function test_validates_km_must_be_numeric()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $data = [
            'token' => $this->validToken,
            'user_id' => $user->id,
            'km_awal' => 'abc', // Invalid
            'foto_odometer_awal' => UploadedFile::fake()->image('odometer_awal.jpg'),
            'km_akhir' => 'xyz', // Invalid
            'foto_odometer_akhir' => UploadedFile::fake()->image('odometer_akhir.jpg'),
            'tujuan_perjalanan' => 'Test perjalanan dinas',
        ];

        $response = $this->post(route('reimbursement.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['km_awal', 'km_akhir']);
    }

    /**
     * Test: Validasi file harus image
     */
    public function test_validates_file_must_be_image()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $data = [
            'token' => $this->validToken,
            'user_id' => $user->id,
            'km_awal' => 10000,
            'foto_odometer_awal' => UploadedFile::fake()->create('document.pdf', 100), // Invalid
            'km_akhir' => 10100,
            'foto_odometer_akhir' => UploadedFile::fake()->create('text.txt', 100), // Invalid
            'tujuan_perjalanan' => 'Test perjalanan dinas',
        ];

        $response = $this->post(route('reimbursement.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['foto_odometer_awal', 'foto_odometer_akhir']);
    }

    /**
     * Test: Validasi file size maksimal 10MB
     */
    public function test_validates_file_size_max_10mb()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $data = [
            'token' => $this->validToken,
            'user_id' => $user->id,
            'km_awal' => 10000,
            'foto_odometer_awal' => UploadedFile::fake()->create('large.jpg', 11000), // 11MB - Invalid
            'km_akhir' => 10100,
            'foto_odometer_akhir' => UploadedFile::fake()->image('odometer_akhir.jpg'),
            'tujuan_perjalanan' => 'Test perjalanan dinas',
        ];

        $response = $this->post(route('reimbursement.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['foto_odometer_awal']);
    }

    /**
     * Test: Dapat mengakses halaman sukses dengan token valid
     */
    public function test_can_access_success_page_with_valid_token()
    {
        $response = $this->get(route('reimbursement.success', ['token' => $this->validToken]));

        $response->assertStatus(200);
        $response->assertViewIs('reimbursement.success');
        $response->assertViewHas('token');
    }

    /**
     * Test: Tidak dapat mengakses halaman sukses dengan token invalid
     */
    public function test_cannot_access_success_page_with_invalid_token()
    {
        $response = $this->get(route('reimbursement.success', ['token' => 'wrong_token']));

        $response->assertStatus(403);
    }

    /**
     * Test: User dropdown berisi semua user
     */
    public function test_user_dropdown_contains_all_users()
    {
        User::factory()->count(5)->create();

        $response = $this->get(route('reimbursement.create', ['token' => $this->validToken]));

        $response->assertStatus(200);

        $users = $response->viewData('users');
        $this->assertCount(5, $users);
    }

    /**
     * Test: Dana masuk dan dana keluar optional
     */
    public function test_dana_fields_are_optional()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $data = [
            'token' => $this->validToken,
            'user_id' => $user->id,
            'km_awal' => 10000,
            'foto_odometer_awal' => UploadedFile::fake()->image('odometer_awal.jpg'),
            'km_akhir' => 10100,
            'foto_odometer_akhir' => UploadedFile::fake()->image('odometer_akhir.jpg'),
            'tujuan_perjalanan' => 'Test perjalanan dinas',
            // dana_masuk and dana_keluar not provided
        ];

        $response = $this->post(route('reimbursement.store'), $data);

        $response->assertRedirect(route('reimbursement.success', ['token' => $this->validToken]));

        $this->assertDatabaseHas('reimbursements', [
            'user_id' => $user->id,
            'dana_masuk' => 0,
            'dana_keluar' => 0,
        ]);
    }
}
