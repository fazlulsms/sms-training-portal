# Knowledge Hub

The Knowledge Hub stores source material for future evidence-based course generation. It does not extract content or change the existing AI Course Generator.

## Storage

- Resource records are stored in `knowledge_resources`.
- Uploaded files use Laravel's private `local` disk under `storage/app/private/knowledge-hub/YYYY/MM`.
- Files are served only through authorized controller routes; no public storage link is used.
- Allowed extensions: PDF, DOCX, DOC, PPTX, XLSX, TXT, JPG, JPEG, PNG, and MP4.
- Maximum file size: 100 MB.

## Access

- Super Admin and Admin: create, view, edit, upload, download, and archive.
- Trainer: view and download Approved resources only.
- Participant: no access.

## Setup

Run:

```bash
php artisan migrate
```

Knowledge Hub appears in the management panel under Training Content and in the trainer sidebar.

## Verification

Run the focused feature tests:

```bash
php artisan test --filter=KnowledgeHubTest
```

Manual checks:

1. Create resources with PDF, DOCX, PPTX, image, and MP4 files.
2. Search by title and notes.
3. Filter by resource type, category, framework, and status.
4. Change Draft to Approved and confirm a trainer can see it.
5. Archive the resource and confirm it is no longer visible to trainers.
6. Confirm participants receive a 403 response.
