<?php

namespace App\Http\Controllers;

use App\Models\PortalResource;
use App\Services\ResourceDocumentSeederExporter;
use App\Services\ResourceDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResourceDocumentsController extends Controller
{
    public function __construct(
        private readonly ResourceDocumentService $documents,
        private readonly ResourceDocumentSeederExporter $documentSeederExporter,
    ) {}

    public function index(Request $request): View
    {
        $payload = $this->documents->libraryPayload(
            search: $request->string('search')->toString() ?: null,
            category: $request->string('category')->toString() ?: null,
        );

        return view('resources.documents', [
            'user' => $request->user(),
            'library' => $payload,
            'canManageDocuments' => $request->user()->canManageDocuments(),
            'canUpdateDocumentSeeder' => $request->user()->canUpdateDocumentSeeder(),
            'previewDocumentId' => $request->integer('document') ?: null,
        ]);
    }

    public function updateSeeder(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canUpdateDocumentSeeder(), 403);

        $result = $this->documentSeederExporter->export();

        return redirect()
            ->route('resources.documents')
            ->with('status', 'document-seeder-updated')
            ->with('seeder_count', $result['count']);
    }

    public function preview(PortalResource $portalResource): JsonResponse
    {
        $this->ensurePublishedDocument($portalResource);
        abort_unless($portalResource->canPreview(), 404);

        $hasRtf = $portalResource->hasHtmlPreview();
        $hasPdf = $portalResource->hasPdfPreview();

        return response()->json([
            'id' => $portalResource->id,
            'title' => $portalResource->title,
            'description' => $portalResource->description,
            'format' => $portalResource->resolvedFormat(),
            'has_rtf' => $hasRtf,
            'has_pdf' => $hasPdf,
            'default_view' => $hasRtf ? 'rtf' : ($hasPdf ? 'pdf' : 'summary'),
            'rtf_html' => $hasRtf ? $portalResource->content : null,
            'pdf_url' => $hasPdf ? $portalResource->inlinePreviewUrl() : null,
            'external_url' => filled($portalResource->url) ? $portalResource->resolvedAccessUrl() : null,
            'download_url' => $portalResource->resolvedAccessUrl(),
        ]);
    }

    public function view(PortalResource $portalResource): BinaryFileResponse
    {
        $this->ensurePublishedDocument($portalResource);
        abort_unless($portalResource->hasDownloadableFile(), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($portalResource->file_path), 404);

        $filename = basename($portalResource->file_path) ?: str($portalResource->title)->slug().'.pdf';

        return response()->file(
            $disk->path($portalResource->file_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
            ],
        );
    }

    public function download(PortalResource $portalResource): StreamedResponse
    {
        $this->ensurePublishedDocument($portalResource);
        abort_unless($portalResource->hasDownloadableFile(), 404);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($portalResource->file_path), 404);

        $filename = basename($portalResource->file_path) ?: str($portalResource->title)->slug().'.'.$portalResource->resolvedFormat();

        return $disk->download($portalResource->file_path, $filename);
    }

    private function ensurePublishedDocument(PortalResource $portalResource): void
    {
        abort_unless($portalResource->is_published && $portalResource->isDocumentLibraryItem(), 404);
    }
}
