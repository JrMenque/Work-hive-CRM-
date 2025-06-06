@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Document Type') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Document Type') }}</li>
@endsection

@section('action-button')
    {{-- @can('Create Document Type')
        <a href="#" data-url="{{ route('document.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create New  Document Type') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
            data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endcan --}}
@endsection

@section('content')
<div class="row">
        {{-- <div class="col-3">
            @include('layouts.hrm_setup')
        </div> --}}
        <div class="col-12">
            @include('layouts.hrm_setup')
        </div>
        <div class="col-12">
            <div class="my-3 d-flex justify-content-end">
                @can('Create Document Type')
                    <a href="#" data-url="{{ route('document.create') }}" data-ajax-popup="true"
                        data-title="{{ __('Create New  Document Type') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
                        data-bs-original-title="{{ __('Create') }}">
                        <i class="ti ti-plus"></i>
                    </a>
                @endcan
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body table-border-style">

                            <div class="table-responsive">
                            <table class="table" id="pc-dt-simple">
                                <thead>
                                    <tr>
                                        <th>{{ __('Document') }}</th>
                                        <th>{{ __('Required Field') }}</th>
                                        @if (Gate::check('Edit Document Type') || Gate::check('Delete Document Type'))
                                            <th width="200px">{{ __('Action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents as $document)
                                        <tr>
                                            <td>{{ $document->name }}</td>
                                            <td>
                                                <h6 class="float-left mr-1">
                                                    @if ($document->is_required == 1)
                                                        <div class="badge bg-success p-2 px-3  status-badge7">{{ __('Required') }}</div>
                                                    @else
                                                        <div class="badge bg-danger p-2 px-3  status-badge7">{{ __('Not Required') }}
                                                        </div>
                                                    @endif
                                                </h6>
                                            </td>
                                            <td class="Action">
                                                <div class="dt-buttons">
                                                    <span class="float-start">
                                                        @can('Edit Document Type')
                                                            <div class="action-btn me-2">
                                                                <a href="#" class="btn btn-sm align-items-center bg-info"
                                                                    data-url="{{ route('document.edit', $document->id) }}"
                                                                    data-ajax-popup="true" data-title="{{ __('Edit Document Type') }}"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    data-original-title="{{ __('Edit') }}">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete Document Type')
                                                            <div class="action-btn ">
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['document.destroy', $document->id],
                                                                    'id' => 'delete-form-' . $document->id,
                                                                    ]) !!}
                                                                    <a href="#"
                                                                    data-bs-trigger="hover" 
                                                                        class="btn btn-sm  align-items-center bs-pass-para bg-danger"
                                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endcan
                                                    </span>
                                                </div>
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
        </div>
</div>
@endsection

