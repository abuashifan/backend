<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;

final class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @return string
     */
    protected function rootView(Request $request): string
    {
        return 'app';
    }

    /**
     * Define props shared by default with Inertia pages.
     *
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // Add shared props here if necessary.
        ]);
    }
}
