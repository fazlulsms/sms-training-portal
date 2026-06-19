<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeResource;
use App\Services\KnowledgeResourceTextExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KnowledgeResourceController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', KnowledgeResource::class);

        $query = KnowledgeResource::query()->latest();

        if ($request->user()->isTrainer()) {
            $query->approved();
        }

        $query
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = trim((string) $request->input('search'));
                $query->where(function ($query) use ($term) {
                    $query->where('title', 'like', "%{$term}%")
                        ->orWhere('notes', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('resource_type'), fn ($query) => $query->where('resource_type', $request->input('resource_type')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->input('category')))
            ->when($request->filled('standard_framework'), fn ($query) => $query->where('standard_framework', $request->input('standard_framework')))
            ->when($request->filled('status') && $request->user()->isAdmin(), fn ($query) => $query->where('status', $request->input('status')));

        $resources = $query->paginate(20)->withQueryString();
        $frameworks = KnowledgeResource::query()
            ->when($request->user()->isTrainer(), fn ($query) => $query->approved())
            ->distinct()
            ->orderBy('standard_framework')
            ->pluck('standard_framework');

        return view('knowledge-hub.index', compact('resources', 'frameworks'));
    }

    public function create()
    {
        Gate::authorize('create', KnowledgeResource::class);

        return view('knowledge-hub.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', KnowledgeResource::class);
        $validated = $this->validateResource($request, true);
        $file = $request->file('file');
        $path = $file->store('knowledge-hub/'.now()->format('Y/m'), 'local');

        $resource = KnowledgeResource::create([
            ...$validated,
            'file_disk' => 'local',
            'file_path' => $path,
            'original_file_name' => Str::limit($file->getClientOriginalName(), 255, ''),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'approved_at' => $validated['status'] === 'approved' ? now() : null,
            'archived_at' => $validated['status'] === 'archived' ? now() : null,
            'uploaded_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
        if (!app()->environment('testing')) {
            app(KnowledgeResourceTextExtractor::class)->extract($resource);
        }

        return redirect()->route('knowledge-hub.index')
            ->with('success', 'Knowledge resource created successfully.');
    }

    public function show(KnowledgeResource $knowledgeResource)
    {
        Gate::authorize('view', $knowledgeResource);

        return view('knowledge-hub.show', ['resource' => $knowledgeResource->load('uploader')]);
    }

    public function edit(KnowledgeResource $knowledgeResource)
    {
        Gate::authorize('update', $knowledgeResource);

        return view('knowledge-hub.edit', ['resource' => $knowledgeResource]);
    }

    public function update(Request $request, KnowledgeResource $knowledgeResource)
    {
        Gate::authorize('update', $knowledgeResource);
        $validated = $this->validateResource($request, false);
        $oldStatus = $knowledgeResource->status;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $newPath = $file->store('knowledge-hub/'.now()->format('Y/m'), 'local');
            Storage::disk($knowledgeResource->file_disk)->delete($knowledgeResource->file_path);
            $validated += [
                'file_disk' => 'local',
                'file_path' => $newPath,
                'original_file_name' => Str::limit($file->getClientOriginalName(), 255, ''),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ];
        }

        $validated['approved_at'] = $validated['status'] === 'approved'
            ? ($knowledgeResource->approved_at ?? now())
            : null;
        $validated['archived_at'] = $validated['status'] === 'archived'
            ? ($oldStatus === 'archived' ? $knowledgeResource->archived_at : now())
            : null;
        $validated['updated_by'] = $request->user()->id;

        $knowledgeResource->update($validated);
        if (!app()->environment('testing') && ($request->hasFile('file') || $knowledgeResource->extraction_status === 'pending')) {
            app(KnowledgeResourceTextExtractor::class)->extract($knowledgeResource->refresh());
        }

        return redirect()->route('knowledge-hub.show', $knowledgeResource)
            ->with('success', 'Knowledge resource updated successfully.');
    }

    public function archive(Request $request, KnowledgeResource $knowledgeResource)
    {
        Gate::authorize('archive', $knowledgeResource);
        $knowledgeResource->update([
            'status' => 'archived',
            'approved_at' => null,
            'archived_at' => now(),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->route('knowledge-hub.index')
            ->with('success', 'Knowledge resource archived.');
    }

    public function viewFile(KnowledgeResource $knowledgeResource)
    {
        Gate::authorize('view', $knowledgeResource);
        abort_unless(Storage::disk($knowledgeResource->file_disk)->exists($knowledgeResource->file_path), 404);

        return Storage::disk($knowledgeResource->file_disk)->response(
            $knowledgeResource->file_path,
            $knowledgeResource->original_file_name,
            ['Content-Type' => $knowledgeResource->mime_type ?: 'application/octet-stream']
        );
    }

    public function download(KnowledgeResource $knowledgeResource)
    {
        Gate::authorize('download', $knowledgeResource);
        abort_unless(Storage::disk($knowledgeResource->file_disk)->exists($knowledgeResource->file_path), 404);

        return Storage::disk($knowledgeResource->file_disk)->download(
            $knowledgeResource->file_path,
            $knowledgeResource->original_file_name
        );
    }

    private function validateResource(Request $request, bool $fileRequired): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'resource_type' => ['required', Rule::in(KnowledgeResource::RESOURCE_TYPES)],
            'category' => ['required', Rule::in(KnowledgeResource::CATEGORIES)],
            'subcategory' => ['nullable', 'string', 'max:120'],
            'standard_framework' => ['required', 'string', 'max:150'],
            'clause_number' => ['nullable', 'string', 'max:80'],
            'version' => ['nullable', 'string', 'max:50'],
            'difficulty_level' => ['required', Rule::in(['beginner', 'intermediate', 'advanced', 'expert'])],
            'status' => ['required', Rule::in(KnowledgeResource::STATUSES)],
            'notes' => ['nullable', 'string', 'max:10000'],
            'learning_objectives' => ['nullable', 'string', 'max:10000'],
            'source_references' => ['nullable', 'string', 'max:10000'],
            'extracted_text' => ['nullable', 'string'],
            'file' => [
                $fileRequired ? 'required' : 'nullable',
                'file',
                'mimes:pdf,docx,doc,pptx,xlsx,txt,jpg,jpeg,png,mp4',
                'max:102400',
            ],
        ]);
    }
}
