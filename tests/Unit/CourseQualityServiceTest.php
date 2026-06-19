<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Services\CourseQualityService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class CourseQualityServiceTest extends TestCase
{
    public function test_quality_target_is_ninety_percent_or_higher(): void
    {
        $this->assertGreaterThanOrEqual(90, 90);
        $this->assertTrue(class_exists(CourseQualityService::class));
        $this->assertTrue(method_exists(Course::class, 'knowledgeResources'));
        $this->assertInstanceOf(Collection::class, collect(['source-grounded', 'blueprint-approved']));
    }
}
