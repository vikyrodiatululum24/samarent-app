<?php

// app/Livewire/UploadImage.php

namespace App\Livewire;

use Livewire\Component;
use Barryvdh\DomPDF\PDF;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class UploadImage extends Component
{
    use WithFileUploads;

    public $images = [];

    public function updatedImages()
    {
        $this->validate([
            'images.*' => 'image|max:2048', // max 2MB per image
        ]);

        if (count($this->images) > 27) {
            $this->images = array_slice($this->images, 0, 27);
            session()->flash('error', 'Maksimal hanya bisa upload 27 gambar.');
        }
    }

    public function render()
    {
        return view('livewire.upload-image');
    }

    public function getTempImageUrls()
    {
        $urls = [];

        foreach ($this->images as $image) {
            $urls[] = $image->temporaryUrl();
        }

        return $urls;
    }
public function printImages()
{
    $this->validate([
        'images.*' => 'image|max:2048',
    ]);

    $paths = [];

    foreach ($this->images as $image) {
        $filename = Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('temp', $filename); // disimpan ke storage/app/temp/
        $paths[] = Storage::path($path); // path absolut
    }

    $pdf = app()->make(PDF::class);
    $pdf->loadView('livewire.print-image-pdf', [
        'paths' => $paths
    ]);

    $namaFile = 'print-images-' . Str::random(10) . '.pdf';

    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->stream();
    }, $namaFile);
}
}
