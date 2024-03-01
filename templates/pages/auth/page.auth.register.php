<form class="sign-up-form container max-width-sm margin-y-xxl" id="signup-form">
    <div class="text-component text-center margin-bottom-sm">
        <h1>Sign Up</h1>
        <p> Already have an account? <a href="<?= $system['address'] ?>auth/login">Login</a></p>
    </div>
    <div class="margin-bottom-sm">
        <label class="form-label margin-bottom-xxxs" for="input-name">Name</label>
        <input class="form-control width-100%" type="text" name="input-name" id="input-name">
    </div>

    <div class="margin-bottom-sm">
        <label class="form-label margin-bottom-xxxs" for="input-email">Email</label>
        <input class="form-control width-100%" type="email" name="input-email" id="input-email" placeholder="email@myemail.com">
    </div>

    <div class="margin-bottom-md">
        <label class="form-label margin-bottom-xxxs" for="input-password">Password</label>
        <input class="form-control width-100%" type="password" name="input-password" id="input-password">
    </div>

    <div class="margin-bottom-sm">
        <button class="btn btn--primary btn--md width-100%" onclick="registerSubmit(event)">Sign Up</button>
    </div>

</form>