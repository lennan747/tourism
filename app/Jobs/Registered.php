<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Registered implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        //
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->user->parent_id == 0) {
            $tree = '0,'.$this->user->id;
        } else {
            $parent = \DB::table('users')->where('id', $this->user->parent_id)->first();
            $tree = $parent->tree.','.$this->user->id;
        }
        // 更新当前用户的tree字段
        \DB::table('users')->where('id', $this->user->id)->update(['tree' => $tree]);
    }
}
