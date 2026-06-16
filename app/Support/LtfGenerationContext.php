<?php

namespace App\Support;

readonly class LtfGenerationContext
{
    public function __construct(
        public ?string $frameworkHint,
        public ?string $frameworkName,
        public ?string $deliveryMethod,
        public ?string $trainingModel,
        public ?string $programPurpose,
        public ?string $competencyLevel,
        public array   $standards,
        public array   $industries,
        public array   $audiences,
    ) {}

    public function hasContext(): bool
    {
        return $this->frameworkHint !== null
            || $this->competencyLevel !== null
            || $this->programPurpose !== null
            || ! empty($this->standards)
            || ! empty($this->industries)
            || ! empty($this->audiences);
    }

    public function toHeaderBlock(): string
    {
        if (! $this->hasContext()) return '';

        $lines = ['[LTF TAXONOMY CONTEXT]'];

        if ($this->deliveryMethod)  $lines[] = "Delivery Method: {$this->deliveryMethod}";
        if ($this->trainingModel)   $lines[] = "Training Model: {$this->trainingModel}";
        if ($this->programPurpose)  $lines[] = "Program Purpose: {$this->programPurpose}";
        if ($this->frameworkName)   $lines[] = "Learning Framework: {$this->frameworkName}";
        if ($this->competencyLevel) $lines[] = "Competency Level: " . ucfirst($this->competencyLevel);

        if (! empty($this->standards)) {
            $lines[] = "Standards/Regulations: " . implode(', ', $this->standards);
        }
        if (! empty($this->industries)) {
            $lines[] = "Target Industries: " . implode(', ', $this->industries);
        }
        if (! empty($this->audiences)) {
            $lines[] = "Target Audiences: " . implode(', ', $this->audiences);
        }

        return implode("\n", $lines);
    }

    public function toStructureInstructions(): string
    {
        if (! $this->hasContext()) return '';

        $parts = [];

        $structureHints = LtfBlockStrategy::structureHints($this->frameworkHint);
        if ($structureHints) {
            $parts[] = "[COURSE STRUCTURE GUIDANCE]\n" . $structureHints;
        }

        $depthHint = LtfBlockStrategy::lessonDepthFragment($this->frameworkHint, $this->competencyLevel);
        if ($depthHint) {
            $parts[] = $depthHint;
        }

        $compFrag = LtfBlockStrategy::competencyFragment($this->competencyLevel);
        if ($compFrag) {
            $parts[] = $compFrag;
        }

        return implode("\n\n", $parts);
    }

    public function toLessonInstructions(): string
    {
        if (! $this->hasContext()) return '';

        $parts = [];

        $blockFrag = LtfBlockStrategy::promptFragment($this->frameworkHint);
        if ($blockFrag) {
            $parts[] = "[LESSON DESIGN INSTRUCTIONS]\n" . $blockFrag;
        }

        $compFrag = LtfBlockStrategy::competencyFragment($this->competencyLevel);
        if ($compFrag) {
            $parts[] = $compFrag;
        }

        $depthFrag = LtfBlockStrategy::lessonDepthFragment($this->frameworkHint, $this->competencyLevel);
        if ($depthFrag) {
            $parts[] = $depthFrag;
        }

        $avoided = LtfBlockStrategy::avoidedBlocks($this->frameworkHint);
        if (! empty($avoided)) {
            $labels   = LtfBlockStrategy::blockLabels();
            $readable = implode(', ', array_map(fn($t) => $labels[$t] ?? $t, $avoided));
            $parts[]  = "DO NOT use these block types for this framework: {$readable}.";
        }

        if (! empty($this->industries)) {
            $parts[] = "Ground all examples in: " . implode(', ', $this->industries) . " industry context.";
        }

        return implode("\n\n", $parts);
    }

    public function toAssessmentInstructions(): string
    {
        if (! $this->hasContext()) return '';

        return "[MODULE QUIZ INSTRUCTIONS]\n" .
               LtfBlockStrategy::assessmentFragment($this->frameworkHint, $this->competencyLevel ?? 'intermediate');
    }

    public function toFinalExamInstructions(): string
    {
        if (! $this->hasContext()) return '';

        return "[FINAL ASSESSMENT INSTRUCTIONS]\n" .
               LtfBlockStrategy::finalExamFragment($this->frameworkHint, $this->competencyLevel ?? 'intermediate');
    }
}
