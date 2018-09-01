
    <div class="uk-vertical-align-middle uk-card">
        <h1 class="uk-heading-primary">Install DB HomeKit</h1>
        <form method="POST" class="uk-form-horizontal uk-margin-large" action="index.php">
            <input type="hidden" name="action" value="install">
            <fieldset class="uk-fieldset">
                <legend class="uk-legend">DB Homekit manager</legend>
                <div class="uk-margin">
                    <label class="uk-form-label"  for="username">Username you want to use</label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-width-medium" type="text" id="username" name="username" placeholder="Username">
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="password">Password you want to use</label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-width-medium" id="password" type="password" name="password" placeholder="Password">
                    </div>
                </div>
            </fieldset>
            <fieldset class="uk-fieldset">
                <legend class="uk-legend">HomeWizard details</legend>
                <div class="uk-margin">
                    <label class="uk-form-label"  for="h_username">HomeWizard username (useally email)</label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-width-medium" type="text" id="h_username" name="h_username" placeholder="Username">
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="h_password">HomeWizard password</label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-width-medium" id="h_password" type="password" name="h_password" placeholder="Password">
                    </div>
                </div>
            </fieldset>
            <div class="uk-margin">
                <input class="uk-input uk-form-width-large uk-button uk-button-primary" type="submit" value="Save">
            </div>
        </form>
    </div>
