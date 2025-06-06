@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Termination') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Termination') }}</li>
@endsection


@section('action-button')
    @can('Create Termination')
        <a href="#" data-url="{{ route('termination.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create New Termination') }}" data-size="lg" data-bs-toggle="tooltip" title=""
            class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection

@section('content')
    <div class="row">

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    {{-- <h5> </h5> --}}
                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    @role('company')
                                        <th>{{ __('Employee Name') }}</th>
                                    @endrole
                                    <th>{{ __('Termination Type') }}</th>
                                    <th>{{ __('Notice Date') }}</th>
                                    <th>{{ __('Termination Date') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    @if (Gate::check('Edit Termination') || Gate::check('Delete Termination'))
                                        <th width="200px">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>


                                @foreach ($terminations as $termination)
                                    <tr>
                                        @role('company')
                                            <td>{{ !empty($termination->employee_id) ? $termination->employee->name : '' }}
                                            </td>
                                        @endrole

                                        <td>{{ !empty($termination->termination_type) ? $termination->terminationType->name : '' }}
                                        </td>
                                        <td>{{ \Auth::user()->dateFormat($termination->notice_date) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($termination->termination_date) }}</td>
                                        <td>
                                                <div class="action-btn">
                                                    <a href="#" class="mx-3 btn btn-sm bg-warning align-items-center"
                                                        data-url="{{ route('termination.description', $termination->id) }}"
                                                        data-ajax-popup="true" data-bs-toggle="tooltip"
                                                        title="{{ __('Desciption') }}"
                                                        data-title="{{ __('Desciption') }}"><span class="text-white"><i
                                                            class="icon_desc fa fa-comment"></i></span></a>
                                                </div>
                                        </td>
                                        <td class="Action">
                                            @if (Gate::check('Edit Termination') || Gate::check('Delete Termination'))
                                                        @can('Edit Termination')
                                                            <div class="action-btn me-2">
                                                                <a href="#" class="mx-3 btn btn-sm bg-info align-items-center"
                                                                    data-size="lg"
                                                                    data-url="{{ URL::to('termination/' . $termination->id . '/edit') }}"
                                                                    data-ajax-popup="true" data-size="md"
                                                                    data-bs-toggle="tooltip" title=""
                                                                    data-title="{{ __('Edit Termination') }}"
                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                    <span class="text-white"><i class="ti ti-pencil"></i></span>
                                                                </a>
                                                            </div>
                                                        @endcan

                                                        @can('Delete Termination')
                                                            <div class="action-btn">
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['termination.destroy', $termination->id],
                                                                    'id' => 'delete-form-' . $termination->id,
                                                                ]) !!}
                                                                <a href="#"
                                                                data-bs-trigger="hover"
                                                                    class="btn btn-sm bg-danger align-items-center bs-pass-para"
                                                                    data-bs-toggle="tooltip" title=""
                                                                    data-bs-original-title="Delete" aria-label="Delete"><span
                                                                        class="text-white"><i
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
