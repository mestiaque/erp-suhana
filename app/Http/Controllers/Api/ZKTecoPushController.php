<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use MshadyDev\ZKTeco\ZKTeco;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ZKTecoPushController extends Controller
{
    /**
     * ১. হ্যান্ডশেক (মেশিন যখন সার্ভারে প্রথম নক করে)
     */
    public function handshake(Request $request)
    {
        $sn = $request->query('SN');
        Log::info("Device connected: SN=$sn");

        $response = "GET OPTION FROM: $sn\n" .
                    "Stamp=9999\n" .
                    "OpStamp=0\n" .
                    "ErrorDelay=60\n" .
                    "Delay=30\n" .
                    "TransTimes=00:00;14:00\n" .
                    "TransInterval=1\n" .
                    "TransFlag=1111111111\n" .
                    "TimeZone=6\n" .
                    "Realtime=1\n" .
                    "Encrypt=0";

        return response($response, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * ২. ডেটা গ্রহণ (অ্যাটেনডেন্স লগ গ্রহণ)
     */
    public function receiveData(Request $request)
    {
        Log::info("Data received from machine");
        return 111;
        Log::info('Attendance request received', [
            'req' => $request->all()
        ]);
    return 111;
        $sn = $request->query('SN');
        $content = $request->getContent();

        if (empty($content)) {
            return response("OK", 200)->header('Content-Type', 'text/plain');
        }

        $rows = explode("\n", $content);

        foreach ($rows as $row) {
            if (empty(trim($row))) continue;

            $data = explode("\t", $row);

            if (count($data) >= 2) {
                $userId    = trim($data[0]);
                $timestamp = trim($data[1]);

                // Attendance save helper function call
                $this->saveAttendance($userId, $timestamp, $sn);
            }
        }

        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Private helper function to handle attendance saving
     */
    private function saveAttendanceX($userId, $timestamp, $sn)
    {
        // ১. ইউজার খুঁজে বের করা
        $user = User::where('employee_id', $userId)->first();
        if (!$user) {
            Log::warning("Attendance skipped: UserID $userId not found. SN=$sn");
            return;
        }

        // ২. Timestamp parse
        $time = Carbon::parse($timestamp, 'Asia/Dhaka'); // Dhaka timezone

        // ৩. আজকের attendance খুঁজে দেখা
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('in_time', $time->toDateString())
            ->latest()
            ->first();

        // ৪. Punch type check (multi-punch ready)
        if (!$attendance || $attendance->out_time) {
            // নতুন ইন টাইম
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->in_time = $time;
            $attendance->device_sn = $sn;
            $attendance->via = 1;

            // Optional: Default geo (machine থেকে না আসলে)
            $attendance->latitude  = null;
            $attendance->longitude = null;

            // Standard start time 9:00 AM
            $standardStart = Carbon::parse($time->toDateString() . ' 09:00:00', 'Asia/Dhaka');

            if ($time->greaterThan($standardStart)) {
                $attendance->status = 'Late';
                $attendance->in_minutes = $time->diffInMinutes($standardStart);
            } else {
                $attendance->status = 'On Time';
                $attendance->in_minutes = 0;
            }

            $attendance->save();
            Log::info("Attendance In marked: User=$userId, Time=$timestamp, SN=$sn");
        } else {
            // আগের ইন আছে, এখন Out টাইম
            $attendance->out_time = $time;
            $attendance->save();
            Log::info("Attendance Out marked: User=$userId, Time=$timestamp, SN=$sn");
        }
    }

    /**
     * ৩. কমান্ড পাঠানো (মেশিনকে কোনো নির্দেশ দেওয়া)
     */
    public function getCommand(Request $request)
    {
        $sn = $request->query('SN');

        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

    /**
     * ৪. কমান্ড রিপ্লাই (মেশিন যখন জানায় কমান্ড কাজ করেছে কিনা)
     */
    public function deviceReply(Request $request)
    {
        $content = $request->getContent();
        Log::info("Device Command Reply: $content");

        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

    public function test1()
    {
        // dd(1);
        $deviceIp = '192.168.110.20';
        $zk = new ZKTeco($deviceIp, 4370, 60, 0, false, true); // verbose = true
        try {
            if ($zk->connect()) {
                // ১. ডিভাইস থেকে ইউজার এবং এটেনডেন্স আনা
                $allUsers = $zk->getUsers();
                $attendance = $zk->getAttendance();
                $deviceTime = $zk->getTime();

                // ২. এটেনডেন্স ডাটা ক্লিন এবং ফরম্যাট করা
                $readableLogs = [];
                foreach ($attendance as $log) {
                    // DateTime অবজেক্টকে স্ট্রিং-এ রূপান্তর
                    $timestamp = ($log['timestamp'] instanceof \DateTime)
                                ? $log['timestamp']->format('Y-m-d H:i:s')
                                : $log['timestamp'];

                    $readableLogs[] = [
                        'uid'        => $log['uid'],
                        'user_id'    => preg_replace('/[\x00-\x1F\x7F]/', '', $log['id']), // হিডেন ক্যারেক্টার রিমুভ
                        'state'      => $log['state'], // পাঞ্চ টাইপ (Finger, Card etc)
                        'timestamp'  => $timestamp,
                        'type'       => $log['type'] // In/Out status
                    ];
                }

                // ৩. ইউজার ডাটা ক্লিন করা
                $readableUsers = [];
                foreach ($allUsers as $user) {
                    $readableUsers[] = [
                        'uid'      => $user['uid'],
                        'user_id'  => preg_replace('/[\x00-\x1F\x7F]/', '', $user['user_id']),
                        'name'     => trim($user['name']) ?: 'No Name',
                        'privilege'=> $user['privilege']
                    ];
                }

                $zk->disconnect();

                // ৪. ফাইনাল আউটপুট
                return response()->json([
                    'status' => 'Success',
                    'info' => [
                        'device_ip'   => $deviceIp,
                        'device_time' => ($deviceTime instanceof \DateTime) ? $deviceTime->format('Y-m-d H:i:s') : $deviceTime,
                        'total_users' => count($readableUsers),
                        'total_logs'  => count($readableLogs),
                    ],
                    'data' => [
                        'attendance_logs' => $readableLogs,
                        'user_list'       => $readableUsers
                    ]
                ], 200, [], JSON_PRETTY_PRINT); // JSON_PRETTY_PRINT দিলে দেখতে সুন্দর লাগবে

            } else {
                return response()->json(['status' => 'Error', 'message' => 'ডিভাইসের সাথে কানেক্ট করা সম্ভব হয়নি।'], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'কোডে সমস্যা হয়েছে: ' . $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

    public function test()
    {
        $deviceIp = '192.168.110.20';
        $zk = new ZKTeco($deviceIp, 4370, 60, 0, false, true); // verbose = true

        try {
            if ($zk->connect()) {
                // ১. ডিভাইস থেকে ইউজার এবং এটেনডেন্স আনা
                $allUsers = $zk->getUsers();
                $attendance = $zk->getAttendance();
                $deviceTime = $zk->getTime();

                dd($allUsers, $attendance);

                // ২. ইউজার ডাটা ক্লিন করা
                $readableUsers = [];
                foreach ($allUsers as $user) {
                    $readableUsers[] = [
                        'uid'       => $user['uid'],
                        'user_id'   => preg_replace('/[\x00-\x1F\x7F]/', '', $user['user_id']),
                        'name'      => trim($user['name']) ?: 'No Name',
                        'privilege' => $user['privilege']
                    ];
                }

                // ৩. এটেনডেন্স ডাটা ক্লিন এবং DB-এ সেভ করা
                $readableLogs = [];
                foreach ($attendance as $log) {
                    $timestamp = ($log['timestamp'] instanceof \DateTime)
                                ? $log['timestamp']->format('Y-m-d H:i:s')
                                : $log['timestamp'];

                    $cleanLog = [
                        'uid'       => $log['uid'],
                        'user_id'   => preg_replace('/[\x00-\x1F\x7F]/', '', $log['id']),
                        'state'     => $log['state'],
                        'timestamp' => $timestamp,
                        'type'      => $log['type']
                    ];

                    $readableLogs[] = $cleanLog;

                    // DB-এ সেভ করা
                    $this->saveAttendance($cleanLog['user_id'], $cleanLog['timestamp'], $deviceIp);
                }

                $zk->disconnect();

                // ৪. ফাইনাল আউটপুট
                return response()->json([
                    'status' => 'Success',
                    'info' => [
                        'device_ip'   => $deviceIp,
                        'device_time' => ($deviceTime instanceof \DateTime) ? $deviceTime->format('Y-m-d H:i:s') : $deviceTime,
                        'total_users' => count($readableUsers),
                        'total_logs'  => count($readableLogs),
                    ],
                    'data' => [
                        'attendance_logs' => $readableLogs,
                        'user_list'       => $readableUsers
                    ]
                ], 200, [], JSON_PRETTY_PRINT);

            } else {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'ডিভাইসের সাথে কানেক্ট করা সম্ভব হয়নি।'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'কোডে সমস্যা হয়েছে: ' . $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

    private function saveAttendance($userId, $timestamp, $deviceSn)
    {
        // ১. ইউজার খুঁজে বের করা
        $user = User::where('employee_id', $userId)->first();
        if (!$user) {
            Log::warning("Attendance skipped: UserID $userId not found. SN=$deviceSn");
            return;
        }

        // ২. Timestamp parse (Dhaka timezone)
        try {
            $time = Carbon::parse($timestamp, 'Asia/Dhaka');
        } catch (\Exception $e) {
            Log::error("Invalid timestamp for UserID $userId: $timestamp. SN=$deviceSn");
            return;
        }

        // ৩. আজকের শেষ ইন টাইম খুঁজে বের করা
        $lastAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('in_time', $time->toDateString())
            ->latest()
            ->first();

        // ৪. Punch টাইপ চেক
        if (!$lastAttendance || $lastAttendance->out_time) {
            // নতুন ইন টাইম
            $attendance = new Attendance();
            $attendance->user_id   = $user->id;
            $attendance->in_time   = $time;
            $attendance->device_sn = $deviceSn;
            $attendance->via       = 1; // Device থেকে এসেছে
            $attendance->latitude  = null; // Optional
            $attendance->longitude = null; // Optional

            // Standard start 9:00 AM
            $standardStart = Carbon::parse($time->toDateString() . ' 09:00:00', 'Asia/Dhaka');

            if ($time->greaterThan($standardStart)) {
                $attendance->status     = 'Late';
                $attendance->in_minutes = $time->diffInMinutes($standardStart);
            } else {
                $attendance->status     = 'On Time';
                $attendance->in_minutes = 0;
            }

            $attendance->save();
            Log::info("Attendance IN marked: User=$userId, Time=$timestamp, SN=$deviceSn");
        } else {
            // আগের ইন আছে, এখন Out টাইম
            $lastAttendance->out_time = $time;
            $lastAttendance->save();
            Log::info("Attendance OUT marked: User=$userId, Time=$timestamp, SN=$deviceSn");
        }
    }

}
