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
                                // FileUpload::make('ttd2')
                                //     ->label('Tanda Tangan Yang Menyetujui')
                                //     ->image()
                                //     ->directory('foto-ttd')
                                //     ->disk('public')
                                //     ->imageResizeMode('contain')
                                //     ->maxSize(2048)
                                //     ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                //     ->hidden(fn() => $this->getUser()->role !== 'admin'),
                                // FileUpload::make('ttd3')
                                //     ->label('Tanda Tangan Pemeriksa')
                                //     ->image()
                                //     ->directory('foto-ttd')
                                //     ->disk('public')
                                //     ->imageResizeMode('contain')
                                //     ->maxSize(2048)
                                //     ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                //     ->hidden(fn() => $this->getUser()->role !== 'admin'),
                                // FileUpload::make('ttd4')
                                //     ->label('Tanda Tangan Yang Mengetahui')
                                //     ->image()
                                //     ->directory('foto-ttd')
                                //     ->disk('public')
                                //     ->imageResizeMode('contain')
                                //     ->maxSize(2048)
                                //     ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                //     ->hidden(fn() => $this->getUser()->role !== 'admin'),
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
        // $data['ttd2'] = $this->getUser()->admin?->ttd2;
        // $data['ttd3'] = $this->getUser()->admin?->ttd3;
        // $data['ttd4'] = $this->getUser()->admin?->ttd4;

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
                $ttdFields = ['ttd', 'ttd2', 'ttd3', 'ttd4'];
                $updateData = [];

                foreach ($ttdFields as $field) {
                    $oldTtd = $this->getUser()->admin?->{$field};
                    $newTtd = $data[$field] ?? null;

                    // If image is removed (empty), delete old file and set DB field to null
                    if (empty($newTtd) && !empty($oldTtd)) {
                        if (Storage::disk('public')->exists($oldTtd)) {
                            Storage::disk('public')->delete($oldTtd);
                        }
                        $updateData[$field] = null;
                    }

                    // If image is changed, update DB and delete old file
                    if (!empty($newTtd) && $newTtd !== $oldTtd) {
                        if (!empty($oldTtd) && Storage::disk('public')->exists($oldTtd)) {
                            Storage::disk('public')->delete($oldTtd);
                        }
                        $updateData[$field] = $newTtd;
                    }
                }

                // Save all TTDs if there are any changes
                if (!empty($updateData)) {
                    Admin::updateOrCreate(
                        ['user_id' => $this->getUser()->id],
                        $updateData
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
