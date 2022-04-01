<?php

namespace Tests\Feature;

use App\Jobs\TestJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        // $response = $this->get('/');
        //
        // $response->assertStatus(200);

        Queue::fake();

        Queue::assertNothingPushed();

        Queue::push(TestJob::class, '122222');

        Queue::assertPushedOn(null, TestJob::class);
    }
}
