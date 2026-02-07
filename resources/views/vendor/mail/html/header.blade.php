@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel' || trim($slot) === config('app.name'))
<table cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto;">
    <tr>
        <td style="padding-right: 12px; vertical-align: middle;">
            <div style="background-color: #5680E9; width: 40px; height: 40px; border-radius: 8px; text-align: center; line-height: 40px;">
                <span style="color: white; font-weight: bold; font-size: 22px; font-family: sans-serif;">M</span>
            </div>
        </td>
        <td style="vertical-align: middle;">
            <span style="color: #2d3748; font-size: 26px; font-weight: 800; font-family: 'Segoe UI', sans-serif; letter-spacing: -0.5px;">{{ config('app.name') }}</span>
        </td>
    </tr>
</table>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
