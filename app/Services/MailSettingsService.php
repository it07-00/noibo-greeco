<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\MailSettingsDTO;
use App\Models\Setting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

final class MailSettingsService
{
    private const string PREFIX = 'mail.';

    public function load(): MailSettingsDTO
    {
        return MailSettingsDTO::fromArray([
            'enabled' => Setting::get(self::PREFIX.'enabled', false),
            'from_name' => Setting::get(self::PREFIX.'from_name', 'GREECO'),
            'from_address' => Setting::get(self::PREFIX.'from_address', ''),
            'imap_host' => Setting::get(self::PREFIX.'imap_host', 'mail.greeco.vn'),
            'imap_port' => Setting::get(self::PREFIX.'imap_port', 993),
            'imap_encryption' => Setting::get(self::PREFIX.'imap_encryption', 'ssl'),
            'imap_username' => Setting::get(self::PREFIX.'imap_username', ''),
            'imap_password' => $this->getSecret(self::PREFIX.'imap_password'),
            'smtp_host' => Setting::get(self::PREFIX.'smtp_host', 'mail.greeco.vn'),
            'smtp_port' => Setting::get(self::PREFIX.'smtp_port', 465),
            'smtp_encryption' => Setting::get(self::PREFIX.'smtp_encryption', 'ssl'),
            'smtp_username' => Setting::get(self::PREFIX.'smtp_username', ''),
            'smtp_password' => $this->getSecret(self::PREFIX.'smtp_password'),
            'timeout' => Setting::get(self::PREFIX.'timeout', 15),
        ]);
    }

    public function save(MailSettingsDTO $dto, bool $keepEmptyPasswords = true): void
    {
        Setting::set(self::PREFIX.'enabled', $dto->enabled ? '1' : '0');
        Setting::set(self::PREFIX.'from_name', $dto->fromName);
        Setting::set(self::PREFIX.'from_address', $dto->fromAddress);
        Setting::set(self::PREFIX.'imap_host', $dto->imapHost);
        Setting::set(self::PREFIX.'imap_port', (string) $dto->imapPort);
        Setting::set(self::PREFIX.'imap_encryption', $dto->imapEncryption);
        Setting::set(self::PREFIX.'imap_username', $dto->imapUsername);
        Setting::set(self::PREFIX.'smtp_host', $dto->smtpHost);
        Setting::set(self::PREFIX.'smtp_port', (string) $dto->smtpPort);
        Setting::set(self::PREFIX.'smtp_encryption', $dto->smtpEncryption);
        Setting::set(self::PREFIX.'smtp_username', $dto->smtpUsername);
        Setting::set(self::PREFIX.'timeout', (string) $dto->timeout);

        if ($dto->imapPassword !== null || ! $keepEmptyPasswords) {
            Setting::set(self::PREFIX.'imap_password', $dto->imapPassword !== null ? Crypt::encryptString($dto->imapPassword) : null);
        }

        if ($dto->smtpPassword !== null || ! $keepEmptyPasswords) {
            Setting::set(self::PREFIX.'smtp_password', $dto->smtpPassword !== null ? Crypt::encryptString($dto->smtpPassword) : null);
        }
    }

    public function withStoredSecrets(MailSettingsDTO $dto): MailSettingsDTO
    {
        $stored = $this->load();

        return $dto->withPasswords(
            imapPassword: $dto->imapPassword ?? $stored->imapPassword,
            smtpPassword: $dto->smtpPassword ?? $stored->smtpPassword,
        );
    }

    public function hasImapCredentials(): bool
    {
        $settings = $this->load();

        return $settings->imapHost !== ''
            && $settings->imapUsername !== ''
            && $settings->imapPassword !== null;
    }

    private function getSecret(string $key): ?string
    {
        $value = Setting::get($key);

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return $value;
        }
    }
}
