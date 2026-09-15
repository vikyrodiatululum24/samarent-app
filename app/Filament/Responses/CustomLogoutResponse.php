<?php
 
namespace App\Filament\Responses;
 
use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Illuminate\Http\RedirectResponse;
 
class CustomLogoutResponse implements LogoutResponse
{
    public function toResponse($request): RedirectResponse
    {
        // change this to your desired route
        return redirect('/login');
    }
}