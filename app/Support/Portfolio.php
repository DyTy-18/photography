<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Junta el inventario (resources/data/inventario.json) con las versiones web generadas
 * (resources/data/photos.json) y el storyboard (config/story.php).
 */
class Portfolio
{
    private Collection $photos;

    public function __construct()
    {
        $inventario = collect(json_decode(file_get_contents(resource_path('data/inventario.json')), true))->keyBy('id');
        $web = json_decode(@file_get_contents(resource_path('data/photos.json')) ?: '{}', true);
        $captions = config('story.captions');
        $peaks = config('story.peaks');

        // Solo las fotos que ya tienen versión web
        $this->photos = $inventario
            ->filter(fn ($p) => isset($web[$p['id']]))
            ->map(function ($p) use ($web, $captions, $peaks) {
                $w = $web[$p['id']];
                $caption = $captions[$p['id']] ?? ($p['place'] ?: $p['category']);

                return $p + [
                    'w' => $w['w'], 'h' => $w['h'], 'widths' => $w['widths'], 'color' => $w['color'],
                    'caption' => $caption,
                    'peak' => $peaks[$p['id']] ?? null,
                    'alt' => trim($caption.', '.mb_strtolower($p['category']).($p['shot'] === 'Drone' ? ' (vista aérea)' : '')),
                ];
            });
    }

    public function photo(string $id): ?array
    {
        return $this->photos->get($id);
    }

    /** @param  array<string>  $ids */
    public function many(array $ids): array
    {
        return array_values(array_filter(array_map(fn ($id) => $this->photo($id), $ids)));
    }

    /** Todo lo que no está en el storyboard, sin duplicados ni descartes, agrupado por categoría. */
    public function archive(): Collection
    {
        return $this->photos
            ->filter(fn ($p) => ! $p['storyboard'] && ! $p['duplicate_of'] && ! $p['discard'])
            ->sortBy([['category', 'asc'], ['place', 'asc'], ['id', 'asc']])
            ->values();
    }
}
