@extends('templates.main')
@section('title', 'Sign Up')

@section('content')
<body class="signup" ng-controller="signUpController" ng-strict-di ng-cloak>

    @include('templates.header')

    <div class="content-wrapper single-page">
        <h1>Join SpaceX Stats</h1>
        <main>
            <p class="exclaim">Mission Control is currently in private beta and will be releasing in early 2016.</p>
            <div class="gr-6@xlarge gr-12@medium gr-centered" ng-hide="hasSignedUp">
                <form name="signUpForm" novalidate>
                    {!! csrf_field() !!}
                    <ul>
                        <li>
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username"
                                   ng-model="user.username" ng-minlength="4" ng-model-options="{ debounce: 500 }"
                                   unique-username required placeholder="4 characters or more" />
                            <p>
                                <span class="red" ng-show="signUpForm.username.$error.username" ng-animate="{ show: 'username-checker-visible', hide: 'username-checker-hidden'}">
                                    <i class="fa fa-times"></i> That username is taken.
                                </span>
                                <span ng-show="signUpForm.username.$pending.username" ng-animate="{ show: 'username-checker-visible', hide: 'username-checker-hidden'}">
                                    <i class="fa fa-circle-o-notch fa-spin"></i> Checking...
                                </span>
                                <span class="green" ng-show="signUpForm.username.$valid" ng-animate="{ show: 'username-checker-visible', hide: 'username-checker-hidden'}">
                                    <i class="fa fa-check"></i> Good to go!
                                </span>
                            </p>
                        </li>
                        <li>
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" ng-model="user.email" required />
                        </li>
                        <li>
                            <label for="password">Password</label>
                            <input type="password" password-toggle ng-model="user.password" ng-minlength="6"
                                   placeholder="6 characters or more" required />
                        </li>
                        <li>
                            <input type="checkbox" name="eula" id="eula" value="true" ng-model="user.eula" required>
                            <label for="eula"><span>I have read & agree to the <a target="_blank" href="/about/rulesandtermsofservice">terms of service</a></span></label>
                        </li>
                        <li>
                            <input type="submit" value="@{{ signUpButtonText }}" ng-disabled="signUpForm.$invalid || signUpForm.$pending || isSigningUp" ng-click="signUp()">
                        </li>
                        <li>
                            <p class="lowvisibility opacity">Already have an account? <a href="/auth/login">Log in</a>.</p>
                        </li>
                    </ul>
                </form>
            </div>
            <div ng-show="hasSignedUp">
                <p class="exclaim">Thanks for signing up!</p>
                <p>You can activate your account by clicking the confirmation link in the email we just sent you.</p>
            </div>

        </main>
    </div>
</body>
@stop