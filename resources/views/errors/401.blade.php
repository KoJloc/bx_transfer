@extends('errors.illustrated-layout')

@section('code', '401')
@section('title', __('Unauthenticated'))

@section('image')
    <div style="background-size: contain; background-image: url('{{ asset('storage/app/public/401.png') }}');" class="absolute pin bg-no-repeat md:bg-left lg:bg-center"></div>
@endsection

@section('message', __('UNAUTHORIZED'))

@section('description', __('Вы не авторизованы в приложении, необходимо запросить доступ у администратора!'))