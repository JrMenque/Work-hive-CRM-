@extends('layouts.admin')
@push('script-page')
    <script>
        $(document).on('click', '.code', function() {
            var type = $(this).val();
            if (type == 'manual') {
                $('#manual').removeClass('d-none');
                $('#manual').addClass('d-block');
                $('#auto').removeClass('d-block');
                $('#auto').addClass('d-none');
            } else {
                $('#auto').removeClass('d-none');
                $('#auto').addClass('d-block');
                $('#manual').removeClass('d-block');
                $('#manual').addClass('d-none');
            }
        });

        $(document).on('click', '#code-generate', function() {
            var length = 10;
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            $('#auto-code').val(result);
        });
    </script>
@endpush
@section('page-title')
    {{ __('Manage Coupon') }}
@endsection

@section('action-button')
    @can('create coupon')
        <a href="#" data-url="{{ route('coupons.create') }}" data-ajax-popup="true" data-title="{{ __('Create New Coupon') }}"
            data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Coupon List') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    {{-- <h5></h5> --}}
                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">

                            <thead>
                                <tr>
                                    <th> {{ __('Name') }}</th>
                                    <th> {{ __('Code') }}</th>
                                    <th> {{ __('Discount (%)') }}</th>
                                    <th> {{ __('Limit') }}</th>
                                    <th> {{ __('Used') }}</th>
                                    <th width="200px"> {{ __('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($coupons as $coupon)
                                    <tr>
                                        <td>{{ $coupon->name }}</td>
                                        <td>{{ $coupon->code }}</td>
                                        <td>{{ $coupon->discount }}</td>
                                        <td>{{ $coupon->limit }}</td>
                                        <td>{{ $coupon->used_coupon() }}</td>
                                        <td class="Action">

                                            <div class="action-btn me-2">
                                                <a href="{{ route('coupons.show', $coupon->id) }}"
                                                    class="mx-3 btn btn-sm bg-warning align-items-center"
                                                    data-bs-toggle="tooltip" title=""
                                                    data-bs-original-title="{{ __('View') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>

                                            @can('edit coupon')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 btn btn-sm bg-info align-items-center"
                                                        data-url="{{ route('coupons.edit', $coupon->id) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Edit Coupon') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Edit') }}">
                                                        <span class="text-white"><i class="ti ti-pencil"></i></span>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete coupon')
                                                <div class="action-btn">
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['coupons.destroy', $coupon->id],
                                                        'id' => 'delete-form-' . $coupon->id,
                                                    ]) !!}
                                                    <a href="#!" data-bs-trigger="hover"  class=" btn btn-sm bg-danger align-items-center bs-pass-para"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Delete') }}">
                                                        <span class="text-white"><i class="ti ti-trash"></i></span></a>
                                                    {!! Form::close() !!}
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
