{{--
    The one approval email every module sends (see App\Services\ApprovalService::notify()).
    $content comes from the module's handler — see BaseApprovalHandler::mailContent() for its shape.
--}}
@php
    $general = \App\Models\General::first();
    $company = $general?->title ?: config('app.name');
    $address = $general?->address_one;
    $logo = $general && $general->logo() ? asset($general->logo()) : null;

    $badge = $content['badge'] ?? strtoupper(\Illuminate\Support\Str::before($approval->title, ' - ') ?: 'Approval Request');
    $number = $content['number'] ?? null;
    $date = $content['date'] ?? $approval->created_at?->format('d.m.Y');
    $meta = array_filter($content['meta'] ?? [], fn ($v) => $v !== null && $v !== '');
    $columns = $content['columns'] ?? [];
    $rows = $content['rows'] ?? [];
    $total = $content['total'] ?? null;
    $notes = array_filter($content['notes'] ?? [], fn ($v) => $v !== null && $v !== '');

    $td = 'border:1px solid #d5d9e0;padding:7px 9px;font-size:13px;color:#17233c;';
    $th = 'border:1px solid #d5d9e0;padding:7px 9px;font-size:12px;color:#17233c;background:#f3f5f8;text-align:left;';
    $label = 'color:#7b8794;font-size:12px;padding:3px 0;width:150px;vertical-align:top;';
    $value = 'color:#17233c;font-size:13px;padding:3px 0;font-weight:600;vertical-align:top;';
    $fmtTotal = fn ($t) => ($t['money'] ?? false) ? number_format((float) $t['value'], 2) : rtrim(rtrim(number_format((float) $t['value'], 4, '.', ','), '0'), '.');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $approval->title }}</title>
</head>
<body style="margin:0;padding:0;background:#eef1f5;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#eef1f5;padding:24px 12px;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:680px;background:#ffffff;border:1px solid #d5d9e0;border-radius:6px;">
                {{-- Company header --}}
                <tr>
                    <td style="padding:22px 24px 14px;border-bottom:2px solid #17233c;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align:top;">
                                    @if($logo)
                                        <img src="{{ $logo }}" alt="{{ $company }}" style="max-height:40px;margin-bottom:6px;display:block;">
                                    @endif
                                    <div style="font-size:20px;font-weight:700;color:#17233c;">{{ $company }}</div>
                                    @if($address)
                                        <div style="font-size:12px;color:#555;margin-top:2px;">{{ $address }}</div>
                                    @endif
                                </td>
                                <td style="vertical-align:top;text-align:right;white-space:nowrap;">
                                    <div style="display:inline-block;background:#17233c;color:#ffffff;font-size:12px;font-weight:700;letter-spacing:1px;padding:5px 12px;">{{ $badge }}</div>
                                    @if($number)
                                        <div style="font-size:12px;color:#555;margin-top:8px;">No: <strong style="color:#17233c;">{{ $number }}</strong></div>
                                    @endif
                                    @if($date)
                                        <div style="font-size:12px;color:#555;">Date: <strong style="color:#17233c;">{{ $date }}</strong></div>
                                    @endif
                                    <div style="font-size:12px;color:#b7791f;font-weight:700;margin-top:4px;">PENDING APPROVAL</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Title + description --}}
                <tr>
                    <td style="padding:16px 24px 4px;">
                        <div style="font-size:15px;font-weight:700;color:#17233c;">{{ $approval->title }}</div>
                        @if($approval->description)
                            <div style="font-size:13px;color:#555;margin-top:4px;line-height:1.5;">{{ $approval->description }}</div>
                        @endif
                    </td>
                </tr>

                {{-- Meta --}}
                @if($meta)
                    <tr>
                        <td style="padding:10px 24px 6px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                @foreach($meta as $metaLabel => $metaValue)
                                    <tr><td style="{{ $label }}">{{ $metaLabel }}</td><td style="{{ $value }}">{{ $metaValue }}</td></tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                @endif

                {{-- Lines --}}
                @if($columns && $rows)
                    <tr>
                        <td style="padding:10px 24px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        <th style="{{ $th }}width:30px;">#</th>
                                        @foreach($columns as $column)
                                            <th style="{{ $th }}{{ ($column['align'] ?? '') === 'right' ? 'text-align:right;' : '' }}">{{ $column['label'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $row)
                                        <tr>
                                            <td style="{{ $td }}">{{ $loop->iteration }}</td>
                                            @foreach(array_values($row) as $i => $cell)
                                                <td style="{{ $td }}{{ ($columns[$i]['align'] ?? '') === 'right' ? 'text-align:right;' : '' }}">
                                                    @if(is_array($cell))
                                                        {{ $cell['text'] ?? '' }}
                                                        @if(! empty($cell['sub']))
                                                            <div style="font-size:11px;color:#7b8794;">{{ $cell['sub'] }}</div>
                                                        @endif
                                                    @else
                                                        {{ $cell }}
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                                @if($total)
                                    <tfoot>
                                        <tr>
                                            <th colspan="{{ count($columns) }}" style="{{ $th }}text-align:right;font-size:13px;">{{ $total['label'] ?? 'Total' }}</th>
                                            <th style="{{ $th }}text-align:right;font-size:14px;">{{ $fmtTotal($total) }}</th>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </td>
                    </tr>
                @elseif($total)
                    <tr>
                        <td style="padding:10px 24px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                <tr>
                                    <th style="{{ $th }}text-align:right;font-size:13px;">{{ $total['label'] ?? 'Total' }}</th>
                                    <th style="{{ $th }}text-align:right;font-size:14px;width:180px;">{{ $fmtTotal($total) }}</th>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif

                @if($amountInWords)
                    <tr>
                        <td style="padding:0 24px 6px;font-size:12px;color:#555;">
                            <strong style="color:#17233c;">Taka in word:</strong> <em>{{ $amountInWords }}</em>
                        </td>
                    </tr>
                @endif

                @foreach($notes as $noteLabel => $noteText)
                    <tr>
                        <td style="padding:4px 24px 6px;font-size:12px;color:#555;">
                            <strong style="color:#17233c;">{{ $noteLabel }}:</strong> {{ $noteText }}
                        </td>
                    </tr>
                @endforeach

                {{-- Footer / action --}}
                <tr>
                    <td style="padding:14px 24px 22px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px dashed #c3c9d2;">
                            <tr>
                                <td style="padding-top:12px;font-size:12px;color:#555;vertical-align:top;">
                                    Requested by: <strong style="color:#17233c;">{{ $approval->requestedBy->name ?? 'System' }}</strong><br>
                                    Requested at: {{ $approval->created_at?->format('d.m.Y h:i A') }}
                                </td>
                                <td style="padding-top:12px;text-align:right;vertical-align:top;">
                                    <a href="{{ $approval->url }}" style="display:inline-block;background:#1769e0;color:#ffffff;text-decoration:none;padding:10px 18px;border-radius:5px;font-size:13px;font-weight:700;">Review &amp; Approve</a>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:16px 0 0;font-size:11px;color:#a0aab8;word-break:break-all;">
                            Sign in with an account that has approval permission for this module. Link: {{ $approval->url }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
