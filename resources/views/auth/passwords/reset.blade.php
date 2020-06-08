@component('layouts.app')
    @slot('title') Reset password @endslot

    <div class="md:bg-white container md:mx-4 md:mx-auto md:shadow-lg rounded md:mt-32 flex md:flex-row flex-col md:max-w-2xl lg:max-w-4xl justify-between">
        <div class="md:w-1/2 text-center md:flex flex-col items-center px-8 py-4">
            <img src="/image/RET-MT01-logos-01.jpg" alt="Admuni Logo" class="pt-8 hidden md:block">
            <p class="text-indigo-500 font-bold text-4xl md:pt-8">AdMuni</p>
            <p class="text-indigo-500 text-xl"> Enfoque sensible al trabajo y colaboración para desarrollo local </p>
        </div>
        <div class=""></div>
        <div class="w-full md:bg-indigo-100 md:w-1/2 p-8">
            <h3 class="text-gray-700 font-medium">@lang('auth.Reset Password')</h3>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <form role="form" method="POST" action="{{ url('/password/reset') }}">
                {{ csrf_field() }}

                <input type="hidden" name="token" value="{{ $token }}">

                <p class="py-4">
                    <input id="email" type="email" name="email" placeholder="E-mail" value="{{ $email ?? old('email') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"  required>
                    @if ($errors->has('email'))
                        <span class="text-red-light block pt-5" style="font-size: 14px;">{{ $errors->first('email') }}</span>
                    @endif
                </p>
                <p class="py-4">
                    <input id="password" type="password" name="password" placeholder="Password" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"  required>
                    @if ($errors->has('password'))
                        <span class="text-red-light block pt-5" style="font-size: 14px;">{{ $errors->first('password') }}</span>
                    @endif
                </p>
                <p class="py-4">
                    <input id="password-confirm" type="password" name="password_confirmation" placeholder="Password Confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker"  required>
                    @if ($errors->has('password_confirmation'))
                        <span class="text-red-light block pt-5" style="font-size: 14px;">{{ $errors->first('password_confirmation') }}</span>
                    @endif
                </p>
                <p class="py-4">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">@lang('auth.Reset Password')</button>
                </p>
            </form>
        </div>
    </div>

    @slot('script') @endslot
@endcomponent
