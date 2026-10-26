<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function __invoke()
    {
        $locations = File::json(base_path('data/locations.json'));
        $facilities = File::json(base_path('data/facilities.json'));
        $layout = $locations['layout'] ?? [];
        $buildings = collect($locations['buildings'] ?? [])->keyBy('id');

        $areas = collect($layout['areas'] ?? [])
            ->map(function (array $area) use ($buildings) {
                $building = $buildings->get($area['building_id']);

                return [
                    ...$area,
                    'kode' => $building['kode'] ?? $area['building_id'],
                    'nama' => $building['nama'] ?? $area['label'],
                ];
            })
            ->values()
            ->all();

        // Konfigurasi untuk form laporan. Data akses data laporan tetap di modul JS.
        $formConfig = [
            'buildings' => collect($locations['buildings'] ?? [])
                ->map(fn (array $building) => [
                    'kode' => $building['kode'] ?? $building['id'],
                    'nama' => $building['nama'],
                    'floors' => collect($building['floors'] ?? [])
                        ->map(fn (array $floor) => [
                            'nama' => $floor['nama'],
                            'rooms' => collect($floor['rooms'] ?? [])->pluck('nama')->values()->all(),
                        ])
                        ->values()
                        ->all(),
                ])
                ->values()
                ->all(),
            'categories' => collect($facilities['categories'] ?? [])
                ->map(fn (array $category) => [
                    'nama' => $category['nama'],
                    'facilities' => collect($category['facilities'] ?? [])->pluck('nama')->values()->all(),
                ])
                ->values()
                ->all(),
        ];

        return view('home', [
            'areas' => $areas,
            'aspectRatio' => str_replace(':', '/', $layout['aspect_ratio'] ?? '4/3'),
            'formConfig' => $formConfig,
        ]);
    }
}
