<?php

namespace App\Filament\Widgets;

use App\Helpers\HolidayDates;
use App\Models\Event;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Event::class;

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(function (Schema $schema, array $arguments) {
                    $schema->fill([
                        'start_at' => $arguments['start'] ?? null,
                        'end_at'   => $arguments['end'] ?? null,
                        'color'    => '#4F46E5',
                    ]);
                }),
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
            TextInput::make('title')->required()->maxLength(255),
            ColorPicker::make('color')->default('#4F46E5'),
            Grid::make()->schema([
                DateTimePicker::make('start_at')->required()->label('Start Date & Time')->default(now()),
                DateTimePicker::make('end_at')->required()->label('End Date & Time')->default(now()->addHour()),
            ]),
            Textarea::make('description'),
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        Log::info('Fetch range', [
            'start' => $fetchInfo['start'],
            'end'   => $fetchInfo['end'],
        ]);
        $events = Event::query()
            ->where('start_at', '>=', $fetchInfo['start'])
            ->where('end_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(fn(Event $event) => [
                'id'     => $event->id,
                'title'  => $event->title,
                'start'  => $event->start_at,
                'end'    => \Carbon\Carbon::parse($event->end_at)->addDay()->format('Y-m-d'), // +1 hari, karena allDay end bersifat exclusive
                'color'  => $event->color,
                'allDay' => true,
            ])
            ->toArray();

        $this->dispatch(
            'calendar-range-changed',
            start: $fetchInfo['start'],
            end: $fetchInfo['end'],
        );

        $holidayEvents = collect();

        foreach (
            range(
                \Carbon\Carbon::parse($fetchInfo['start'])->year,
                \Carbon\Carbon::parse($fetchInfo['end'])->year
            ) as $year
        ) {
            Log::info('Fetching From widget: ' . $year);
            $holidayEvents = $holidayEvents->merge(
                HolidayDates::getHolidayDates($year)->where('is_national_holiday', true)
            );
        }

        $holidayEvents = $holidayEvents
            ->map(fn($holiday) => [
                'start'      => $holiday['date'],
                'display'    => 'background',
                'classNames' => ['libur-nasional'],
            ])
            ->values()
            ->toArray();

        return array_merge($events, $holidayEvents);
    }

    public function config(): array
    {
        return [
            'initialView'   => 'dayGridMonth',
            'height'        => 650,
            'aspectRatio'   => 1.8,
            'headerToolbar' => [
                'left'   => 'prev,next today',
                'center' => 'title',
                'right'  => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],

        ];
    }
}
