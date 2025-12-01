<?php
namespace App\Console\Commands;

use Mail;
use Carbon\Carbon;
use App\Models\Attribute;
use App\Models\Transaction;
use Illuminate\Console\Command;

class EmailNotificationsDaily extends Command
{


    protected $signature = 'DailyEmail:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Email Notifications';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $accounts =Attribute::with('user')->where('type',10)->where('status','active')->orderBy('name')->select(['id','name','amount'])->get();
        $from =Carbon::now();
        $to = clone $from;
        $accounts = collect($accounts)->map(function($acc) use ($from,$to) {

            // Opening Balance calculation
            $openingBalance = Transaction::where('account_id', $acc->id)
                ->whereDate('created_at', '<', $from)
                ->selectRaw("
                    SUM(
                        CASE
                            WHEN type IN (0,1) THEN amount
                            WHEN type IN (3,4,5,6,7) THEN -amount
                            ELSE 0
                        END
                    ) as balance
                ")
                ->value('balance') ?? 0;

            // Fetch transactions inside date range
            $transactions = Transaction::where('account_id', $acc->id)
                ->where('status','success')
                ->whereIn('type', [0,1,3,4,5,6,7])
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->orderBy('created_at')
                ->get();

            // Running balance generation
            $balance = $openingBalance;

            $transactions->map(function($t) use (&$balance) {
                if (in_array($t->type, [0,1])) {
                    $balance += $t->amount;
                } else {
                    $balance -= $t->amount;
                }
                $t->running_balance = $balance;
                return $t;
            });

            // Append calculated values into account object
            $acc->opening_balance   = $openingBalance;
            $acc->available_balance = $balance;
            $acc->transactions      = $transactions;

            return $acc;
        });
        
        
        $emails =['info@natoreit.com','rabiulk449@gmail.com'];
        $toName =general()->title;
        $subject ='Erp Software Daily Summery Report Mail Form '.general()->title;
        $datas =['accounts'=>$accounts];
        $template ='mails.summeryDailyAdminMail';

        foreach ($emails as $toEmail){
            sendMail($toEmail,$toName,$subject,$datas,$template);
        }
        
        \Log::info('Every Hour after Mail Send Notification');
    }
    
    
    
}