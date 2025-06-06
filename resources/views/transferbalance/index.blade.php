@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Transfer Balance') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Transfer Balance') }}</li>
@endsection

@section('action-button')
    <a href="{{ route('transfer_balance.export') }}" class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip"
        data-bs-original-title="{{ __('Export') }}">
        <i class="ti ti-file-export"></i>
    </a>

    @can('Create Transfer Balance')
        <a href="#" data-url="{{ route('transferbalance.create') }}" data-ajax-popup="true" data-size="lg"
            data-title="{{ __('Create New Transfer Balance') }}" data-bs-toggle="tooltip" title=""
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
                                <th>{{ __('From Account') }}</th>
                                <th>{{ __('To Account') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Payment Method') }}</th>
                                <th>{{ __('Ref#') }}</th>
                                <th width="200px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($transferbalances as $transferbalance)
                                <tr>
                                    <td>{{ !empty($transferbalance->account($transferbalance->from_account_id)) ? $transferbalance->account($transferbalance->from_account_id)->account_name : '' }}
                                    </td>
                                    <td>{{ !empty($transferbalance->account($transferbalance->to_account_id)) ? $transferbalance->account($transferbalance->to_account_id)->account_name : '' }}
                                    </td>
                                    <td>{{ \Auth::user()->dateFormat($transferbalance->date) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($transferbalance->amount) }}</td>
                                    <td>{{ !empty($transferbalance->payment_type_id) ? $transferbalance->payment_types->name : '' }}
                                    </td>
                                    <td>{{ $transferbalance->referal_id }}</td>
                                    <td class="Action">
                                                @can('Edit Transfer Balance')
                                                    <div class="action-btn me-2">
                                                        <a href="#" class="mx-3 btn btn-sm bg-info align-items-center" data-size="lg"
                                                            data-url="{{ URL::to('transferbalance/' . $transferbalance->id . '/edit') }}"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title=""
                                                            data-title="{{ __('Edit Transfer Balance') }}"
                                                            data-bs-original-title="{{ __('Edit') }}">
                                                            <span class="text-white"><i class="ti ti-pencil"></i></span>
                                                        </a>
                                                    </div>
                                                @endcan

                                                @can('Delete Transfer Balance')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['transferbalance.destroy', $transferbalance->id], 'id' => 'delete-form-' . $transferbalance->id]) !!}
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
@push('scripts')
<script>
    $(document).on('change', '#from_account', function() {
        var account_id = $(this).val();
        getAccounts(account_id);
    });
    function getAccounts(account_id) {
        var data = {
            "account_id": account_id,
            "_token": "{{ csrf_token() }}",
        }
        $.ajax({
            url: '{{ route('transferbalance.getaccounts') }}',
            method: 'POST',
            data: data,
            success: function(data) {
                 console.log(data);
                $('#to_account').empty();
                $('#to_account').append(
                    '<option value="" disabled>{{ __('Select Account') }}</option>');
                $.each(data, function(key, value) {
                    $('#to_account').append('<option value="' + key + '">' + value +
                        '</option>');
                });
                $('#to_account').val('');
            }
        });
    }
</script>
@endpush
