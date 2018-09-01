
    <div class="uk-vertical-align-middle uk-card">
        <h1 class="uk-heading-primary">Create a room</h1>
        <form method="POST" class="uk-form-horizontal uk-margin-large" action="index.php">
            <input type="hidden" name="action" value="createroom">
            <fieldset class="uk-fieldset">
                <legend class="uk-legend">DB Homekit rooms</legend>
                <div class="uk-margin">
                    <label class="uk-form-label"  for="roomname">Room name</label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-width-medium" type="text" id="roomname" name="roomname" placeholder="Name for a room">
                    </div>
                </div>
            </fieldset>
            <div class="uk-margin">
                <input class="uk-input uk-form-width-large uk-button uk-button-primary" type="submit" value="Save">
            </div>
        </form>
    </div>
