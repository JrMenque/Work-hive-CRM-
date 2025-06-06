@extends('layouts.admin')

@section('page-title')
    {{ __('Zoom Meetings Calender') }}
@endsection

@php
    $setting = App\Models\Utility::settings();
@endphp

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Zoom Metting') }}</li>
@endsection

@section('action-button')
    <a href="{{ route('zoom-meeting.index') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary me-1"
        data-bs-original-title="{{ __('List View') }}">
        <i class="ti ti-list"></i>
    </a>
    @if (\Auth::user()->type == 'company')
        <a href="#" data-url="{{ route('zoom-meeting.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create New Zoom Meeting') }}" data-size="lg" data-bs-toggle="tooltip" title=""
            class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endif
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5>{{ __('Calendar') }}</h5>
                            <input type="hidden" id="path_admin" value="{{ url('/') }}">
                        </div>
                        <div class="col-lg-6">
                            {{-- <div class="form-group"> --}}
                            <label for=""></label>
                            @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
                                <select class="form-control" name="calender_type" id="calender_type"
                                    style="float: right;width: 155px;" onchange="get_data()">
                                    <option value="google_calender">{{ __('Google Calendar') }}</option>
                                    <option value="local_calender" selected="true">{{ __('Local Calendar') }}</option>
                                </select>
                            @endif
                            {{-- </div> --}}
                        </div>
                        <div class="card-body">
                            <div id='calendar' class='calendar'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">

            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">{{ __('Zoom Mettings') }}</h4>
                    <ul class="event-cards list-group list-group-flush mt-3 w-100">
                        @forelse ($current_month_event as $event)
                        <li class="list-group-item card mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="badge theme-avtar bg-primary">
                                        <i class="ti ti-calendar-event"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="text-primary mb-1">
                                            <a href="#" data-size="lg"
                                                data-url="{{ route('zoom-meeting.show', $event->id) }}"
                                                data-ajax-popup="true" data-title="{{ $event->title }}"
                                                class="text-primary">{{ $event->title }}</a>
                                        </h5>
                                        <div class="card-text small text-dark">
                                            {{ date('d F Y, h:i A', strtotime($event->start_date)) }}
                                        </div>
                                    </div>
                                </div>

                                @can('Delete Zoom meeting')
                                    <div class="me-2">
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'route' => ['zoom-meeting.destroy', $event->id],
                                            'id' => 'delete-form-' . $event->id,
                                        ]) !!}
                                        <a href="#" class="btn btn-sm bg-danger bs-pass-para"
                                            data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="Delete" aria-label="Delete">
                                            <span class="text-white"><i class="ti ti-trash"></i></span>
                                        </a>
                                        {!! Form::close() !!}
                                    </div>
                                @endcan
                            </div>
                        </li>

                        @empty
                            <div class="text-center">
                                <h6>{{ __('There is no zoom meetings in this month') }}</h6>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            get_data();
        });

        function get_data() {
            var calender_type = $('#calender_type :selected').val();
            $('#calendar').removeClass('local_calender');
            $('#calendar').removeClass('google_calender');
            if (calender_type == undefined) {
                calender_type = 'local_calender';
            }
            $('#calendar').addClass(calender_type);

            $.ajax({
                url: $("#path_admin").val() + "/zoom-meeting/get_zoom_meeting_data",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'calender_type': calender_type
                },
                success: function(data) {
                    (function() {
                        var etitle;
                        var etype;
                        var etypeclass;
                        var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            buttonText: {
                                timeGridDay: "{{ __('Day') }}",
                                timeGridWeek: "{{ __('Week') }}",
                                dayGridMonth: "{{ __('Month') }}"
                            },
                            slotLabelFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false,
                            },
                            themeSystem: 'bootstrap',
                            // slotDuration: '00:10:00',
                            allDaySlot: true,
                            navLinks: true,
                            droppable: true,
                            selectable: true,
                            selectMirror: true,
                            editable: true,
                            dayMaxEvents: true,
                            handleWindowResize: false,
                            events: data,
                            height: 'auto',
                            timeFormat: 'H(:mm)',
                        });
                        calendar.render();
                    })();
                }
            });

        }
    </script>

    {{-- <script type="text/javascript">
    (function () {
        var etitle;
        var etype;
        var etypeclass;
        var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridDay,timeGridWeek,dayGridMonth'
            },
            buttonText: {
                    timeGridDay: "{{__('Day')}}",
                    timeGridWeek: "{{__('Week')}}",
                    dayGridMonth: "{{__('Month')}}"
                },
            themeSystem: 'bootstrap',

            slotDuration: '00:10:00',
            navLinks: true,
            droppable: true,
            selectable: true,
            selectMirror: true,
            editable: true,
            dayMaxEvents: true,
            handleWindowResize: true,
            events:{!! $calandar!!},


        });

        calendar.render();
    })();
</script> --}}
@endpush
