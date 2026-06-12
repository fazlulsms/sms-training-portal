<?php

namespace App\Http\Controllers;

use App\Models\AiPromptTemplate;
use App\Models\AiPromptTemplateVersion;
use App\Services\OpenAIService;
use Illuminate\Http\Request;

class AiPromptTemplateController extends Controller
{
    public function __construct(private OpenAIService $ai) {}

    private function guardSuperAdmin(): void
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'Prompt Template administration is restricted to Super Admins.');
        }
    }

    private function categories(): array
    {
        return config('ai.prompt_categories', []);
    }

    // ── Index ─────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->guardSuperAdmin();

        $query = AiPromptTemplate::with('creator')->orderBy('category')->orderBy('template_name');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('template_name', 'like', '%' . $request->search . '%')
                  ->orWhere('template_code', 'like', '%' . $request->search . '%');
            });
        }

        $templates   = $query->get();
        $categories  = $this->categories();
        $allCategories = collect($categories)->keys();

        return view('ai.prompt-templates.index', compact('templates', 'categories', 'allCategories'));
    }

    // ── Create / Store ────────────────────────────────────────

    public function create()
    {
        $this->guardSuperAdmin();
        $categories = $this->categories();
        return view('ai.prompt-templates.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->guardSuperAdmin();

        $data = $request->validate([
            'template_name'              => 'required|string|max:200',
            'template_code'              => 'required|string|max:100|unique:ai_prompt_templates,template_code|alpha_dash',
            'category'                   => 'required|string|max:100',
            'description'                => 'nullable|string|max:1000',
            'system_prompt'              => 'required|string',
            'user_prompt_template'       => 'required|string',
            'output_format_instructions' => 'nullable|string',
            'model_override'             => 'nullable|string|max:50',
            'temperature'                => 'nullable|numeric|min:0|max:2',
            'max_tokens'                 => 'nullable|integer|min:100|max:16000',
            'is_active'                  => 'boolean',
        ]);

        $data['created_by']    = auth()->id();
        $data['updated_by']    = auth()->id();
        $data['is_active']     = $request->boolean('is_active', true);
        $data['version_number'] = 1;

        AiPromptTemplate::create($data);

        return redirect()->route('ai.prompt-templates.index')
            ->with('success', "Template \"{$data['template_name']}\" created successfully.");
    }

    // ── Show ──────────────────────────────────────────────────

    public function show(AiPromptTemplate $promptTemplate)
    {
        $this->guardSuperAdmin();
        return view('ai.prompt-templates.show', ['template' => $promptTemplate]);
    }

    // ── Edit / Update ─────────────────────────────────────────

    public function edit(AiPromptTemplate $promptTemplate)
    {
        $this->guardSuperAdmin();
        $categories = $this->categories();
        return view('ai.prompt-templates.edit', ['template' => $promptTemplate, 'categories' => $categories]);
    }

    public function update(Request $request, AiPromptTemplate $promptTemplate)
    {
        $this->guardSuperAdmin();

        $data = $request->validate([
            'template_name'              => 'required|string|max:200',
            'category'                   => 'required|string|max:100',
            'description'                => 'nullable|string|max:1000',
            'system_prompt'              => 'required|string',
            'user_prompt_template'       => 'required|string',
            'output_format_instructions' => 'nullable|string',
            'model_override'             => 'nullable|string|max:50',
            'temperature'                => 'nullable|numeric|min:0|max:2',
            'max_tokens'                 => 'nullable|integer|min:100|max:16000',
            'is_active'                  => 'boolean',
        ]);

        // Snapshot the current version before overwriting
        $promptTemplate->snapshotVersion(auth()->id());

        $data['updated_by']    = auth()->id();
        $data['is_active']     = $request->boolean('is_active', $promptTemplate->is_active);
        $data['version_number'] = $promptTemplate->version_number + 1;

        $promptTemplate->update($data);

        return redirect()->route('ai.prompt-templates.edit', $promptTemplate)
            ->with('success', "Template updated to version {$promptTemplate->version_number}.");
    }

    // ── Archive (soft delete) ─────────────────────────────────

    public function destroy(AiPromptTemplate $promptTemplate)
    {
        $this->guardSuperAdmin();

        $promptTemplate->snapshotVersion(auth()->id());
        $promptTemplate->delete();

        return redirect()->route('ai.prompt-templates.index')
            ->with('success', "Template \"{$promptTemplate->template_name}\" archived.");
    }

    // ── Toggle active/inactive ────────────────────────────────

    public function toggle(AiPromptTemplate $promptTemplate)
    {
        $this->guardSuperAdmin();

        $promptTemplate->update(['is_active' => ! $promptTemplate->is_active]);

        $state = $promptTemplate->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Template \"{$promptTemplate->template_name}\" {$state}.");
    }

    // ── Clone ─────────────────────────────────────────────────

    public function clone(AiPromptTemplate $promptTemplate)
    {
        $this->guardSuperAdmin();

        $newCode = $promptTemplate->template_code . '_copy_' . time();

        AiPromptTemplate::create([
            'template_name'              => $promptTemplate->template_name . ' (Copy)',
            'template_code'              => $newCode,
            'category'                   => $promptTemplate->category,
            'description'                => $promptTemplate->description,
            'system_prompt'              => $promptTemplate->system_prompt,
            'user_prompt_template'       => $promptTemplate->user_prompt_template,
            'output_format_instructions' => $promptTemplate->output_format_instructions,
            'model_override'             => $promptTemplate->model_override,
            'temperature'                => $promptTemplate->temperature,
            'max_tokens'                 => $promptTemplate->max_tokens,
            'is_active'                  => false,
            'version_number'             => 1,
            'created_by'                 => auth()->id(),
            'updated_by'                 => auth()->id(),
        ]);

        return redirect()->route('ai.prompt-templates.index')
            ->with('success', "Cloned \"{$promptTemplate->template_name}\" — edit and activate the copy when ready.");
    }

    // ── Version History ───────────────────────────────────────

    public function versions(AiPromptTemplate $promptTemplate)
    {
        $this->guardSuperAdmin();
        $versions = $promptTemplate->versions()->with('savedBy')->get();
        return view('ai.prompt-templates.versions', ['template' => $promptTemplate, 'versions' => $versions]);
    }

    public function rollback(AiPromptTemplate $promptTemplate, AiPromptTemplateVersion $version)
    {
        $this->guardSuperAdmin();

        if ($version->template_id !== $promptTemplate->id) {
            abort(404);
        }

        // Snapshot current state before rollback
        $promptTemplate->snapshotVersion(auth()->id());

        $promptTemplate->update([
            'template_name'              => $version->template_name,
            'category'                   => $version->category,
            'description'                => $version->description,
            'system_prompt'              => $version->system_prompt,
            'user_prompt_template'       => $version->user_prompt_template,
            'output_format_instructions' => $version->output_format_instructions,
            'model_override'             => $version->model_override,
            'temperature'                => $version->temperature,
            'max_tokens'                 => $version->max_tokens,
            'updated_by'                 => auth()->id(),
            'version_number'             => $promptTemplate->version_number + 1,
        ]);

        return redirect()->route('ai.prompt-templates.versions', $promptTemplate)
            ->with('success', "Rolled back to version {$version->version_number}. Current version is now {$promptTemplate->version_number}.");
    }

    // ── Test ──────────────────────────────────────────────────

    public function test(Request $request, AiPromptTemplate $promptTemplate)
    {
        $this->guardSuperAdmin();

        $request->validate([
            'test_input' => 'required|string|min:5|max:4000',
        ]);

        if (! $promptTemplate->is_active) {
            return response()->json([
                'success' => false,
                'error'   => 'This template is inactive. Activate it before testing.',
            ]);
        }

        $result = $this->ai->generateFromTemplate(
            $promptTemplate,
            $request->input('test_input'),
            auth()->id()
        );

        return response()->json($result);
    }
}
