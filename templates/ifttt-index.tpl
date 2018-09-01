<div class="uk-vertical-align-middle uk-card">
    <h1 class="uk-heading-line uk-padding-large"><span>Creating IFTTT Applets</span></h1>
    <div class="uk-card uk-card-default uk-body uk-padding">
        <p>This wizard will guide you through making IFTTT applets to control your switches using Google's Assistant or any other WEB hook function.</p>
        <p>More variations are possible then shown here, but the result will help you to create different variations on your own. See it as a good starting point or guideline.</p>
        <h4>What do you want to turn on/off ?</h4>
        <p>
            <form method="post">
                <input type="hidden" name="action" value="iftttwizard1">
                <select name="what" class="uk-select">
                    <option value="device">1 particular device</option>
                    <option value="room">All devices of a certain type in a room</option>
                    <option value="room">All devices of a certain type</option>
                </select>
                <input class="uk-button uk-button-primary uk-width-1-1 uk-margin-top" type="submit" value="next">
            </form>
        </p>
    </div>
</div>
