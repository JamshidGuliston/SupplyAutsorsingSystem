<?php

namespace Tests\Unit\Services\Push;

use App\Services\Push\MulticastResultParser;
use Tests\TestCase;

class MulticastResultParserTest extends TestCase
{
    private MulticastResultParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new MulticastResultParser();
    }

    public function test_empty_report_returns_zero_counts_and_no_invalid_tokens(): void
    {
        $result = $this->parser->classify([], []);

        $this->assertSame(0, $result['success_count']);
        $this->assertSame(0, $result['failure_count']);
        $this->assertEqualsCanonicalizing([], $result['invalid_tokens']);
    }

    public function test_all_success_reports_count_as_success_with_no_invalid_tokens(): void
    {
        $reports = [
            ['token' => 'tokA', 'success' => true, 'errorCode' => null],
            ['token' => 'tokB', 'success' => true, 'errorCode' => null],
            ['token' => 'tokC', 'success' => true, 'errorCode' => null],
        ];

        $result = $this->parser->classify($reports, ['tokA', 'tokB', 'tokC']);

        $this->assertSame(3, $result['success_count']);
        $this->assertSame(0, $result['failure_count']);
        $this->assertEqualsCanonicalizing([], $result['invalid_tokens']);
    }

    public function test_permanent_failure_token_is_flagged_invalid(): void
    {
        $reports = [
            ['token' => 'tok1', 'success' => false, 'errorCode' => 'messaging/registration-token-not-registered'],
        ];

        $result = $this->parser->classify($reports, ['tok1']);

        $this->assertSame(0, $result['success_count']);
        $this->assertSame(1, $result['failure_count']);
        $this->assertEqualsCanonicalizing(['tok1'], $result['invalid_tokens']);
    }

    public function test_transient_failure_token_is_not_flagged_invalid(): void
    {
        $reports = [
            ['token' => 'tokT', 'success' => false, 'errorCode' => 'messaging/server-unavailable'],
        ];

        $result = $this->parser->classify($reports, ['tokT']);

        $this->assertSame(0, $result['success_count']);
        $this->assertSame(1, $result['failure_count']);
        $this->assertEqualsCanonicalizing([], $result['invalid_tokens']);
    }

    public function test_all_known_permanent_codes_flag_their_tokens(): void
    {
        $reports = [
            ['token' => 'tokInvalidArg', 'success' => false, 'errorCode' => 'messaging/invalid-argument'],
            ['token' => 'tokInvalidReg', 'success' => false, 'errorCode' => 'messaging/invalid-registration-token'],
            ['token' => 'tokNotReg',     'success' => false, 'errorCode' => 'messaging/registration-token-not-registered'],
        ];

        $result = $this->parser->classify($reports, ['tokInvalidArg', 'tokInvalidReg', 'tokNotReg']);

        $this->assertSame(0, $result['success_count']);
        $this->assertSame(3, $result['failure_count']);
        $this->assertEqualsCanonicalizing(
            ['tokInvalidArg', 'tokInvalidReg', 'tokNotReg'],
            $result['invalid_tokens']
        );
    }
}
