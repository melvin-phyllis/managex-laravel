@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel' || trim($slot) === config('app.name'))
<table cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td style="padding-right: 10px; vertical-align: middle;">
            <div style="background: linear-gradient(135deg, #5680E9, #8860D0); width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <span style="color: white; font-weight: bold; font-size: 20px;">M</span>
            </div>
        </td>
        <td style="vertical-align: middle;">
            <span style="color: #ffffff; font-size: 24px; font-weight: 700; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">{{ config('app.name') }}</span>
        </td>
    </tr>
</table>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
