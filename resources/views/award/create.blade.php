@php
    $plan = App\Models\Utility::getChatGPTSettings();
@endphp

{{ Form::open(['url' => 'award', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">

    @if ($plan->enable_chatgpt == 'on')
    <div class="card-footer text-end">
        <a href="#" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true"
            data-url="{{ route('generate', ['award']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
            <i class="fas fa-robot"></i>{{ __(' Generate With AI') }}
        </a>
    </div>
    @endif

    <div class="row">
        <div class="form-group col-md-6 col-lg-6 ">
            {{ Form::label('employee_id', __('Employee'), ['class' => 'col-form-label']) }}<x-required></x-required>
            {{ Form::select('employee_id', $employees, null, ['class' => 'form-control', 'required' => 'required']) }}
            <div class="text-xs mt-1">
                {{ __('Create employee.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Click here') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6 col-lg-6">
            {{ Form::label('award_type', __('Award Type'), ['class' => 'col-form-label']) }}<x-required></x-required>
            {{ Form::select('award_type', $awardtypes, null, ['class' => 'form-control' , 'required' => 'required']) }}
            <div class="text-xs mt-1">
                {{ __('Create award type.') }} <a href="{{ route('awardtype.index') }}"><b>{{ __('Click here') }}</b></a>
            </div>
        </div>
        <div class="form-group col-md-6 col-lg-6">
            {{ Form::label('date', __('Date'), ['class' => 'col-form-label']) }}<x-required></x-required>
            {{ Form::text('date', null, ['class' => 'form-control d_week current_date','autocomplete'=>'off' , 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6 col-lg-6">
            {{ Form::label('gift', __('Gift'), ['class' => 'col-form-label']) }}<x-required></x-required>
            {{ Form::text('gift', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Gift')]) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'col-form-label ']) }}<x-required></x-required>
            {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Enter Description'),'rows'=>'3' , 'required' => 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>

{{ Form::close() }}

<script>
    $(document).ready(function() {
        var now = new Date();
        var month = (now.getMonth() + 1);
        var day = now.getDate();
        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;
        var today = now.getFullYear() + '-' + month + '-' + day;
        $('.current_date').val(today);
    });
</script>
