@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel' || trim($slot) === config('app.name'))
<table cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto;">
    <tr>
        <td style="padding-right: 15px; vertical-align: middle;">
             <img src="{{ asset('images/managex_logo.png') }}" alt="{{ config('app.name') }}" style="height: 50px; width: 50px; object-fit: cover; border-radius: 50%; display: block;">
        </td>
        <td style="vertical-align: middle;">
            <span style="color: #2d3748; font-size: 24px; font-weight: 800; font-family: 'Segoe UI', sans-serif; letter-spacing: -0.5px;">{{ config('app.name') }}</span>
        </td>
    </tr>
</table>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
