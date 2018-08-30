@extends('layouts.auth')

@section('content')
<!-- Simple login form -->
<form action="{{ url('login') }}" method="post" id="login" @submit.prevent="login($event)" autocomplete="off">
    {{ csrf_field() }}
    <div class="panel panel-body login-form">
        <div class="text-center">
            <div class="icon-object border-slate-300 text-slate-300"><i class="icon-reading"></i></div>
            <h5 class="content-group">@lang('trans.login')</h5>
        </div>

        <div class="alert alert-bordered pt-5 pb-5 pl-10 pr-10" :class="{'alert-success': statusLogin === 2, 'alert-danger': statusLogin === 1}" v-if="statusLogin > 0" v-cloak>
            <span v-if="statusLogin === 2">@lang('trans.login_success')</span>
            <span v-if="statusLogin === 1">@lang('trans.login_failed')</span>
        </div>

        <div class="form-group has-feedback has-feedback-left">
            <input type="text" class="form-control" placeholder="@lang('trans.username')" name="name">
            <div class="form-control-feedback">
                <i class="icon-user text-muted"></i>
            </div>
            <label class="validation-error-label" v-if="errors.username_required">@lang('trans.username_required')</label>
        </div>

        <div class="form-group has-feedback has-feedback-left">
            <input type="password" class="form-control" placeholder="@lang('trans.password')" name="password">
            <div class="form-control-feedback">
                <i class="icon-lock2 text-muted"></i>
            </div>
            <label class="validation-error-label" v-if="errors.password_required">@lang('trans.password_required')</label>
        </div>

        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" class="styled" name="remember">@lang('trans.remember')
                </label>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">@lang('trans.login') <i class="icon-circle-right2 position-right"></i></button>
        </div>

        <div class="text-center">
            <a href="login_password_recover.html">@lang('trans.forgot_password')</a>
        </div>
    </div>
</form>
<!-- /simple login form -->
@endsection

@push('vue')
<script type="text/javascript" src="{{ asset('js/login.js') }}"></script>
@endpush
