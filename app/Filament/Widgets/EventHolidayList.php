<?php

namespace App\Filament\Widgets;

use App\Helpers\HolidayDates;
use App\Models\Event;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;


class EventHolidayListWidget extends Widget
{
    protected string $view = 'filament.widgets.event-holiday-list';

    protected int | string | array $columnSpan = 'full';

    public array $events = [];

    public ?Carbon $start = null;
    public ?Carbon $end = null;

    #[On('calendar-range-changed')]
    public function onCalendarRangeChanged(
        string $start,
        string $end,
    ): void {

        $this->start = Carbon::parse($start);

        $this->end = Carbon::parse($end);
    }

    public function mount(): void
    {
        $this->start = now()->startOfMonth();
        $this->end = now()->endOfMonth();
    }

    public function getListItems(): array
    {
        $start = $this->start ?? now()->startOfMonth();
        $end = $this->end ?? now()->endOfMonth();

        // Ambil event di bulan berjalan
        $events = Event::query()
            ->where('start_at', '<=', $end)
            ->where('end_at', '>=', $start)
            ->get()
            ->map(fn(Event $event) => [
                'type'  => 'event',
                'date'  => Carbon::parse($event->start_at),
                'title' => $event->title,
                'color' => $event->color,
                'description' => $event->description,
            ]);

        // Ambil libur nasional di bulan berjalan
        $holidays = HolidayDates::getHolidayDates($start->year)
            ->where('is_national_holiday', true)
            ->filter(fn($holiday) => Carbon::parse($holiday['date'])->between($start, $end))
            ->map(fn($holiday) => [
                'type'  => 'holiday',
                'date'  => Carbon::parse($holiday['date']),
                'title' => $holiday['name'],
                'color' => '#dc2626',
                'description' => null,
            ]);

        Log::info('List items fetched', [
            'start' => $start->toDateString(),
            'end'   => $end->toDateString(),
            'events_count' => $events->count(),
            'holidays_count' => $holidays->count(),
        ]);

        return $events
            ->concat($holidays)
            ->sortBy('date')
            ->values()
            ->toArray();
    }
}
