<div class="uk-vertical-align-middle uk-card">
    <h1 class="uk-heading-line uk-padding-large"><span>Creating IFTTT Applets</span></h1>
    <div class="uk-card uk-card-default uk-body uk-padding">
        <h2>Turn on/off one particular device</h2>
        <h4>Choose your device</h4>
        <p>
            <form method="post">
                <input type="hidden" name="action" value="iftttwizard2">
                <input type="hidden" name="ifttt-type" value="1">
                <select name="ifttt-chosen" class="uk-select">
                    %%list%%
                </select>
                <input class="uk-button uk-button-primary uk-width-1-1 uk-margin-top" type="submit" value="next">
            </form>
        </p>
    </div>
</div>
