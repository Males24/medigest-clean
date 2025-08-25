<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


/**
 * Gera um avatar SVG com as iniciais do utilizador.
 *
 * Rotas:
 * - GET /avatar/{user}.svg -> initials()
 *
 * Notas: usa ETag para cache e um fallback de cores
 * que combina com o tema da app.
 */
class AvatarController extends Controller
{
    /**
     * Renderiza o SVG com as iniciais do utilizador.
     */
    public function initials(Request $request, User $user)
    {
        $initials = collect(preg_split('/\s+/', trim((string) $user->name)))
            ->filter()
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
            ->take(2)
            ->implode('') ?: 'U';

        $bg  = '#E5E7EB'; // gray-200
        $fg  = '#00795C'; // brand
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 128 128" role="img" aria-label="{$initials}">
  <rect width="128" height="128" rx="64" fill="{$bg}" />
  <text x="50%" y="50%" dy=".35em" text-anchor="middle"
        font-family="Inter, system-ui, Arial" font-size="56" font-weight="700" fill="{$fg}">
    {$initials}
  </text>
</svg>
SVG;

        $etag = md5('u:'.$user->id.'|n:'.$user->name.'|t:'.optional($user->updated_at)->timestamp);

        if ($request->headers->get('If-None-Match') === $etag) {
            return response('', 304, ['ETag' => $etag]);
        }

        return new Response($svg, 200, [
            'Content-Type'  => 'image/svg+xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=604800', // 7 dias
            'ETag'          => $etag,
        ]);
    }
}
