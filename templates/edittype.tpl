
    <div class="uk-vertical-align-middle uk-card">
        <h1 class="uk-heading-primary">Edit a type</h1>
        <form method="POST" class="uk-form-horizontal uk-margin-large" action="index.php">
            <input type="hidden" name="action" value="edittype">
            <input type="hidden" name="oldtype" value="%%typename%%">
            <fieldset class="uk-fieldset">
                <legend class="uk-legend">DB Homekit types</legend>
                <div class="uk-margin">
                    <label class="uk-form-label"  for="typename">Type name</label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-width-medium" type="text" id="typename" name="typename" value="%%typename%%" placeholder="Name for a type">
                    </div>
                </div>
            </fieldset>
            <div class="uk-margin">
                <input class="uk-input uk-form-width-large uk-button uk-button-primary" type="submit" value="Save">
            </div>
        </form>
    </div>
