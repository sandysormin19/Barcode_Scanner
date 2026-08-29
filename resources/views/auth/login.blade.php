<x-guest-layout>

<div class="login-box" style="width:420px">

    <div class="card card-outline card-primary shadow-lg">

        <div class="card-header text-center py-4">

            <div class="mb-2">
                <i class="fas fa-warehouse fa-3x text-primary"></i>
            </div>

            <h2 class="font-weight-bold mb-0">
                WMS
            </h2>

            <p class="font-weight-bold mb-0">
                    Roputex Management System
            </p>

        </div>


        <div class="card-body p-4">


            <p class="login-box-msg">
                Silahkan login untuk mengakses sistem
            </p>



            @if(session('status'))

                <div class="alert alert-success">
                    {{ session('status') }}
                </div>

            @endif



            <form method="POST" action="{{ route('login') }}">

                @csrf


                <div class="input-group mb-3">

                    <input
                        type="email"
                        name="email"
                        class="form-control form-control-lg"
                        placeholder="Email"
                        value="{{ old('email') }}"
                        required
                        autofocus>


                    <div class="input-group-append">

                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>

                    </div>

                </div>


                @error('email')

                <small class="text-danger">
                    {{ $message }}
                </small>

                @enderror




                <div class="input-group mb-3">


                    <input
                        type="password"
                        name="password"
                        class="form-control form-control-lg"
                        placeholder="Password"
                        required>


                    <div class="input-group-append">

                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>

                    </div>


                </div>


                @error('password')

                <small class="text-danger">
                    {{ $message }}
                </small>

                @enderror





                <div class="row align-items-center mt-4">


                    <div class="col-7">

                        <div class="icheck-primary">

                            <input
                                type="checkbox"
                                name="remember"
                                id="remember">


                            <label for="remember">

                                Ingat saya

                            </label>

                        </div>

                    </div>



                    <div class="col-5">

                        <button
                            type="submit"
                            class="btn btn-primary btn-block btn-lg">

                            <i class="fas fa-sign-in-alt mr-1"></i>
                            Login

                        </button>

                    </div>


                </div>


            </form>



            <hr>


            <div class="text-center text-muted">

                <small>
                    © {{ date('Y') }} Roputex Management System
                </small>

            </div>


        </div>


    </div>


</div>


</x-guest-layout>
