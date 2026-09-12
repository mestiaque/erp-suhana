<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Powers the topbar search box: searches every leaf item of the fully
 * assembled config('sidebar') (main app + every package's merged-in
 * sidebar, see AppServiceProvider::mergePackageSidebar()) by title, applying
 * the exact same hasChildPermission() gate sidebar.blade.php itself uses so
 * a result never links anywhere the current user couldn't already see in
 * the sidebar.
 */
class MenuSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if ($term === '') {
            return response()->json([]);
        }

        $items = $this->flatten(config('sidebar', []));

        $matches = [];

        foreach ($items as $item) {
            $title = $item['title'] ?? '';
            $meta = $item['meta'] ?? '';

            // Meta array হলে string বানাও
            if (is_array($meta)) {
                $meta = collect($meta)->flatten()->implode(' ');
            }

            // Title অথবা Meta - যেকোনো একটাতে match হলেই হবে
            $titleMatch = stripos((string) $title, $term) !== false;
            $metaMatch  = stripos((string) $meta, $term) !== false;

            if (! $titleMatch && ! $metaMatch) {
                continue;
            }

            $permission = $item['permission'] ?? '';

            if (! empty($permission) && ! hasChildPermission($permission)) {
                continue;
            }

            $matches[] = $item;

            if (count($matches) >= 10) {
                break;
            }
        }

        return response()->json($matches);
    }

    /**
     * Walks every group's items (recursing through 'children' to any
     * depth) and returns only the navigable leaves — nodes with a 'route'
     * and no 'children' of their own, since parent/group nodes are purely
     * expandable containers in this sidebar (see sidebar.blade.php's
     * renderMenu(), which only ever checks 'permission' on leaves).
     */
    private function flatten(array $groups): array
    {
        $items = [];

        $walk = function (array $nodes, array $trail) use (&$walk, &$items) {
            foreach ($nodes as $key => $node) {
                if ($key === 'group_title' || ! is_array($node)) {
                    continue;
                }

                $title = $node['title'] ?? null;

                if (isset($node['children']) && is_array($node['children'])) {
                    $walk($node['children'], $title ? [...$trail, $title] : $trail);
                    continue;
                }

                if ($title && ! empty($node['route'])) {
                    $items[] = [
                        'title'      => $title,
                        'route'      => $node['route'],
                        'icon'       => $node['icon'] ?? 'fa-solid fa-circle',
                        'breadcrumb' => implode(' / ', $trail),
                        'permission' => $node['permission'] ?? '',
                        'meta'       => $node['meta'] ?? [],
                    ];
                }
            }
        };

        foreach ($groups as $group) {
            if (! is_array($group)) {
                continue;
            }

            $groupTitle = $group['group_title'] ?? null;
            $walk($group, $groupTitle ? [$groupTitle] : []);
        }

        return $items;
    }
}
