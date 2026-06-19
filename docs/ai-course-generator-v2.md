# AI Course Generator V2

V2 is opt-in and backward compatible. Existing courses and the legacy structure-only generator remain unchanged.

## Workflow

1. Upload source material to Knowledge Hub.
2. Review extracted text and set the resource to Approved.
3. Open the eLearning course generator and choose Complete eLearning.
4. Select one or more Approved Knowledge Hub sources.
5. AI creates a source-linked course blueprint.
6. Review and approve the blueprint.
7. The queue generates lessons, module checks, question-bank records, and the final exam.
8. Review the content quality score before publishing.

Every V2 course, module, lesson, and generated question maintains a database reference to its Knowledge Hub source.

## Extraction

Automatic text extraction supports TXT, PDF, DOCX, PPTX, and XLSX. Images, legacy DOC files, and MP4 files require an approved transcript/source text entered in the resource editor.

To extract text from existing Approved resources:

```bash
php artisan knowledge:extract
```

## Deployment

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan knowledge:extract
npm ci
npm run build
php artisan optimize:clear
php artisan optimize
sudo systemctl restart php8.3-fpm
sudo systemctl restart supervisor
```

The queue worker must be running because blueprint approval dispatches the complete-course generation job.

## Quality controls

The quality service checks blueprint approval, source coverage, objectives, lesson content, module checks, final exam presence, and estimated learning duration. V2 courses remain drafts for administrator review.
