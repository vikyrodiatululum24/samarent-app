<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\ViewAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Event::class;

protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(
                    function (Schema $schema, array $arguments) {
                        $schema->fill([
                            'start_at' => $arguments['start'] ?? null,
                            'end_at'   => $arguments['end'] ?? null,
                            'color'    => '#4F46E5',
                        ]);
                    }
                ),
        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(255),

            ColorPicker::make('color')
                ->default('#4F46E5'),

            Grid::make()
                ->schema([
                    DateTimePicker::make('start_at')
                        ->required()
                        ->label('Start Date & Time')
                        ->default(now()),

                    DateTimePicker::make('end_at')
                        ->required()
                        ->label('End Date & Time')
                        ->default(now()->addHour()),
                ]),

            Textarea::make('description'),
        ];
    }



    public function fetchEvents(array $fetchInfo): array
    {
        return Event::query()
            ->where('start_at', '>=', $fetchInfo['start'])
            ->where('end_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(fn(Event $event) => [
                'id'    => $event->id,
                'title' => $event->title,
                'start' => $event->start_at,
                'end'   => $event->end_at,
                'color' => $event->color,
            ])
            ->toArray();
    }



    public function config(): array
    {
        return [
            'initialView' => 'dayGridMonth',
            'height' => 650,          // <-- tinggi tetap dalam pixel
            // atau
            'aspectRatio' => 1.8,     // <-- rasio lebar:tinggi (semakin besar, semakin pendek)
            'headerToolbar' => [
                'left'   => 'prev,next today',
                'center' => 'title',
                'right'  => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
        ];
    }
}
