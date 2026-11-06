<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    /**
     * Panel admin masih berupa tampilan dengan data contoh.
     * Belum ada login maupun pembatasan peran; lihat banner pratinjau di layout.
     * ganti dengan middleware peran admin saat backend/login dibuat.
     */
    public function dashboard()
    {
        return $this->render('dashboard');
    }

    public function reports()
    {
        return $this->render('reports');
    }

    public function locations()
    {
        return $this->render('locations');
    }

    public function activity()
    {
        return $this->render('activity');
    }

    private function render(string $page)
    {
        $locations = File::json(base_path('data/locations.json'));
        $facilities = File::json(base_path('data/facilities.json'));

        $buildings = collect($locations['buildings'] ?? [])
            ->map(fn (array $building) => [
                'kode' => $building['kode'] ?? $building['id'],
                'nama' => $building['nama'],
            ])
            ->values()
            ->all();

        $categories = collect($facilities['categories'] ?? [])
            ->pluck('nama')
            ->values()
            ->all();

        return view("admin.{$page}", [
            'page' => $page,
            'buildings' => $buildings,
            'categories' => $categories,
        ]);
    }
}
