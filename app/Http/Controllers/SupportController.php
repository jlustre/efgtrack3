<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\DocumentationMarkdown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SupportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $user->loadMissing([
            'profile',
            'rank',
            'team',
            'sponsor',
        ]);

        return view('support.index', [
            'user' => $user,
        ]);
    }

    public function documentation(string $guide): View
    {
        $entry = collect(config('support-documentation.modules', []))
            ->first(fn (array $module): bool => ($module['slug'] ?? null) === $guide);

        if (! $entry || empty($entry['file'])) {
            throw new NotFoundHttpException();
        }

        $path = base_path('docs/'.$entry['file']);

        if (! File::isFile($path)) {
            throw new NotFoundHttpException();
        }

        return view('support.documentation', [
            'title' => $entry['module'],
            'summary' => $entry['summary'] ?? '',
            'content' => DocumentationMarkdown::toHtml(File::get($path)),
        ]);
    }
}
