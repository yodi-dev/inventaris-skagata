<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_waka_can_update_nomor_identitas_in_profile(): void
    {
        $waka = User::factory()->create([
            'role' => 'waka',
            'nomor_identitas' => null,
        ]);

        $response = $this
            ->actingAs($waka)
            ->patch('/profile', [
                'name' => 'Waka Sarpras Updated',
                'email' => $waka->email,
                'nomor_identitas' => '19750814 200003 1 002',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $waka->refresh();
        $this->assertSame('19750814 200003 1 002', $waka->nomor_identitas);
    }

    public function test_user_seeder_creates_waka_sarpras_and_kepala_gudang(): void
    {
        $this->seed(\Database\Seeders\UserSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'waka@skagata.sch.id',
            'role' => 'waka',
            'nomor_identitas' => null,
            'status' => 'aktif',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'gudang@skagata.sch.id',
            'role' => 'waka',
            'nomor_identitas' => null,
            'status' => 'aktif',
        ]);

        // Uji otentikasi akun waka
        $loginResponse = $this->post('/login', [
            'email' => 'waka@skagata.sch.id',
            'password' => 'password123',
        ]);
        $loginResponse->assertRedirect('/superadmin/dashboard');

        // Uji otentikasi akun kepala gudang
        $this->post('/logout');
        $gudangResponse = $this->post('/login', [
            'email' => 'gudang@skagata.sch.id',
            'password' => 'password123',
        ]);
        $gudangResponse->assertRedirect('/superadmin/dashboard');
    }
}
