@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Account') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Account') }}</li>
@endsection

@section('action-button')
    @can('Create Account List')
        <a href="#" data-url="{{ route('accountlist.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create New Account') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
            data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                {{-- <h5> </h5> home--}}
                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>{{ __('Account Name') }}</th>
                                <th>{{ __('Initial Balance') }}</th>
                                <th>{{ __('Account Number') }}</th>
                                <th>{{ __('Branch Code') }}</th>
                                <th>{{ __('Bank Branch') }}</th>
                                <th width="200px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accountlists as $accountlist)
                                <tr>
                                    <td>{{ $accountlist->account_name }}</td>
                                    <td>{{ \Auth::user()->priceFormat($accountlist->initial_balance) }}</td>
                                    <td>{{ $accountlist->account_number }}</td>
                                    <td>{{ $accountlist->branch_code }}</td>
                                    <td>{{ $accountlist->bank_branch }}</td>
                                    <td class="Action">
                                                @can('Edit Account List')
                                                    <div class="action-btn me-2">
                                                        <a href="#" class="mx-3 btn btn-sm bg-info align-items-center"
                                                            data-url="{{ URL::to('accountlist/' . $accountlist->id . '/edit') }}"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title=""
                                                            data-title="{{ __('Edit Account List') }}"
                                                            data-bs-original-title="{{ __('Edit') }}">
                                                            <span class="text-white"><i class="ti ti-pencil"></i></span>
                                                        </a>
                                                    </div>
                                                @endcan

                                                @can('Delete Account List')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['accountlist.destroy', $accountlist->id], 'id' => 'delete-form-' . $accountlist->id]) !!}
                                                        <a href="#"  data-bs-trigger="hover" class="btn btn-sm bg-danger align-items-center bs-pass-para"
                                                            data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                                                            aria-label="Delete"><span class="text-white"><i
                                                                class="ti ti-trash"></i></span></a>
                                                        </form>
                                                    </div>
                                                @endcan
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
