<option value="">@lang('form.please_select')</option>
@foreach ($requirements as $id => $name)
<option value="{{ $id }}">{{ $name }}</option>
@endforeach
