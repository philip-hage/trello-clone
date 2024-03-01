<form class="login-form container max-width-sm margin-y-xxl">
    <div class="text-component text-center margin-bottom-sm">
        <h1>Log in</h1>
    </div>

    <div class="margin-bottom-sm">
        <label class="form-label margin-bottom-xxxs" for="input-email">Email</label>
        <input class="form-control width-100%" type="email" name="input-email" id="input-email" placeholder="email@myemail.com">
    </div>

    <div class="margin-bottom-sm">
        <div class="flex justify-between margin-bottom-xxxs">
            <label class="form-label" for="input-password">Password</label>
        </div>

        <input class="form-control width-100%" type="password" name="input-password" id="input-password">
    </div>

    <div class="margin-bottom-sm">
        <button onclick="loginForm(event)" class="btn btn--primary btn--md width-100%">Login</button>
    </div>

    <div class="text-center">
        <p class="text-sm">Don't have an account? <a href="<?= $system['address'] ?>auth/register">Get started</a></p>
    </div>
</form>

