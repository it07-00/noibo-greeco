<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Livewire\Mail\MailCenterIndex;
use App\Mail\ComposedMail;
use App\Models\Setting;
use App\Models\User;
use App\Services\MailSettingsService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

final class MailModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_mail_center(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);

        $this->actingAs($admin)
            ->get(route('mail.index'))
            ->assertOk()
            ->assertSee('Hộp thư nội bộ');
    }

    public function test_user_without_mail_permission_cannot_access_mail_center(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('mail.index'))
            ->assertForbidden();
    }

    public function test_mail_settings_can_be_saved_with_encrypted_passwords(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);

        $this->actingAs($admin);

        Livewire::test(MailCenterIndex::class)
            ->set('enabled', true)
            ->set('from_name', 'GREECO')
            ->set('from_address', 'no-reply@greeco.vn')
            ->set('imap_host', 'mail.greeco.vn')
            ->set('imap_port', 993)
            ->set('imap_encryption', 'ssl')
            ->set('imap_username', 'no-reply@greeco.vn')
            ->set('imap_password', 'imap-secret')
            ->set('smtp_host', 'mail.greeco.vn')
            ->set('smtp_port', 465)
            ->set('smtp_encryption', 'ssl')
            ->set('smtp_username', 'no-reply@greeco.vn')
            ->set('smtp_password', 'smtp-secret')
            ->call('saveSettings')
            ->assertHasNoErrors();

        $this->assertSame('mail.greeco.vn', Setting::get('mail.imap_host'));
        $this->assertNotSame('imap-secret', Setting::get('mail.imap_password'));
        $this->assertNotSame('smtp-secret', Setting::get('mail.smtp_password'));

        $settings = app(MailSettingsService::class)->load();

        $this->assertSame('imap-secret', $settings->imapPassword);
        $this->assertSame('smtp-secret', $settings->smtpPassword);
    }

    public function test_user_with_mail_send_permission_can_send_email_from_compose_form(): void
    {
        Mail::fake();
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);

        $this->actingAs($admin);

        Livewire::test(MailCenterIndex::class)
            ->set('from_name', 'GREECO')
            ->set('from_address', 'duan@greeco.vn')
            ->set('smtp_host', 'mail.greeco.vn')
            ->set('smtp_port', 465)
            ->set('smtp_encryption', 'ssl')
            ->set('smtp_username', 'duan@greeco.vn')
            ->set('smtp_password', 'smtp-secret')
            ->set('compose_to', 'recipient@example.com')
            ->set('compose_subject', 'Test subject')
            ->set('compose_body', 'Test body')
            ->call('sendMail')
            ->assertHasNoErrors()
            ->assertSet('successMessage', 'Đã gửi email thành công.');

        Mail::assertSent(ComposedMail::class);
    }
}
