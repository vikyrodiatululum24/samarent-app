<?php

namespace App\Filament\Pages\Auth;

use App\Models\Admin;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Notifications\Notification;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Profil')
                    ->description('Update informasi profil dan akun Anda')
                    ->schema([
                        Section::make('Informasi Akun')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),

                                Select::make('role')
                                    ->label('Role')
                                    ->options([
                                        'admin' => 'Administrator',
                                        'manager' => 'Manager',
                                        'finance' => 'Finance',
                                        'cs' => 'Customer Service',
                                        'checker' => 'Checker'
                                    ])
                                    ->disabled()
                                    ->dehydrated(false),

                                FileUpload::make('ttd')
                                    ->label('Tanda Tangan Digital')
                                    ->image()
                                    ->directory('foto-ttd')
                                    ->disk('public')
                                    ->imageResizeMode('contain')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                    ->hidden(fn() => $this->getUser()->role !== 'admin'),
                            ])
                            ->columnSpan(1),

                        Section::make('Informasi Keamanan')
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password Baru')
                                    ->password()
                                    ->rule('min:8')
                                    ->confirmed()
                                    ->revealable()
                                    ->dehydrated(fn($state) => filled($state))
                                    ->dehydrateStateUsing(fn($state) => bcrypt($state)),

                                TextInput::make('password_confirmation')
                                    ->label('Konfirmasi Password')
                                    ->password()
                                    ->revealable()
                                    ->dehydrated(false)
                                    ->rules(['required_with:password']),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2)
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['ttd'] = $this->getUser()->admin?->ttd;
        return $data;
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            // Update basic user info
            parent::save();

            // Handle TTD for admin users
            if ($this->getUser()->role === 'admin') {
                $oldTtd = $this->getUser()->admin?->ttd;
                $newTtd = $data['ttd'] ?? null;

                // Delete old file if different
                if ($oldTtd && $oldTtd !== $newTtd && Storage::disk('public')->exists($oldTtd)) {
                    Storage::disk('public')->delete($oldTtd);
                }

                // Save new TTD
                if ($newTtd) {
                    Admin::updateOrCreate(
                        ['user_id' => $this->getUser()->id],
                        ['ttd' => $newTtd]
                    );
                }
            }

            Notification::make()
                ->title('Profile Updated')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
