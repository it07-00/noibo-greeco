<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Thông tin cá nhân')]
final class ProfileEdit extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $dob = '';

    public string $address = '';

    public $avatarUpload;

    public ?string $successMessage = null;

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->dob = $user->dob ? $user->dob->format('Y-m-d') : '';
        $this->address = $user->address ?? '';
    }

    public function save(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'dob' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'avatarUpload' => ['nullable', 'image', 'max:2048'],
        ]);

        $attributes = [
            'name' => $this->name,
            'email' => $this->email,
            'dob' => ! empty($this->dob) ? $this->dob : null,
            'address' => $this->address,
        ];

        $oldAvatarPath = $user->avatar_path;
        $newAvatarPath = null;

        if ($this->avatarUpload) {
            $newAvatarPath = $this->avatarUpload->store('avatars', 'public');
            $attributes['avatar_path'] = $newAvatarPath;
        }

        if (! empty($this->password)) {
            $attributes['password'] = Hash::make($this->password);
        }

        $user->update($attributes);

        if ($newAvatarPath !== null) {
            if ($oldAvatarPath && $oldAvatarPath !== $newAvatarPath) {
                Storage::disk('public')->delete($oldAvatarPath);
            }

            $this->avatarUpload = null;
            $user->refresh();
            $this->dispatch('profile-avatar:updated', url: $user->avatar_url);
        }

        $this->successMessage = 'Cập nhật thông tin cá nhân thành công!';
        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Thành công!',
            'text' => $this->successMessage,
        ]);
        $this->password = '';
    }

    public function render(): View
    {
        return view('livewire.profile.profile-edit', [
            'user' => auth()->user(),
        ]);
    }
}
