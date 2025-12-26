<?php

namespace App\Filament\Pages;

use App\Models\Settings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.settings-page';

    protected static ?string $navigationLabel = 'Pengaturan Tanda Tangan';

    protected static ?string $title = 'Pengaturan Tanda Tangan Digital';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 999;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'ttd_diketahui' => Settings::where('key', 'ttd_diketahui')->first()?->value,
            'nama_diketahui' => Settings::where('key', 'nama_diketahui')->first()?->value,
            'ttd_diperiksa' => Settings::where('key', 'ttd_diperiksa')->first()?->value,
            'nama_diperiksa' => Settings::where('key', 'nama_diperiksa')->first()?->value,
            'ttd_disetujui' => Settings::where('key', 'ttd_disetujui')->first()?->value,
            'nama_disetujui' => Settings::where('key', 'nama_disetujui')->first()?->value,
            'ttd_dibukukan' => Settings::where('key', 'ttd_dibukukan')->first()?->value,
            'nama_dibukukan' => Settings::where('key', 'nama_dibukukan')->first()?->value,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tanda Tangan Digital')
                    ->description('Upload foto tanda tangan digital untuk berbagai keperluan persetujuan dokumen')
                    ->schema([
                        Section::make('Diketahui')
                            ->schema([
                                FileUpload::make('ttd_diketahui')
                                    ->label('Tanda Tangan - Diketahui')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->maxSize(2048)
                                    ->directory('signatures')
                                    ->visibility('public')
                                    ->helperText('Upload foto tanda tangan untuk keperluan "Diketahui" (Max: 2MB)')
                                    ->columnSpanFull(),
                                \Filament\Forms\Components\TextInput::make('nama_diketahui')
                                    ->label('Nama - Diketahui')
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama penandatangan')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->collapsible(),

                        Section::make('Diperiksa')
                            ->schema([
                                FileUpload::make('ttd_diperiksa')
                                    ->label('Tanda Tangan - Diperiksa')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->maxSize(2048)
                                    ->directory('signatures')
                                    ->visibility('public')
                                    ->helperText('Upload foto tanda tangan untuk keperluan "Diperiksa" (Max: 2MB)')
                                    ->columnSpanFull(),
                                \Filament\Forms\Components\TextInput::make('nama_diperiksa')
                                    ->label('Nama - Diperiksa')
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama penandatangan')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->collapsible(),

                        Section::make('Disetujui')
                            ->schema([
                                FileUpload::make('ttd_disetujui')
                                    ->label('Tanda Tangan - Disetujui')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->maxSize(2048)
                                    ->directory('signatures')
                                    ->visibility('public')
                                    ->helperText('Upload foto tanda tangan untuk keperluan "Disetujui" (Max: 2MB)')
                                    ->columnSpanFull(),
                                \Filament\Forms\Components\TextInput::make('nama_disetujui')
                                    ->label('Nama - Disetujui')
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama penandatangan')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->collapsible(),

                        Section::make('Dibukukan')
                            ->schema([
                                FileUpload::make('ttd_dibukukan')
                                    ->label('Tanda Tangan - Dibukukan')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->maxSize(2048)
                                    ->directory('signatures')
                                    ->visibility('public')
                                    ->helperText('Upload foto tanda tangan untuk keperluan "Dibukukan" (Max: 2MB)')
                                    ->columnSpanFull(),
                                \Filament\Forms\Components\TextInput::make('nama_dibukukan')
                                    ->label('Nama - Dibukukan')
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama penandatangan')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->collapsible(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save each signature as separate record with key-value
        $signatures = [
            'ttd_diketahui' => [
                'value' => $data['ttd_diketahui'] ?? null,
                'description' => 'Foto tanda tangan untuk diketahui',
            ],
            'nama_diketahui' => [
                'value' => $data['nama_diketahui'] ?? null,
                'description' => 'Nama penandatangan untuk diketahui',
            ],
            'ttd_diperiksa' => [
                'value' => $data['ttd_diperiksa'] ?? null,
                'description' => 'Foto tanda tangan untuk diperiksa',
            ],
            'nama_diperiksa' => [
                'value' => $data['nama_diperiksa'] ?? null,
                'description' => 'Nama penandatangan untuk diperiksa',
            ],
            'ttd_disetujui' => [
                'value' => $data['ttd_disetujui'] ?? null,
                'description' => 'Foto tanda tangan untuk disetujui',
            ],
            'nama_disetujui' => [
                'value' => $data['nama_disetujui'] ?? null,
                'description' => 'Nama penandatangan untuk disetujui',
            ],
            'ttd_dibukukan' => [
                'value' => $data['ttd_dibukukan'] ?? null,
                'description' => 'Foto tanda tangan untuk dibukukan',
            ],
            'nama_dibukukan' => [
                'value' => $data['nama_dibukukan'] ?? null,
                'description' => 'Nama penandatangan untuk dibukukan',
            ],
        ];

        foreach ($signatures as $key => $config) {
            Settings::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $config['value'],
                    'type' => strpos($key, 'ttd_') === 0 ? 'file' : 'string',
                    'group' => 'signatures',
                    'description' => $config['description'],
                ]
            );
        }

        Notification::make()
            ->success()
            ->title('Berhasil disimpan')
            ->body('Pengaturan tanda tangan digital berhasil diperbarui.')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('save'),
        ];
    }
}
