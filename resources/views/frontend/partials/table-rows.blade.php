@php
$currencySymbol = App\Models\SiteSetting::get('currency_symbol', '₹');
$currencyPosition = App\Models\SiteSetting::get('currency_position', 'before');
@endphp

@forelse($rows as $row)
@php
$data = $row->getTranslatedData($currentLang ?? 'en');
@endphp
<tr>
    @foreach($table->columns as $column)
    <td class="col-{{ $column->slug }}">
        @if($column->type === 'currency')
        @php
        $price = $data[$column->slug] ?? 0;
        $formattedPrice = number_format($price, 2);
        @endphp
        @if($currencyPosition === 'before')
        {{ $currencySymbol }}{{ $formattedPrice }}
        @else
        {{ $formattedPrice }}{{ $currencySymbol }}
        @endif
        @elseif($column->type === 'button')
        @php
        $btnData = $data[$column->slug] ?? '';
        $parts = explode('|', $btnData);
        $btnText = trim($parts[0] ?? 'Button');
        $btnLink = trim($parts[1] ?? '#');
        @endphp
        @if($btnText && $btnLink !== '#')
        <a href="{{ $btnLink }}" class="btn-action" target="_blank">
            {{ $btnText }} <i class="fas fa-external-link-alt ms-1"></i>
        </a>
        @else
        <span class="text-muted">-</span>
        @endif
        @elseif($column->type === 'number')
        {{ number_format($data[$column->slug] ?? 0) }}
        @else
        {{ $data[$column->slug] ?? '-' }}
        @endif
    </td>
    @endforeach
</tr>
@empty
<tr>
    <td colspan="{{ $table->columns->count() }}" class="no-results">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h5>No results found</h5>
        <p class="text-muted">Try adjusting your search or filters.</p>
    </td>
</tr>
@endforelse