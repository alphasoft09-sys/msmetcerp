<option value="">All Sectors</option>
@foreach($sectors as $sector)
    <option value="{{ $sector }}" {{ request('sector') == $sector ? 'selected' : '' }}>
        {{ $sector }}
    </option>
@endforeach 