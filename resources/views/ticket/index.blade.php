@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Ticket') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Manage Ticket') }}</li>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
@endpush

@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
@endpush

@section('action-button')
    @can('Create Ticket')
        <a href="#" data-url="{{ route('ticket.create') }}" data-ajax-popup="true" data-title="{{ __('Create New Ticket') }}"
            data-size="lg" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
            data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection


@section('content')
    <div class="row">

        <div class="col-xxl-8">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="card ticket-card">
                        <div class="card-body">
                            <div class="badge theme-avtar bg-info">
                                <i class="ti ti-ticket"></i>
                            </div>
                            <p class="text-muted text-sm mt-4 mb-2"></p>
                            <h6 class="mb-3">{{ __('Total Ticket') }}</h6>
                            <h3 class="mb-0">{{ $countTicket }} </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card ticket-card">
                        <div class="card-body">
                            <div class="badge theme-avtar bg-primary">
                                <i class="ti ti-ticket"></i>
                            </div>
                            <p class="text-muted text-sm mt-4 mb-2"></p>
                            <h6 class="mb-3">{{ __('Open Ticket') }}</h6>
                            <h3 class="mb-0">{{ $countOpenTicket }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card ticket-card">
                        <div class="card-body">
                            <div class="badge theme-avtar bg-warning">
                                <i class="ti ti-ticket"></i>
                            </div>
                            <p class="text-muted text-sm mt-4 mb-2"></p>
                            <h6 class="mb-3">{{ __('Hold Ticket') }}</h6>
                            <h3 class="mb-0">{{ $countonholdTicket }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card ticket-card">
                        <div class="card-body">
                            <div class="badge theme-avtar bg-danger">
                                <i class="ti ti-ticket"></i>
                            </div>
                            <p class="text-muted text-sm mt-4 mb-2"></p>
                            <h6 class="mb-3">{{ __('Close Ticket') }}</h6>
                            <h3 class="mb-0">{{ $countCloseTicket }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4">
            <div class="card">
                <div class="card-header">
                    <div class="float-end">

                    </div>
                    <h5>{{ __('Ticket By Status') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div id="projects-chart"></div>
                        </div>
                        <div class="col-6">
                            <div class="row mt-3">
                                <div class="col-6">
                                    <span class="d-flex align-items-center mb-2">
                                        <i class="f-10 lh-1 fas fa-circle text-danger"></i>
                                        <span class="ms-2 text-sm">{{ __('Close') }} </span>
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span class="d-flex align-items-center mb-2">
                                        <i class="f-10 lh-1 fas fa-circle text-warning"></i>
                                        <span class="ms-2 text-sm">{{ __('Hold') }}</span>
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span class="d-flex align-items-center mb-2">
                                        <i class="f-10 lh-1 fas fa-circle text-info"></i>
                                        <span class="ms-2 text-sm">{{ __('Total') }}</span>
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span class="d-flex align-items-center mb-2">
                                        <i class="f-10 lh-1 fas fa-circle text-primary"></i>
                                        <span class="ms-2 text-sm">{{ __('Open') }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                {{-- <h5></h5> --}}
                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>{{ __('New') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Ticket Code') }}</th>
                                @role('company')
                                    <th>{{ __('Employee') }}</th>
                                @endrole
                                <th>{{ __('Priority') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Created By') }}</th>
                                <th>{{ __('Status') }}</th>
                                {{-- <th>{{ __('Description') }}</th> --}}
                                <th width="200px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td>
                                        @if (\Auth::user()->type == 'employee')
                                            @if ($ticket->ticketUnread() > 0)
                                                <i title="New Message" class="fas fa-circle circle text-success"></i>
                                            @endif
                                        @else
                                            @if ($ticket->ticketUnread() > 0)
                                                <i title="New Message" class="fas fa-circle circle text-success"></i>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $ticket->title }}</td>
                                    <td>{{ $ticket->ticket_code }}</td>
                                    @role('company')
                                        <td>{{ !empty(\Auth::user()->getUser($ticket->employee_id)) ? \Auth::user()->getUser($ticket->employee_id)->name : '' }}
                                        </td>
                                    @endrole
                                    <td>
                                        @if ($ticket->priority == 'medium')
                                            <div class="status_badge text-capitalize badge bg-info ticket-set ">
                                                {{ __('Medium') }}
                                            </div>
                                        @elseif($ticket->priority == 'low')
                                            <div class="status_badge text-capitalize badge bg-success ticket-set ">
                                                {{ __('Low') }}</div>
                                        @elseif($ticket->priority == 'high')
                                            <div class="status_badge text-capitalize badge bg-warning ticket-set ">
                                                {{ __('High') }}</div>
                                        @elseif($ticket->priority == 'critical')
                                            <div class="status_badge text-capitalize badge bg-danger ticket-set ">
                                                {{ __('Critical') }}</div>
                                        @endif
                                    </td>
                                    <td>{{ \Auth::user()->dateFormat($ticket->end_date) }}</td>
                                    <td>{{ !empty($ticket->createdBy) ? $ticket->createdBy->name : '' }}</td>
                                    <td>
                                        @if ($ticket->status == 'open')
                                            <div class="status_badge text-capitalize badge bg-success ticket-set ">
                                                {{ __('Open') }}</div>
                                        @elseif($ticket->status == 'close')
                                            <div class="status_badge text-capitalize badge bg-danger ticket-set ">
                                                {{ __('Close') }}</div>
                                        @elseif($ticket->status == 'onhold')
                                            <div class="status_badge text-capitalize badge bg-warning ticket-set ">
                                                {{ __('On Hold') }}</div>
                                        @endif
                                    </td>
                                    {{-- <td>
                                        <p
                                            style="white-space: nowrap;
                                            width: 200px;
                                            overflow: hidden;
                                            text-overflow: ellipsis;">
                                            {{ $ticket->description }}</p>
                                    </td> --}}
                                    <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="{{ URL::to('ticket/' . $ticket->id . '/reply') }}"
                                                    class="mx-3 btn btn-sm bg-primary align-items-center" data-bs-toggle="tooltip"
                                                    title="" data-title="{{ __('Replay') }}"
                                                    data-bs-original-title="Reply">
                                                    <span class="text-white"><i class="ti ti-arrow-back-up"></i></span>
                                                </a>
                                            </div>
                                            @if (\Auth::user()->type == 'company' || $ticket->ticket_created == \Auth::user()->id)
                                                @can('Delete Ticket')
                                                    <div class="action-btn">
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['ticket.destroy', $ticket->id],
                                                            'id' => 'delete-form-' . $ticket->id,
                                                        ]) !!}
                                                        <a href="#"
                                                        data-bs-trigger="hover"
                                                            class="btn btn-sm bg-danger align-items-center bs-pass-para"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Delete" aria-label="Delete"><span class="text-white"><i
                                                                class="ti ti-trash"></i></span></a>
                                                        </form>
                                                    </div>
                                                @endcan
                                            @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        (function() {
            var options = {
                chart: {
                    height: 140,
                    type: 'donut',
                },
                dataLabels: {
                    enabled: false,
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                        }
                    }
                },
                series: {{ $ticket_arr }},
                colors: ["#3ec9d6", '#6fd943', '#fd7e14', '#ff3a6e'],
                labels: ["Total", "Open", "Hold", "Close"],
                legend: {
                    show: false
                }
            };
            var chart = new ApexCharts(document.querySelector("#projects-chart"), options);
            chart.render();
        })();
    </script>
@endpush
