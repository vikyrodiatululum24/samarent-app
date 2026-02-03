<?php

namespace App\Filament\Pages\Auth;

use App\Models\Admin;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
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
                            ])
                            ->columnSpan(1),

                        Section::make('Informasi Personal')
                            ->schema([
                                TextInput::make('phone_number')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('08123456789'),

                                DatePicker::make('date_of_birth')
                                    ->label('Tanggal Lahir')
                                    ->native(false)
                                    ->maxDate(now()),

                                Textarea::make('address')
                                    ->label('Alamat')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),

                                FileUpload::make('photo')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->directory('foto-profile')
                                    ->disk('public')
                                    ->avatar()
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),

                        Section::make('Informasi Rekening')
                            ->description('Informasi rekening untuk keperluan pembayaran')
                            ->schema([
                                TextInput::make('nama_rek')
                                    ->label('Nama Rekening')
                                    ->maxLength(255),

                                Select::make('bank')
                                    ->label('Bank')
                                    ->options([
                                        'BCA' => 'BCA',
                                        'MANDIRI' => 'MANDIRI',
                                        'BRI' => 'BRI',
                                        'BNI' => 'BNI',
                                        'PERMATA' => 'PERMATA',
                                        'BTN' => 'BTN',
                                        'CIMB NIAGA' => 'CIMB NIAGA',
                                        'DANAMON' => 'DANAMON',
                                        'BSI' => 'BSI',
                                    ])
                                    ->searchable(),

                                TextInput::make('norek')
                                    ->label('Nomor Rekening')
                                    ->numeric()
                                    ->maxLength(30),
                            ])
                            ->columnSpan(1)
                            ->columns(1),

                        Section::make('Tanda Tangan Digital')
                            ->description('Upload tanda tangan digital untuk keperluan dokumen')
                            ->schema([
                                FileUpload::make('ttd')
                                    ->label('Tanda Tangan 1')
                                    ->image()
                                    ->resize(50)
                                    ->directory('foto-ttd')
                                    ->disk('public')
                                    ->imageResizeMode('contain')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png']),
                            ]),

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
        // Load profile data
        $profile = $this->getUser()->profile;
        if ($profile) {
            $data['phone_number'] = $profile->phone_number;
            $data['date_of_birth'] = $profile->date_of_birth;
            $data['address'] = $profile->address;
            $data['photo'] = $profile->photo;
            $data['nama_rek'] = $profile->nama_rek;
            $data['bank'] = $profile->bank;
            $data['norek'] = $profile->norek;
        }

        // Load admin TTD data
        if ($this->getUser()->role === 'admin') {
            $data['ttd'] = $this->getUser()->admin?->ttd;
        }

        return $data;
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            // Update basic user info
            parent::save();

            // Handle Profile data
            $profileData = [
                'phone_number' => $data['phone_number'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $data['address'] ?? null,
                'nama_rek' => $data['nama_rek'] ?? null,
                'bank' => $data['bank'] ?? null,
                'norek' => $data['norek'] ?? null,
            ];

            // Handle photo upload/deletion
            $oldPhoto = $this->getUser()->profile?->photo;
            $newPhoto = $data['photo'] ?? null;

            if (empty($newPhoto) && !empty($oldPhoto)) {
                if (Storage::disk('public')->exists($oldPhoto)) {
                    Storage::disk('public')->delete($oldPhoto);
                }
                $profileData['photo'] = null;
            } elseif (!empty($newPhoto) && $newPhoto !== $oldPhoto) {
                if (!empty($oldPhoto) && Storage::disk('public')->exists($oldPhoto)) {
                    Storage::disk('public')->delete($oldPhoto);
                }
                $profileData['photo'] = $newPhoto;
            }

            // Save profile
            Profile::updateOrCreate(
                ['user_id' => $this->getUser()->id],
                $profileData
            );

            // Handle TTD for admin users
            if ($this->getUser()->role === 'admin') {
                $ttdFields = ['ttd'];
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
