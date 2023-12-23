<?php

namespace App\Console\Commands;

use App\Models\Estate;
use App\Models\UsersShift;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateUserSubstitutions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user-substitutions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command checks current replacements of estates supervisors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->substitute_active_shifts();

        $this->back_expired_substitutions();
    }

    private function substitute_active_shifts(): void
    {
        $now = Carbon::now()->format('Y-m-d');

        $active_user_shifts = UsersShift::
            where(function ($query) use($now) {
                $query->where('date_from', '<=', $now);
                $query->where('date_to', '>=', $now);
            })
            ->whereNull('temp_changes')
            ->get();

        if(count($active_user_shifts) > 0){
            foreach($active_user_shifts as $user_shift){
                $shift_user_estates_ids = $user_shift->shift_user->estates()->pluck('id')->toArray();
    
                if(count($shift_user_estates_ids) > 0){
                    DB::transaction(function() use($user_shift, $shift_user_estates_ids){
                        $user_shift->update(['temp_changes' => json_encode($shift_user_estates_ids)]);
    
                        $user_shift->shift_user->estates()->update(['supervisor_user_id' => $user_shift->substitution_user->user_id]);
                    });
                }
            }
        }
    }

    private function back_expired_substitutions(): void
    {
        $now = Carbon::now()->format('Y-m-d');

        $expired_user_shifts = UsersShift::
            where('date_to', '<', $now)
            ->whereNotNull('temp_changes')
            ->get();
            
        if(count($expired_user_shifts) > 0){
            foreach($expired_user_shifts as $user_shift){
                $temp_estates_ids = json_decode($user_shift->temp_changes);

                Estate::whereIn('id', $temp_estates_ids)->update(['supervisor_user_id' => $user_shift->shift_user->user_id]);
            }
        }
    }

}
